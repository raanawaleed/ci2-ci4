<?php

namespace App\Controllers;

use App\Models\RefTypeOccurencesModel;
use App\Models\SalarieModel;

use App\Controllers\BaseController;

class DemandeCongeController extends BaseController
{
    protected $referentiels;
    protected $salarieModel;
    public function __construct()
    {
        $this->referentiels = new RefTypeOccurencesModel();
        $this->salarieModel = new SalarieModel();
        session_start();

        if (!session()->has('client') && !session()->has('user')) {
            return redirect()->to('login');
        }
    }


    public function index()
    {
        $userId = session()->get('user')->id;
        $user_name = $this->salarieModel->getUserInfo($userId);

        $solde = $user_name->droit_conge;
        $salarie_id = $user_name->id;

        $conges = $salarie_id ? $this->salarieModel->getConges($salarie_id) : [];

        $data = [
            'conges' => $conges,
            'solde' => $solde,
            'user' => "{$user_name->nom} {$user_name->prenom}",
            'motif' => $this->referentiels->getReferentielsByIdType(config("app.type_id_motif_absence")),
            'statut' => $this->referentiels->getReferentielsByIdType(config("app.type_id_statut_conges")),
        ];

        return view("rhpaie/all_for_user", $data);
    }


    /**
     * Création d'un congés
     */
    function create2()
    {

        //get user
        $user = $this->user->id;
        $sql = "SELECT salaries.id,salaries.nom , salaries.prenom ,salaries.mail,salaries.droit_conge  FROM salaries
       join users on salaries.id = users.salaries_id 
       WHERE(users.id = $user)";
        $user_name = $this->db->query($sql)->result()[0];


        $salarie_id = $user_name->id;



        $this->view_data['user'] = $user_name->nom . ' ' . $user_name->prenom;

        $this->view_data['id'] = $salarie_id;


        if ($_POST) {
            unset($_POST['send']);
            $_POST['id_salarie'] = $salarie_id;
            $_POST['date_debut'] = ($_POST['date_debut']);
            $_POST['motif'] = $_POST['motif'];
            $_POST['statut'] = "162";
            $_POST['date_fin'] = $_POST['date_fin'];
            $this->db->select('mail,nom , prenom');
            $this->db->from('salaries');
            $this->db->where('id', $salarie_id);
            $email = $this->db->get()->result()[0];
            //liste email parametre



            $this->db->select('email_notification');
            $this->db->from('core');
            $this->db->where('id', '2');
            $liste = $this->db->get()->result()[0];

            $pieces = explode(";", $liste->email_notification);
            $rowns = implode(',', $pieces);
            //$motif = get_texte_occurence($_POST['motif']);
            $motif = $this->referentiels->getOccNameById($_POST['motif']);
            //var_dump($motif->name);exit;
            $this->load->library('email');


            $this->email->from($email->mail, $email->nom . ' ' . $email->prenom);
            $this->email->to($rowns);
            $this->email->subject('Demande de congés Du ' . $_POST['date_debut'] . ' au ' . $_POST['date_fin']);
            $this->email->message('Bonjour ,' . '<br>' . '<br>' . 'Une nouvelle demande de congés est envoyée par ' . $email->nom . ' ' . $email->prenom . '<br>' . $motif->name . ' du ' . $_POST['date_debut'] . ' au ' . $_POST['date_fin'] . '<br>' . 'Veuillez consulter la liste des demandes en attente .' . '<br>' . '<br>' . 'Cordialement ,');
            if ($this->email->send()) {
                $this->session->set_flashdata('message', 'success:' . $this->lang->line('messages_email_success'));
            } else {
                show_error($this->email->print_debugger());

            }



            if (!$this->db->insert('t_pasa_conges', $_POST))
                show_error("L'enregistrement des congés a échoué.", "404", $heading = 'Une erreur a été rencontrée');
            redirect('demandeConge');
        } else {


            $this->view_data['motif'] = $this->referentiels->getReferentielsByIdType($this->config->item("type_id_motif_absence"));
            $this->view_data['statut'] = $this->referentiels->getReferentielsByIdType($this->config->item("type_id_statut_conges"));
            $this->view_data['salaries'] = Salaries::find('all');

            $this->theme_view = 'modal';
            $this->view_data['title'] = $this->lang->line('application_conge_info');
            $this->view_data['form_action'] = 'demandeConge/create2';
            $this->content_view = 'rhpaie/addconge';



        }


    }

