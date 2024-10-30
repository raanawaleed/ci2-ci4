<?php

namespace App\Controllers;

use App\Models\ClientModel;
use App\Models\CompanyModel;
use App\Models\SettingModel;
use App\Controllers\BaseController;

class RegisterController extends BaseController
{
	protected $view_data = [];

	private ClientModel $clientModel;
	private CompanyModel $companyModel;
	private SettingModel $settingModel;

	function __construct()
	{
		if (!$this->isLoggedIn()) {
			return redirect()->to('login');
		}
	}
	public function index()
	{
		$coreSettings = $this->settingModel->find(['id_vcompanies' => session()->get('current_company')]);

		if ($coreSettings->registration != 1) {
			return redirect()->to('login');
		}

		if ($this->request->getMethod() === 'post') {
			return $this->register();
		}

		$this->setViewData();
		return view('auth/register', $this->view_data);
	}

	private function register()
	{
		$postData = $this->request->getPost();
		$postData = array_map('htmlspecialchars', $postData);

		$client = $this->clientModel->findByEmail(trim($postData['email']));
		$company = $this->companyModel->findByName(trim($postData['name']));

		if (!$client && !$company && $this->isValidRegistration($postData)) {
			return $this->createAccount($postData);
		}

		$this->handleRegistrationErrors($client, $company);
		$this->setViewData($postData);
		return view('auth/register', $this->view_data);
	}

	private function createAccount(array $data)
	{
		$coreSettings = $this->settingModel->find(['id_vcompanies' => session()->get('current_company')]);

		$companyData = [
			'name' => trim($data['name']),
			'website' => trim($data['website']),
			'phone' => trim($data['phone']),
			'mobile' => trim($data['mobile']),
			'address' => trim($data['address']),
			'zipcode' => trim($data['zipcode']),
			'city' => trim($data['city']),
			'country' => trim($data['country']),
			'province' => trim($data['province']),
			'vat' => trim($data['vat']),
			'reference' => $coreSettings->company_reference
		];

		$coreSettings->company_reference++;
		$this->settingModel->save($coreSettings);

		$company = $this->companyModel->insert($companyData);

		if (!$company) {
			return $this->setFlashData('messages_registration_error', 'error');
		}

		$clientData = [
			'email' => trim($data['email']),
			'firstname' => trim($data['firstname']),
			'lastname' => trim($data['lastname']),
			'phone' => trim($data['phone']),
			'mobile' => trim($data['mobile']),
			'address' => trim($data['address']),
			'zipcode' => trim($data['zipcode']),
			'city' => trim($data['city']),
			'access' => $coreSettings->default_client_modules,
			'company_id' => $company
		];

		$client = $this->clientModel->insert($clientData);
		if ($client) {
			$this->clientModel->update($client, ['password' => password_hash($data['password'], PASSWORD_BCRYPT)]);
			$this->companyModel->update($company, ['client_id' => $client]);

			$this->sendRegistrationEmail($clientData, $coreSettings, $company);
			return $this->setFlashData('messages_registration_success', 'success');
		} else {
			return $this->setFlashData('messages_registration_error', 'error');
		}
	}

	private function sendRegistrationEmail(array $clientData, $coreSettings, $company)
	{
		$email = \Config\Services::email();

		$email->setFrom($coreSettings->email, $coreSettings->company);
		$email->setTo($clientData['email']);
		$email->setSubject(lang('application_your_account_has_been_created'));

		// Prepare email message
		$messageData = [
			'link' => base_url('login/'),
			'company' => $coreSettings->company,
			'company_reference' => $company->reference,
			'logo' => '<img src="' . base_url($coreSettings->logo) . '" alt="' . $coreSettings->company . '"/>',
			'invoice_logo' => '<img src="' . base_url($coreSettings->invoice_logo) . '" alt="' . $coreSettings->company . '"/>',
		];

		$emailTemplate = view('templates/email_create_account', $messageData);
		$email->setMessage($emailTemplate);

		// Attempt to send the email
		if (!$email->send()) {
			log_message('error', 'Email not sent: ' . $email->printDebugger());
		}
	}

	private function handleRegistrationErrors($client, $company)
	{
		if ($client) {
			$this->view_data['error'] = lang('messages_email_already_taken');
		}
		if ($company) {
			$this->view_data['error'] = "Company name is already taken!";
		}
	}

	private function isValidRegistration(array $data)
	{
		return !empty($data['name']) && !empty($data['email']) && !empty($data['password'])
			&& !empty($data['firstname']) && !empty($data['lastname']) && !empty($data['confirmcaptcha']);
	}

	private function setFlashData($messageKey, $status)
	{
		session()->setFlashdata('message', "$status: " . lang($messageKey));
		return redirect()->to('register');
	}

	private function setViewData(array $data = [])
	{
		$this->view_data = [
			'form_action' => 'register',
			'registerdata' => $data
		];
	}

	private function isLoggedIn()
	{
		return isset($this->client) || isset($this->user);
	}

}
