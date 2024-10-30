<?php

namespace App\Controllers;

use App\Models\ClientModel;
use App\Models\CompanyModel;
use App\Models\SettingModel;
use CodeIgniter\Controller;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\Email\Email;

class RegisterController extends Controller
{
	protected $clientModel;
	protected $companyModel;
	protected $settingModel;
	protected $session;
	protected $email;
	protected $view_data = [];

	public function __construct()
	{
		$this->clientModel = new ClientModel();
		$this->companyModel = new CompanyModel();
		$this->settingModel = new SettingModel();
		$this->email = \Config\Services::email();
		$this->session = session();
	}

	public function index(): RedirectResponse|string
	{
		$currentCompanyId = $this->session->get('current_company');
		$coreSettings = $this->settingModel->find($currentCompanyId);

		if ($coreSettings->registration != 1) {
			return redirect()->to('login');
		}

		if ($this->request->getMethod() === 'post') {
			return $this->handleRegistration($coreSettings);
		}

		return $this->showRegistrationForm();
	}

	protected function handleRegistration($coreSettings): RedirectResponse
	{
		$postData = $this->request->getPost();

		// Validate inputs
		if (!$this->validateInputs($postData)) {
			return $this->setValidationErrors($postData);
		}

		// Check if client or company already exists
		$client = $this->clientModel->where('email', trim($postData['email']))->first();
		$company = $this->companyModel->where('name', trim($postData['name']))->first();

		if ($client || $company) {
			return $this->setClientOrCompanyError($client, $company);
		}

		// Create company
		$companyId = $this->createCompany($postData, $coreSettings);

		// Create client
		$clientId = $this->createClient($postData, $companyId, $coreSettings);

		if ($clientId) {
			$this->sendConfirmationEmail($postData['email'], $coreSettings, $companyId);
			return redirect()->to('login')->with('message', 'Registration successful.');
		}

		return redirect()->to('register')->with('message', 'Registration error occurred.');
	}

	protected function validateInputs(array $data): bool
	{
		return !empty(trim($data['name'])) &&
			!empty(trim($data['email'])) &&
			!empty($data['password']) &&
			!empty(trim($data['firstname'])) &&
			!empty(trim($data['lastname'])) &&
			!empty(trim($data['confirmcaptcha']));
	}

	protected function setValidationErrors(array $data): RedirectResponse
	{
		return redirect()->back()->withInput()->with('errors', [
			'message' => 'Please fill in all required fields.'
		]);
	}

	protected function setClientOrCompanyError($client, $company): RedirectResponse
	{
		$errors = [];
		if ($client) {
			$errors[] = 'Email already taken.';
		}
		if ($company) {
			$errors[] = 'Company name already taken.';
		}
		return redirect()->back()->withInput()->with('errors', $errors);
	}

	protected function createCompany(array $data, $coreSettings): int
	{
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
		$coreSettings->save();

		$company = $this->companyModel->insert($companyData);
		return $this->companyModel->insertID(); // Return new company ID
	}

	protected function createClient(array $data, int $companyId, $coreSettings): ?int
	{
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
			'company_id' => $companyId,
			'password' => password_hash($data['password'], PASSWORD_DEFAULT) // Secure password hashing
		];

		return $this->clientModel->insert($clientData) ? $this->clientModel->insertID() : null;
	}

	protected function sendConfirmationEmail(string $email, $coreSettings, int $companyId): void
	{
		// Configure email sending
		// Email logic remains the same as in the original
		$this->email->setFrom($coreSettings->email, $coreSettings->company);
		$this->email->setTo($email);
		$this->email->setSubject('Your account has been created');

		$parseData = [
			'link' => base_url('login'),
			'company' => $coreSettings->company,
			'company_reference' => $companyId,
			'logo' => '<img src="' . base_url($coreSettings->logo) . '" alt="' . $coreSettings->company . '"/>',
			'invoice_logo' => '<img src="' . base_url($coreSettings->invoice_logo) . '" alt="' . $coreSettings->company . '"/>'
		];

		$emailTemplate = view($coreSettings->template . '/templates/email_create_account', $parseData);
		$this->email->setMessage($emailTemplate);
		$this->email->send();
	}

	protected function showRegistrationForm(): string
	{
		$this->view_data['error'] = false;
		$this->view_data['form_action'] = 'register';
		return view('auth/register', $this->view_data);
	}
}
