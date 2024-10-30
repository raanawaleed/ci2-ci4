<?php

namespace App\Controllers;


use App\Models\FactureModel;
use App\Models\CompanyModel;
use App\Models\ClientModel;

use App\Controllers\BaseController;

class ClientsController extends BaseController
{
	protected $invoice, $view_data = [];
	protected $company, $clientModel;
	function __construct()
	{
		$this->invoice = new FactureModel();

		$this->company = new CompanyModel();

		$this->clientModel = new ClientModel();

		$access = FALSE;

		if (session('client')) {
			redirect('cprojects');
		} elseif (session('user')) {

			$this->view_data['project_access'] = FALSE;
			$this->view_data['invoice_access'] = FALSE;

			foreach ($this->view_data['menu'] as $menuItem) {
				if ($menuItem->link === "clients") {
					$access = true;
				}
				if ($menuItem->link === "invoices") {
					$this->view_data['invoice_access'] = true;
				}
				if ($menuItem->link === "projects") {
					$this->view_data['project_access'] = true;
				}
			}

			if (!$access) {
				redirect('login');
			}
		} else {
			redirect('login');
		}
	}

	public function index()
	{
		$this->view_data['companies'] = $this->company->getActiveClient();

		foreach ($this->view_data['companies'] as $key => $value) {
			$contact_principale = $this->company->getClientById($value->client_id);
			$this->view_data['companies'][$key]->client_id = $contact_principale;
		}

		return view('clients/all', $this->view_data);
	}

	//Créer un nouveau contact client
	public function create($company_id = false)
	{
		if ($this->request->getMethod() === 'post') {
			$uploadPath = './files/media/';
			$config = [
				'upload_path' => $uploadPath,
				'encrypt_name' => true,
				'allowed_types' => 'gif|jpg|png',
				'max_width' => 180,
				'max_height' => 180,
			];

			$this->load->library('upload', $config);

			if ($this->upload->do_upload('userfile')) {
				$data = $this->upload->data();
				$_POST['userpic'] = $data['file_name'];
			}

			// Sanitize input
			$clientData = $this->request->getPost();
			$clientData['timbre_fiscal'] = $clientData['timbre_fiscal'] ?? 1;
			$clientData['access'] = isset($clientData['access']) ? implode(',', $clientData['access']) : null;
			$clientData['company_id'] = $company_id;
			$clientData = array_map('htmlspecialchars', $clientData);

			if (!$this->clientModel->insert($clientData)) {
				session()->setFlashdata('message', 'error: Client creation failed.');
			} else {
				session()->setFlashdata('message', 'success: Client added successfully.');
			}

			return redirect()->to('clients/view/' . $company_id);
		}

		$this->view_data['clients'] = $this->clientModel->where('inactive', 0)->findAll();
		$this->view_data['modules'] = Module::where('type', 'client')->orderBy('sort', 'asc')->findAll();
		$this->view_data['next_reference'] = $this->clientModel->getLastClient();
		$this->view_data['form_action'] = 'clients/create/' . $company_id;
		$this->view_data['title'] = lang('application_add_new_contact');

		return view('clients/_clients', $this->view_data);
	}


	//mettre à jour un contact client
	public function update($id = false)
	{
		if ($this->request->getMethod() === 'post') {
			$client = $this->clientModel->find($id);
			$clientData = $this->request->getPost();

			// If no password change is required, unset it
			if (empty($clientData['password'])) {
				unset($clientData['password']);
			} else {
				$clientData['password'] = password_hash($clientData['password'], PASSWORD_BCRYPT);
			}

			$clientData['access'] = isset($clientData['access']) ? implode(',', $clientData['access']) : null;
			$clientData = array_map('htmlspecialchars', $clientData);

			if (!$client->update($id, $clientData)) {
				session()->setFlashdata('message', 'error: Failed to update client.');
			} else {
				session()->setFlashdata('message', 'success: Client updated successfully.');
			}

			return redirect()->to('clients/view/' . $client->company_id);
		}

		$this->view_data['client'] = $this->clientModel->find($id);
		$this->view_data['modules'] = Module::where('type', 'client')->orderBy('sort', 'asc')->findAll();
		$this->view_data['form_action'] = 'clients/update';
		$this->view_data['title'] = lang('application_edit_client');

		return view('clients/_clients', $this->view_data);
	}

