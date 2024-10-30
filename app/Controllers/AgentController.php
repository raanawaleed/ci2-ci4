<?php

namespace App\Controllers;

use App\Models\VerifusersModel; // Assuming your model name follows PSR standards
use App\Models\UserModel;
use App\Controllers\BaseController;
use App\Models\ClientModel;
use CodeIgniter\HTTP\Files\UploadedFile; // File upload handler

class AgentController extends BaseController
{
	protected VerifusersModel $verifusersModel;
	protected UserModel $userModel;
	protected $clientModel;
	protected $view_data = [];

	public function __construct()
	{
		$this->verifusersModel = new VerifusersModel();
		$this->userModel = new UserModel();
		$this->clientModel = new ClientModel();

		if (!session()->get('user')) {
			return redirect()->to('login');
		}
	}

	public function verifUser()
	{
		$currentUserId = session()->get('user_id');
		$currentUsername = $this->verifusersModel->getUsername($currentUserId);
		$emails = array_column($this->verifusersModel->verifUsername(), 'email');
		$emails = array_diff($emails, [$currentUsername[0]->email]);

		return $this->response->setJSON(['result' => in_array($this->request->getPost('email'), $emails) ? 1 : 0]);
	}

	public function verifEmail()
	{
		$emails = array_column($this->verifusersModel->getEmails(), 'email');

		return $this->response->setJSON(['result' => in_array($this->request->getPost('email'), $emails) ? 1 : 0]);
	}

	public function verifPassword()
	{
		$currentUserId = session()->get('user_id');
		$currentHashedPassword = $this->verifusersModel->getPassword($currentUserId)[0]->hashed_password;

		$isValidPassword = password_verify($this->request->getPost('oldpassword'), $currentHashedPassword);

		return $this->response->setJSON(['result' => $isValidPassword ? 1 : 0]);
	}

	public function index()
	{
		$user = !session()->get('user_id') ? $this->clientModel->find(session()->get('user_id')) : $this->userModel->find(session()->get('user_id'));

		if ($this->request->getMethod() === 'post') {
			$this->handleUpload($user);
		} else {
			$this->view_data['user'] = $user;
			$this->view_data['title'] = lang('application_change_password');
			$this->view_data['form_action'] = 'agent/';
			return view('blueline/settings/_userform', $this->view_data);
		}
	}

	protected function handleUpload($user)
	{
		$file = $this->request->getFile('userfile');

		// Verify the file upload
		if ($file->isValid() && !$file->hasMoved()) {
			$newName = $file->getRandomName();
			$file->move('./files/media/', $newName);

			$userPic = $newName;
		} else {
			$error = $file->getErrorString();
			return $this->session->setFlashdata('message', 'error: ' . $error);
		}

		$attr = [];
		if ($this->request->getPost('password')) {
			$attr['password'] = password_hash($this->request->getPost('password'), PASSWORD_BCRYPT);
		}

		if (isset($userPic)) {
			$attr['userpic'] = $userPic;
		}

		if ($this->userModel->updateUser($attr)) {
			$this->session->setFlashdata('message', 'success:' . lang('messages_changes_saved'));
		} else {
			$this->session->setFlashdata('message', 'error: Un problème est apparu');
		}

		return redirect()->to('');
	}

	public function hash_password($password)
	{
		$salt = bin2hex(random_bytes(32));
		$hash = hash('sha256', $salt . $password);
		return $salt . $hash;
	}

	public function language($lang = false)
	{
		$folder = APPPATH . 'Language/';
		if (file_exists($folder . $lang)) {
			setcookie('fc2language', $lang, time() + 31536000); // Set cookie for 1 year
		}
		return redirect()->to('');
	}
}
