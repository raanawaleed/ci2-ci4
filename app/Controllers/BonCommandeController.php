<?php

namespace App\Controllers;

//use App\Models\CommandeModel;
use App\Models\SettingModel;
use App\Models\CompanyModel;

use App\Controllers\BaseController;

class BonCommandeController extends BaseController
{
    //protected CommandeModel $commandeModel;
    protected $view_data = [];
    protected SettingModel $settingModel;
    protected CompanyModel $companyModel;

    public function __construct()
    {
        $this->settingModel = new SettingModel();
        $this->companyModel = new CompanyModel();
       // $this->commandeModel = new CommandeModel(); // Uncomment when the model is available

        $this->checkAccess();
        $this->view_data['submenu'] = [
            $this->lang->line('application_all') => 'estimates',
            $this->lang->line('application_open') => 'estimates/filter/open',
            $this->lang->line('application_Sent') => 'estimates/filter/sent',
            $this->lang->line('application_Accepted') => 'estimates/filter/accepted',
            $this->lang->line('application_Invoiced') => 'estimates/filter/invoiced',
        ];
    }

    private function checkAccess(): void
    {
        if ($this->client) {
            return redirect('login');
        } elseif ($this->user) {
            $access = false;
            foreach ($this->view_data['submenuRight'] as $value) {
                if ($value->link === "boncommande") {
                    $access = true;
                }
            }
            // Uncomment if you need to enforce access
            // if (!$access) { return redirect('login'); }
        } else {
            return redirect('login');
        }
    }
    public function index()
    {
        $this->view_data['commande'] = $this->commandeModel->where('id_vcompanies', $_SESSION['current_company'])->findAll();
        $this->content_view = "boncommande/all";
    }

    public function create()
    {
        if ($this->request->getMethod() === 'post') {
            $data = $this->request->getPost();
            unset($data['send'], $data['_wysihtml5_mode'], $data['files']);
            $data['id_vcompanies'] = $_SESSION['current_company'];

            if ($this->commandeModel->insert($data)) {
                $this->updateEstimateReference($data['estimate_reference']);
                $this->session->setFlashdata('message', 'success:' . $this->lang->line('messages_create_commande_success'));
            } else {
                $this->session->setFlashdata('message', 'error:' . $this->lang->line('messages_create_commande_error'));
            }
            return redirect('boncommande');
        }

        $this->view_data['companies'] = $this->companyModel->where('inactive', 0)->findAll();
        $this->view_data['title'] = $this->lang->line('application_create_commande');
        $this->view_data['current_date'] = date('Y-m-d');
        $this->view_data['form_action'] = 'boncommande/create';
        $this->content_view = 'boncommande/_commande';
    }

    private function updateEstimateReference(int $currentReference): void
    {
        $newReference = $currentReference + 1;
        $setting = $this->settingModel->where('id_vcompanies', $_SESSION['current_company'])->first();
        if ($setting) {
            $setting->estimate_reference = $newReference;
            $this->settingModel->save($setting);
        }
    }

    public function update(int $id)
    {
        if ($this->request->getMethod() === 'post') {
            $data = $this->request->getPost();
            unset($data['send'], $data['_wysihtml5_mode'], $data['files'], $data['id']);

            if ($this->commandeModel->update($id, $data)) {
                $this->session->setFlashdata('message', 'success:' . $this->lang->line('messages_update_commande_success'));
            } else {
                $this->session->setFlashdata('message', 'error:' . $this->lang->line('messages_update_commande_error'));
            }
            return redirect('boncommande');
        }

        $this->view_data['companies'] = $this->companyModel->where('inactive', 0)->findAll();
        $this->view_data['title'] = $this->lang->line('application_create_commande');
        $this->view_data['current_date'] = date('Y-m-d');
        $this->view_data['commande'] = $this->commandeModel->find($id);
        $this->view_data['form_action'] = 'boncommande/update/' . $id;
        $this->content_view = 'boncommande/_commande';
    }

    public function delete(int $id)
    {
        if ($this->request->getMethod() === 'post') {
            if ($this->commandeModel->delete($id)) {
                $this->session->setFlashdata('message', 'success:' . $this->lang->line('messages_delete_commande_success'));
            } else {
                $this->session->setFlashdata('message', 'error:' . $this->lang->line('messages_delete_commande_error'));
            }
            return redirect('boncommande');
        }
    }
}