	public function delete($id = false)
	{
		$client = $this->clientModel->find($id);
		$client->inactive = 1;
		$client->save();

		if (!$client) {
			session()->setFlashdata('message', 'error: Failed to delete client.');
		} else {
			session()->setFlashdata('message', 'success: Client deleted successfully.');
		}

		return redirect()->to('clients');
	}

	public function view($id = false)
	{
		$this->view_data['submenu'] = [
			lang('application_back') => 'clients',
		];
		$this->view_data['company'] = $this->company->find($id);
		$client_id = $this->view_data['company']->client_id;
		$this->view_data['contact_principale'] = $this->company->getClientById($client_id);

		$this->view_data['estimates'] = $this->invoice->where('estimate !=', 0)
			->where('company_id', $id)
			->findAll();
		$this->view_data['invoices'] = $this->invoice->getByCompany($id);

		return view('clients/view', $this->view_data);
	}
	function notes($id = FALSE)
	{
		if ($_POST) {
			unset($_POST['send']);
			$_POST = array_map('htmlspecialchars', $_POST);
			$project = Company::find($id);
			$project->update_attributes($_POST);
		}
		$this->theme_view = 'ajax';
	}

	function company($condition = FALSE, $id = FALSE)
	{
		switch ($condition) {
			case 'create':
				if ($_POST) {
					unset($_POST['send']);

					if (isset($_POST['timbre_fiscal'])) {
						$_POST['timbre_fiscal'] = 1;
					} else {
						$_POST['timbre_fiscal'] = 0;
					}
					//Garantee de company 
					if (isset($_POST['guarantee'])) {
						$_POST['guarantee'] = 1;
					} else {
						$_POST['guarantee'] = 0;
					}
					//Garantee de company 
					if (isset($_POST['tva'])) {
						$_POST['tva'] = 1;
					} else {
						$_POST['tva'] = 0;
					}
					$_POST = array_map('htmlspecialchars', $_POST);
					$company = Company::create($_POST);
					$companyid = Company::last();
					$new_company_reference = $_POST['reference'];
					$_POST['reference'] = $new_company_reference;
					$option = array("id_vcompanies" => $_SESSION['current_company']);
					$company_reference = Setting::find($option);
					$company_reference->update_attributes(array('company_reference' => $new_company_reference));
					if (!$company) {
						$this->session->set_flashdata('message', 'error:' . $this->lang->line('messages_company_add_error'));
					} else {
						$this->session->set_flashdata('message', 'success:' . $this->lang->line('messages_company_add_success'));
					}
					redirect('clients/view/' . $companyid->id);
				} else {
					$this->view_data['next_reference'] = Company::last();
					$this->theme_view = 'modal';
					$this->view_data['title'] = $this->lang->line('application_add_new_company');
					$this->view_data['form_action'] = 'clients/company/create';
					$this->content_view = 'clients/_company';
				}
				break;
			case 'update':
				if ($_POST) {
					unset($_POST['estimate_status']);
					unset($_POST['send']);
					$id = $_POST['id'];
					if (isset($_POST['view'])) {
						$view = $_POST['view'];
						unset($_POST['view']);
					}
					if (isset($_POST['timbre_fiscal'])) {
						$_POST['timbre_fiscal'] = 1;
					} else {
						$_POST['timbre_fiscal'] = 0;
					}
					//Garantee de company 
					if (isset($_POST['guarantee'])) {
						$_POST['guarantee'] = 1;
					} else {
						$_POST['guarantee'] = 0;
					}
					//tva
					if (isset($_POST['tva'])) {
						$_POST['tva'] = 1;
					} else {
						$_POST['tva'] = 0;
					}
					$_POST = array_map('htmlspecialchars', $_POST);
					$company = Company::find($id);
					$company->update_attributes($_POST);
					if (!$company) {
						$this->session->set_flashdata('message', 'error:' . $this->lang->line('messages_save_company_error'));
					} else {
						$this->session->set_flashdata('message', 'success:' . $this->lang->line('messages_save_company_success'));
					}
					redirect('clients/view/' . $id);

				} else {
					$this->view_data['company'] = Company::find($id);
					$this->theme_view = 'modal';
					$this->view_data['title'] = $this->lang->line('application_edit_company');
					$this->view_data['form_action'] = 'clients/company/update';
					$this->content_view = 'clients/_company';
				}
				break;
			case 'delete':
				$company = Company::find($id);
				$company->inactive = '1';
				$company->save();
				foreach ($company->clients as $value) {
					$client = Client::find($value->id);
					$client->inactive = '1';
					$client->save();
				}
				$this->content_view = 'clients/all';
				if (!$company) {
					$this->session->set_flashdata('message', 'error:' . $this->lang->line('messages_delete_company_error'));
				} else {
					$this->session->set_flashdata('message', 'success:' . $this->lang->line('messages_delete_company_success'));
				}
				redirect('clients');
				break;
			case 'passToPersistent':
				if ($_POST) {
					unset($_POST['send']);
					$id = $_POST['id'];
					if (isset($_POST['view'])) {
						$view = $_POST['view'];
						unset($_POST['view']);
					}
					if (isset($_POST['timbre_fiscal'])) {
						$_POST['timbre_fiscal'] = 1;
					} else {
						$_POST['timbre_fiscal'] = 0;
					}
					//Garantee de company 
					if (isset($_POST['guarantee'])) {
						$_POST['guarantee'] = 1;
					} else {
						$_POST['guarantee'] = 0;
					}
					//tva
					if (isset($_POST['tva'])) {
						$_POST['tva'] = 1;
					} else {
						$_POST['tva'] = 0;
					}
					$_POST = array_map('htmlspecialchars', $_POST);
					$company = Company::find($id);
					$_POST['passager'] = 0;
					$company->update_attributes($_POST);
					if (!$company) {
						$this->session->set_flashdata('message', 'error:' . $this->lang->line('messages_save_company_error'));
					} else {
						$this->session->set_flashdata('message', 'success:' . $this->lang->line('messages_save_company_success'));
					}
					redirect('clients/view/' . $id);
				} else {
					$this->view_data['company'] = Company::find($id);
					$this->theme_view = 'modal';
					$this->view_data['title'] = $this->lang->line('application_edit_company');
					$this->view_data['form_action'] = 'clients/company/passToPersistent';
					$this->content_view = 'clients/_company';
				}
				break;
		}
	}

