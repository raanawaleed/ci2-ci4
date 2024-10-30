<?php

namespace App\Controllers;

use App\Models\SalarieModel;
use App\Models\CongesModel;
use App\Models\RefTypeOccurencesModel;
use CodeIgniter\Email\Email;
use CodeIgniter\Controller;

class GestionCongeController extends Controller
{
    protected SalarieModel $salariesModel;
    protected CongesModel $congesModel;
    protected RefTypeOccurencesModel $referentielsModel;
    protected array $view_data = [];

    public function __construct()
    {
        // Load models
        $this->salariesModel = new SalarieModel();
        $this->congesModel = new CongesModel();
        $this->referentielsModel = new RefTypeOccurencesModel();


        // Check user authentication
        if (!session()->get('user')) {
            return redirect()->to('login');
        }
    }

    public function index()
    {
        // Fetch all salaries and conges
        $this->view_data['salaries'] = $this->salariesModel->findAll();
        $this->view_data['conges'] = $this->filterCongesByStatut($this->request->getGet('statut'));

        // Fetch reference data
        $this->view_data['motif'] = $this->referentielsModel->getReferentielsByIdType(config('App')->type_id_motif_absence);
        $this->view_data['statut'] = $this->referentielsModel->getReferentielsByIdType(config('App')->type_id_statut_conges);

        return view("blueline/rhpaie/all");
    }

    private function filterCongesByStatut(?string $statut): array
    {
        return match ($statut) {
            'enattent' => $this->referentielsModel->getattent(),
            'accepter' => $this->referentielsModel->getaccept(),
            default => $this->congesModel->findAll(),
        };
    }

    public function valider(int $id)
    {
        $conges = $this->congesModel->find($id);
        $user = $this->salariesModel->find($conges->id_salarie);
        $userName = "{$user->prenom} {$user->nom}";

        // Calcul du nombre de jours de congés demandés
        $nbJoursTimestamp = (strtotime($conges->date_fin) - strtotime($conges->date_debut)) / 86400;

        // Valider la demande de congés
        $conge = $this->congesModel->find($id);
        $conge->statut = '28'; // Update status to 'accepted'
        $conge->save();

        // Mise à jour du solde de congés
        $solde = $this->salariesModel->find($conge->id_salarie);
        $solde->droit_conge -= $nbJoursTimestamp;
        $solde->save();

        // Envoi du mail de confirmation
        $this->sendEmail($user->mail, $userName, $conges, $solde->droit_conge, true);

        return redirect()->to('gestionconge?statut=enattent');
    }

    public function refuser(int $id)
    {
        $conge = $this->congesModel->find($id);
        $salaries = $this->salariesModel->find($conge->id_salarie);

        if (!$salaries->mail) {
            return 'Ce salarié n\'a pas d\'adresse email valide dans sa fiche salariée.';
        }

        $conge->statut = '123'; // Update status to 'refused'
        $conge->save();

        // Envoi de l'email
        $this->sendEmail($salaries->mail, "{$salaries->prenom} {$salaries->nom}", $conge, null, false);

        return redirect()->to('gestionconge');
    }

    private function sendEmail(string $email, string $userName, $conge, ?int $remainingDays, bool $isAccepted): void
    {
        if ($email) {
            $emailService = new Email();
            $emailService->setFrom('t.chakroun@3click-solutions.com', 'RH BIMMAPPING')
                ->setTo($email)
                ->setSubject('Réponse de demande de congés');

            $message = $isAccepted
                ? "Bonjour $userName,<br><br>Votre demande pour la période allant du " . date('d-m-Y h:i', strtotime($conge->date_debut)) . " au " . date('d-m-Y h:i', strtotime($conge->date_fin)) . " est acceptée.<br><br>Votre solde restant est $remainingDays jours.<br><br>Cordialement,"
                : "Bonjour $userName,<br><br>Votre demande pour la période allant du " . date('d-m-Y h:i', strtotime($conge->date_debut)) . " au " . date('d-m-Y h:i', strtotime($conge->date_fin)) . " a été refusée.<br><br>Cordialement,";

            $emailService->setMessage($message);

            if (!$emailService->send()) {
                throw new \RuntimeException('Erreur lors de l\'envoi de l\'e-mail: ' . implode(', ', $emailService->getErrors()));
            }
        } else {
            throw new \InvalidArgumentException('Ce salarié n\'a pas d\'adresse email valide dans sa fiche salariée.');
        }
    }
}
