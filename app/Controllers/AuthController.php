<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\ModuleModel;
use App\Models\SettingModel;

use App\Controllers\BaseController;
use App\Models\ProjectModel;

class AuthController extends BaseController
{
    protected $session, $setingModel, $userModel, $ModuleModel, $db, $projectModel;
    protected $view_data = [];
    public function __construct()
    {
        $this->session = session();
        $this->loadDatabase();
        $this->userModel = new UserModel();
        $this->ModuleModel = new ModuleModel();
        $this->setingModel = new SettingModel();
    }
    private function loadDatabase()
    {
        // Assuming database is already set in the configuration
        $this->db = \Config\Database::connect();
    }
    public function login()
    {
        $this->handlePasswordReset();
        $this->initializeVersionData();
        // if ($this->request->getMethod() === 'post') {
        //     return $this->processLogin();
        // }
        return view('blueline/auth/login', $this->view_data);
    }
    private function handlePasswordReset()
    {
        if (session()->get('reset_password')) {
            $this->view_data['reset_password'] = true;
            $this->view_data['send_email'] = session()->get('send_email');
        }
        session()->set('reset_password', false);
        session()->set('send_email', false);
    }
    private function initializeVersionData()
    {
        $core = $this->setingModel->first();
        $version = $this->db->table('version')->get()->getRow();
        // Check if $version is null before accessing its properties
        if ($version) {
            $this->view_data['version'] = "DB {$version->db_version} - R {$version->revision} - " . date('d/m/Y', strtotime($version->last_update));
        } else {
            $this->view_data['version'] = "Version information is not available.";
        }
        $data = $this->getLicenseData($core);
        if ($data->result === 'success') {
            $this->view_data['expiration'] = "";
        } else {
            $this->view_data['expiration'] = "License non valide, contactez nous";
        }
    }
    private function getLicenseData($core)
    {
        $actual_link = current_url();
        if (strpos($actual_link, "localhost") === false) {
            // You may want to enable this when moving to production
            // $json = file_get_contents($core->jarvis_url . '/api/license?token=' . $core->token_key);
            // return json_decode(substr($json, 3));
            return (object)[
                'result' => 'success',
                'valid_to' => date('Y-m-d', strtotime('+30 days')),
                'valid_from' => date('Y-m-d'),
            ];
        }
        return (object)[
            'result' => 'success',
            'valid_to' => date('Y-m-d', strtotime('+30 days')),
            'valid_from' => date('Y-m-d'),
        ];
    }
    public function processLogin()
    {

        $validation = \Config\Services::validation();
        $validation->setRules([
            'email' => 'required|valid_email',
            'password' => 'required|min_length[6]',
        ]);

        if (! $this->validate($validation->getRules())) {
            $this->view_data['error'] = true;
            $this->view_data['message'] = lang('messages_login_incorrect');
            $this->view_data['validation'] = $validation->getErrors(); // Include validation errors if needed
            return view('blueline/auth/login', $this->view_data);
        }

        // Get the validated inputs
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        // Validate the user's credentials using the UserModel
        $user = $this->userModel->validate_login($email, $password);
        // var_dump($user);
        // die();
        if ($user) {
            $this->setUserSession($user);
            // return redirect()->to($this->getRedirectUrl($user));
        }

        // If validation fails, send an error message back
        $this->view_data['error'] = true;
        $this->view_data['email'] = $email;
        $this->view_data['message'] = lang('messages_login_incorrect');

        $data = $this->view_data;

        return view('blueline/dashboard/dashboard', ['user' => $user]);
    }
    private function setUserSession($user)
    {
        session()->set('current_company', (int)$this->db->table('v_companies')->get()->getRow()->id);
        // $this->userModel->setSession($user);
    }
    private function getRedirectUrl($user)
    {
        $default_module = $this->ModuleModel->where('name', 'dashboard')->first();
        $url = $default_module ? $default_module->link : '/';
        if ($user->default_screen) {
            $default_module = $this->ModuleModel->find($user->default_screen);
            $url = $default_module ? $default_module->link : $url;
        }
        return $url;
    }
    public function logout()
    {
        if (session()->has('current_company')) {
            session()->remove('current_company');
        }
        $this->userModel->logout();
        return redirect()->to('login');
    }
}