	function credentials($id = FALSE, $email = FALSE, $newPass = FALSE)
	{
		if ($email) {
			$this->load->helper('file');
			$client = Client::find($id);
			$client->password = $client->set_password($newPass);
			$client->save();
			$option = array("id_vcompanies" => $_SESSION['current_company']);
			$setting = Setting::find($option);
			$this->email->from($setting->email, $setting->company);
			$this->email->to($client->email);
			$this->email->subject($setting->credentials_mail_subject);
			$this->load->library('parser');
			$parse_data = array(
				'client_contact' => $client->firstname . ' ' . $client->lastname,
				'client_company' => $client->company->name,
				'client_link' => $setting->domain,
				'company' => $setting->company,
				'username' => $client->email,
				'password' => $newPass,
				'logo' => '<img src="' . base_url() . '' . $setting->logo . '" alt="' . $setting->company . '"/>',
				'invoice_logo' => '<img src="' . base_url() . '' . $setting->invoice_logo . '" alt="' . $setting->company . '"/>'
			);

			$message = read_file('./application/views/' . $setting->template . '/templates/email_credentials.html');
			$message = $this->parser->parse_string($message, $parse_data);
			$this->email->message($message);
			if ($this->email->send()) {
				$this->session->set_flashdata('message', 'success:' . $this->lang->line('messages_send_login_details_success'));
			} else {
				$this->session->set_flashdata('message', 'error:' . $this->lang->line('messages_send_login_details_error'));
			}
			redirect('clients/view/' . $client->company_id);

		} else {
			$this->view_data['client'] = Client::find($id);
			$this->theme_view = 'modal';
			function random_password($length = 8)
			{
				$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
				$password = substr(str_shuffle($chars), 0, $length);
				return $password;
			}
			$this->view_data['new_password'] = random_password();
			$this->view_data['title'] = $this->lang->line('application_login_details');
			$this->view_data['form_action'] = 'clients/credentials';
			$this->content_view = 'clients/_credentials';
		}
	}

	function hash_passwords()
	{
		$clients = Client::all();
		foreach ($clients as $client) {
			$pass = $client->password_old;
			$client->password = $client->set_password($pass);
			$client->save();
		}
		redirect('clients');
	}

	function ClientPassager()
	{

		$options = array('conditions' => array('inactive=?', 0));
		$this->view_data['companies'] = Company::find('all', $options);

		$this->content_view = 'clients/allPassager';
	}

	//All refernce of client 
	public function AllReference()
	{
		$reference = $this->company->getAllReference();
		header('Content-Type: application/json');
		echo json_encode($reference);
		exit();
	}
}