    public function create()
    {
        $userId = session()->get('user')->id;
        $user_name = $this->salarieModel->getUserInfo($userId);
        $salarie_id = $user_name->id;

        $data = [
            'user' => "{$user_name->nom} {$user_name->prenom}",
            'id' => $salarie_id,
            'motif' => $this->referentiels->getReferentielsByIdType(config("app.type_id_motif_absence")),
            'statut' => $this->referentiels->getReferentielsByIdType(config("app.type_id_statut_conges")),
            'salaries' => $this->salarieModel->findAll(),
        ];

        if ($this->request->getMethod() === 'post') {
            $this->handlePostCreate($salarie_id);
        } else {
            return view('rhpaie/addconge', $data);
        }
    }

    private function handlePostCreate(int $salarie_id)
    {
        $postData = $this->request->getPost();
        unset($postData['send']);
        $postData['id_salarie'] = $salarie_id;
        $postData['statut'] = "162";

        $email = $this->salarieModel->getEmail($salarie_id);
        $notificationEmails = $this->salarieModel->getNotificationEmails();

        $motif = $this->referentiels->getOccNameById($postData['motif']);
        $this->sendEmailNotification($email, $notificationEmails, $motif, $postData);

        if (!$this->salarieModel->insertConges($postData)) {
            throw new \RuntimeException("L'enregistrement des congés a échoué.");
        }

        return redirect()->to('demandeConge');
    }

    private function sendEmailNotification(object $email, string $to, object $motif, array $postData)
    {
        $emailService = \Config\Services::email();
        $emailService->setFrom($email->mail, "{$email->nom} {$email->prenom}");
        $emailService->setTo($to);
        $emailService->setSubject('Demande de congés Du ' . $postData['date_debut'] . ' au ' . $postData['date_fin']);
        $emailService->setMessage('Bonjour,<br><br>Une nouvelle demande de congés est envoyée par ' . "{$email->nom} {$email->prenom}<br>{$motif->name} du {$postData['date_debut']} au {$postData['date_fin']}<br>Veuillez consulter la liste des demandes en attente.<br><br>Cordialement,");

        if (!$emailService->send()) {
            throw new \RuntimeException($emailService->printDebugger());
        }
    }

    public function updatedemande(int $id)
    {
        if ($this->request->getMethod() === 'post') {
            $this->handlePostUpdate($id);
        } else {
            $this->showUpdateForm($id);
        }
    }

    private function handlePostUpdate(int $id)
    {
        $data = $this->request->getPost();
        unset($data['send']);

        if (!$this->salarieModel->updateConges($id, $data)) {
            session()->setFlashdata('message', 'error: ' . lang('messages_error_update_demande'));
        } else {
            session()->setFlashdata('message', 'success: ' . lang('messages_update_demande'));
        }

        return redirect()->to('demandeConge');
    }

    private function showUpdateForm(int $id)
    {
        $conge = $this->salarieModel->getCongesById($id);
        $data = [
            'conge' => $conge,
            'motif' => $this->referentiels->getReferentielsByIdType(config("app.type_id_motif_absence")),
        ];

        return view('rhpaie/updateconge', $data);
    }

    public function deletedemande(int $id)
    {
        if ($this->salarieModel->deleteConges($id)) {
            session()->setFlashdata('message', 'success: ' . lang('messages_delete_demande'));
        } else {
            session()->setFlashdata('message', 'error: ' . lang('messages_error_delete_demande'));
        }

        return redirect()->to('demandeConge');
    }
}