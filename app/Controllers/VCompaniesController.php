<?php

namespace App\Controllers;

use App\Controllers\BaseController;

use App\Models\VCompanyModel;
use App\Models\UserModel;
use App\Models\ModuleModel;
use App\Models\SubmenuModel;
use App\Models\AccesRigthModel;

class VCompaniesController extends BaseController
{
    public function __construct(){
    {

        $this->db = \Config\Database::connect(); // Initialize the database connection

        if (!isset($this->user)) {
            redirect()->to('login')->send(); // Redirect and send the response
            exit(); // Ensure script execution stops after redirection
        }

    }

    public function create(): void
    {
        if ($this->request->getMethod() === 'post') {
            $data = [
                'name' => service('request')->getPost('name'),
                'client_id' => $this->user->id,
                'reference' => 0,
                'zipcode' => 0,
                'picture' => 'no-pic.png'
            ];

            // Insert company data
            $this->db->table('v_companies')->insert($data);
            $maxid = $this->db->insertID();

            $dataSmptp = [
                'id_company' => $maxid,
                'priority' => "3",
                'validate' => true,
                'smtp_crypto' => "tls",
                'smtp_debug' => "0",
                'wordwrap' => true,
                'wrapchars' => "76",
                'bcc_batch_mode' => false,
                'bcc_batch_size' => "200"
            ];

            $this->db->table('smtp_conf')->insert($dataSmptp);

            // Insert core data
            $rowCore = $this->db->table('core')->select('version')->where('version IS NOT NULL', null, false)->get()->getRow();
            $dataCore = [
                'version' => $rowCore->version,
                'domain' => 'http://' . $_SERVER['HTTP_HOST'],
                'company_reference' => 1,
                'project_reference' => 1,
                'invoice_reference' => 1,
                'estimate_reference' => 1,
                'invoice_logo' => 'assets/blueline/img/invoice_logo.png',
                'estimate_pdf_template' => 'templates/estimate/blueline',
                'invoice_pdf_template' => 'templates/estimate/blueline',
                'jarvis_url' => "http://jarvis.vision-erp.com",
                'id_vcompanies' => $maxid,
                'commande_reference' => 1,
                'livraison_reference' => 1,
                'companies' => 0,
                'number_users' => 1
            ];

            $this->db->table('core')->insert($dataCore);

            // Create user access rights
            $userModel = new UserModel(); // Instantiate the mode
            $users = $userModel->where('admin', 1)->findAll();
            foreach ($users as $user) {
                $allModule = '';
                $allSubmenu = '';

                $moduleModel = new ModuleModel(); // Instantiate Module model
                $submenuModel = new SubmenuModel(); // Instantiate Submenu model

                $modules = $moduleModel->where('type !=', 'client')->orderBy('sort', 'asc')->findAll();
                $submenus = $submenuModel->where('type !=', 'client')->orderBy('sort', 'asc')->findAll();

                foreach ($modules as $val) {
                    $allModule .= $val->id . ',';
                }
                foreach ($submenus as $val) {
                    $allSubmenu .= $val->id . ',';
                }

                $dataAccess = [
                    'user_id' => $user->id,
                    'company_id' => $maxid,
                    'menu' => $allModule,
                    'submenu' => $allSubmenu
                ];
                $accessRightModel = new AccesRightModel(); // Instantiate AccesRight model
                $accessRightModel->insert($dataAccess);
            }

            return redirect()->to('dashboard');
        } else {
            $this->theme_view = 'modal';
            $this->view_data['title'] = lang('application_add_new_company');
            $this->view_data['form_action'] = 'v_companies/create';
            $this->content_view = 'v_companies/_company';
        }
    }

    public function edit(int $id): void
    {
        if ($this->request->getMethod() === 'post') {
            $data = [
                'name' => service('request')->getPost('name')
            ];

            $this->db->table('v_companies')->where('id', $id)->update($data);
            return redirect()->to('dashboard');
        } else {
            $company = $this->db->table('v_companies')->where('id', $id)->get()->getRow();

            $this->theme_view = 'modal';
            $this->view_data['title'] = lang('application_edit_company');
            $this->view_data['form_action'] = 'v_companies/edit/' . $id;
            $this->view_data['company'] = $company;
            $this->content_view = 'v_companies/_company';
        }
    }

    public function delete(int $id): void
    {
        if ($this->request->getMethod() === 'post') {
            if ($this->user->validate_password(service('request')->getPost('password'))) {
                $this->db->table('v_companies')->where('id', $id)->delete();
                $this->db->table('core')->where('id_vcompanies', $id)->delete();
            } else {
                session()->setFlashdata('message', 'error:' . lang('messages_password_incorrect'));
            }

            return redirect()->to('dashboard');
        } else {
            $company = $this->db->table('v_companies')->where('id', $id)->get()->getRow();

            $this->theme_view = 'modal';
            $this->view_data['title'] = lang('application_delete_company');
            $this->view_data['form_action'] = 'v_companies/delete/' . $id;
            $this->view_data['company'] = $company;
            $this->content_view = 'v_companies/_delete';
        }
    }

    public function access(int $id): void
    {
        session()->set('current_company', $id);
        return redirect()->to('');
    }
}
