<?php

namespace App\Controllers;


use App\Models\SettingModel;
use App\Models\RefTypeModel;
use App\Models\RefTypeOccurencesModel;
use App\Models\CompteBancaireModel;
use App\Models\UserModel;
use App\Models\SalarieModel;
use App\Models\FactureModel;

use App\Controllers\BaseController;

class SettingsController extends BaseController
{
	protected $refType;
	protected $referentiels;
	protected $compteBancaire;
	protected $account;
	protected $salaries;
	protected $invoice;
	protected $settingTables;

	public function __construct()
	{
		$this->refType = new RefTypeModel();
		$this->referentiels = new RefTypeOccurencesModel();
		$this->compteBancaire = new CompteBancaireModel();
		$this->account = new UserModel();
		$this->salaries = new SalarieModel();
		$this->invoice = new FactureModel();
		$this->settingTables = new SettingModel();

		if (!session()->get('client') && !session()->get('user')) {
			return redirect()->to('login');
		}

		helper('suivi');

		// Load settings based on the current company
		if (session()->has('current_company')) {
			$this->view_data['submenu'] = [
				lang('application_settings') => 'settings',
				lang('application_edit_company') => 'settings/editcompany',
				lang('application_users_access') => 'settings/listUser',
				lang('application_GestionCommercial') => 'settings/gestionCommercial',
				lang('application_ref_vente') => 'settings/refvente',
				lang('application_ref_societe') => 'settings/societe',
				lang('application_compte_bancaire') => 'settings/compteBancaire',
				lang('application_param_paie_cnss') => 'settings/paiecnss',
				lang('application_param_smtp') => 'settings/smtp_settings',
				lang('application_choice_templates') => 'settings/choice_template',
				lang('application_notification_template') => 'settings/notification',
			];
		} else {
			$this->view_data['submenu'] = [
				lang('application_users') => 'settings/users',
				lang('application_system_updates') => 'settings/updates',
			];
		}

		// Load default settings
		$this->config->load('defaults');
		$option = ["id_vcompanies" => session()->get('current_company')];
		$this->view_data['settings'] = $this->settingTables->find($option);
	}

	public function index()
	{
		$this->view_data['breadcrumb'] = lang('application_settings');
		$this->view_data['breadcrumb_id'] = "settings";

		$currency = $this->db->table('ref_type_occurences')
			->where(['id_type' => 10, 'visible' => 1])
			->get()->getResult();
		$this->view_data['currencys'] = $currency;

		$echeances = $this->db->table('ref_type_occurences')
			->where(['id_type' => 20, 'visible' => 1])
			->get()->getResult();
		$this->view_data['echeances'] = $echeances;

		$this->view_data['form_action'] = 'settings/settings_update';

		if (!session()->has('current_company')) {
			return redirect()->to('login');
		}

		return view('settings/settings_all', $this->view_data);
	}

	public function chiffreDevise($name)
	{
		$Idref = $this->refType->getRefTypeByName(urldecode($name))->id;
		$chiffre = $this->referentiels->getReferentielsByIdType($Idref)->name;

		return $this->response->setContentType('application/json')->setBody(json_encode($chiffre));
	}

	public function settings_update()
	{
		if ($this->request->getMethod() === 'post') {
			$option = ["id_vcompanies" => session()->get('current_company')];
			$settings = $this->settingTables->find($option);

			$displayfacture = $this->request->getPost('display_logo_facture') ? 1 : 0;
			$displaydevis = $this->request->getPost('display_logo_devis') ? 1 : 0;
			$displaycommande = $this->request->getPost('display_logo_commande') ? 1 : 0;
			$displaylivraison = $this->request->getPost('display_logo_livraison') ? 1 : 0;
			$displayavoir = $this->request->getPost('display_logo_avoir') ? 1 : 0;

			$data = [
				"email" => $this->request->getPost('email'),
				"language" => $this->request->getPost('language'),
				"date_format" => $this->request->getPost('date_format'),
				"date_time_format" => $this->request->getPost('date_time_format'),
				"currency" => $this->request->getPost('currency'),
				"echeance" => $this->request->getPost('echeance'),
				"money_currency_position" => $this->request->getPost('money_currency_position'),
				"display_logo_facture" => $displayfacture,
				"display_logo_devis" => $displaydevis,
				"display_logo_commande" => $displaycommande,
				"display_logo_livraison" => $displaylivraison,
				"display_logo_avoir" => $displayavoir,
				"chiffre" => $this->request->getPost('chiffre'),
				"signataire" => $this->request->getPost('signataire')
			];

			$id = session()->get('current_company');
			$this->db->table('core')->where('id_vcompanies', $id)->set($data)->update();

			if ($this->db->affectedRows() > 0) {
				session()->setFlashdata('message', 'success:' . lang('messages_save_settings_success'));
			} else {
				session()->setFlashdata('message', 'error:' . lang('messages_save_settings_error'));
			}

			return redirect()->to('settings');
		}
	}

	public function settings_reset($template = false)
	{
		helper('file');
		$option = ["id_vcompanies" => session()->get('current_company')];
		$settings = $this->settingTables->find($option);

		if ($template) {
			$data = read_file('./app/Views/' . $settings->template . '/templates/default/' . $template . '.html');
			if (write_file('./app/Views/' . $settings->template . '/templates/' . $template . '.html', $data)) {
				session()->setFlashdata('message', 'success:' . lang('messages_reset_mail_body_success'));
				return redirect()->to('settings/templates');
			}
		}
	}

	public function templates($template = "invoice")
	{
		helper('file');

		$option = ["id_vcompanies" => session()->get('current_company')];
		$settings = $this->settingsModel->find($option);
		$filename = './app/Views/' . $settings->template . '/templates/email_' . $template . '.html';
		$this->view_data['folder_path'] = '/app/Views/' . $settings->template . '/templates/ ';
		$this->view_data['not_writable'] = !is_writable($filename);

		$this->view_data['breadcrumb'] = lang('application_templates');
		$this->view_data['breadcrumb_id'] = "templates";
		$this->view_data['breadcrumb_sub'] = lang('application_' . $template);
		$this->view_data['breadcrumb_sub_id'] = $template;

		if ($this->request->getMethod() === 'post') {
			$data = html_entity_decode($this->request->getPost("mail_body"));
			unset($_POST["mail_body"], $_POST["send"]);

			$settings->updateAttributes($_POST);

			if (write_file($filename, $data)) {
				session()->setFlashdata('message', 'success:' . lang('messages_save_template_success'));
				return redirect()->to('settings/templates/' . $template);
			} else {
				session()->setFlashdata('message', 'error:' . lang('messages_save_template_error'));
				return redirect()->to('settings/templates/' . $template);
			}
		} else {
			$this->view_data['email'] = read_file($filename);
			$this->view_data['template'] = $template;
			$this->view_data['template_files'] = get_filenames('./app/Views/' . $settings->template . '/templates/default/');
			$this->view_data['template_files'] = array_map(fn($file) => str_replace(['.html', 'email_'], '', $file), $this->view_data['template_files']);
			$this->view_data['settings'] = $settings;
			$this->view_data['form_action'] = 'settings/templates/' . $template;

			return view('settings/templates', $this->view_data);
		}
	}

	public function invoice_templates($dest = false, $template = false)
	{
		helper('file');

		$option = ["id_vcompanies" => session()->get('current_company')];
		$settings = $this->settingsModel->find($option);
		$filename = './app/Views/' . $settings->template . '/templates/invoice/default.php';
		$this->view_data['folder_path'] = '/app/Views/' . $settings->template . '/templates/ ';
		$this->view_data['breadcrumb'] = lang('application_pdf_templates');
		$this->view_data['breadcrumb_id'] = "pdf_templates";

		if ($this->request->getMethod() === 'post') {
			unset($_POST["send"]);
			$_POST["pdf_path"] = $_POST["pdf_path"] ?? 0;
			$settings->updateAttributes($_POST);

			if ($settings) {
				session()->setFlashdata('message', 'success:' . lang('messages_save_template_success'));
				return redirect()->to('settings/invoice_templates/');
			} else {
				session()->setFlashdata('message', 'error:' . lang('messages_save_template_error'));
				return redirect()->to('settings/invoice_templates/');
			}
		} else {
			if ($dest && $template) {
				$DBdest = $dest . "_pdf_template";
				$settings->updateAttributes([$DBdest => 'templates/' . $dest . '/' . $template]);
				return redirect()->to('settings/invoice_templates');
			} else {
				$this->view_data['invoice_template_files'] = array_map(fn($file) => str_replace('.php', '', $file), get_filenames('./app/Views/' . $settings->template . '/templates/invoice/'));
				$this->view_data['estimate_template_files'] = array_map(fn($file) => str_replace('.php', '', $file), get_filenames('./app/Views/' . $settings->template . '/templates/estimate/'));

				$this->view_data['settings'] = $settings;
				$active_template = basename($settings->invoice_pdf_template);
				$this->view_data['active_template'] = str_replace('.php', '', $active_template);
				$active_estimate_template = basename($settings->estimate_pdf_template);
				$this->view_data['active_estimate_template'] = str_replace('.php', '', $active_estimate_template);
				$this->view_data['form_action'] = 'settings/invoice_templates/' . $template;

				return view('settings/invoice_templates', $this->view_data);
			}
		}
	}

	public function editpaiement($id)
	{
		if ($this->request->getMethod() === 'post') {
			$ins = [
				'name' => $this->request->getPost('name'),
				'description' => $this->request->getPost('description'),
				'update_date' => date("Y-m-d H:i:s"),
			];
			$this->db->table('ref_type_occurences')->update($ins, ['id' => $id]);
			return redirect()->to('settings/gestionCommercial');
		} else {
			$this->view_data['title'] = lang('application_edit');
			$this->view_data['form_action'] = 'settings/editpaiement/' . $id;
			$data = $this->db->table('ref_type_occurences')->getWhere(['id' => $id])->getRow();
			$this->view_data['data'] = $data;
			$this->view_data['theme_view'] = 'modal';

			return view('settings/addref', $this->view_data);
		}
	}

	public function deletepaiement($id)
	{
		$this->db->table('ref_type_occurences')->update(['visible' => 0], ['id' => $id]);
		return redirect()->to('settings/gestionCommercial');
	}

	public function Timbre()
	{
		if ($this->request->getMethod() === 'post') {
			$data = $this->request->getPost();
			unset($data['send'], $data['nomdevis'], $data['nomfacture'], $data['nomcommande'], $data['nomabonnement'], $data['nomlivraison'], $data['nomproject'], $data['nomavoir'], $data['estimate'], $data['invoice'], $data['subscription'], $data['commandePrefix'], $data['livraisonPrefix'], $data['projectPrefix'], $data['avoirPrefix'], $data['avoir']);

			$settings = $this->settingsModel->find(['id_vcompanies' => session()->get('current_company')]);
			$settings->updateAttributes($data);

			if ($settings) {
				session()->setFlashdata('message', 'success:' . lang('messages_save_settings_success'));
			} else {
				session()->setFlashdata('message', 'error:' . lang('messages_save_settings_error'));
			}
			return redirect()->to('settings/gestionCommercial');
		}
	}
	public function ajoutecheance()
	{
		if ($this->request->getMethod() === 'post') {
			$data = $this->request->getPost();
			unset($data['send']);
			$data['id_type'] = 20;
			$data['created_date'] = date('Y-m-d');
			$data['visible'] = 1;

			$this->db->table('ref_type_occurences')->insert($data);
			return redirect()->to('settings/gestionCommercial');
		} else {
			$this->view_data['title'] = lang('application-add');
			$this->view_data['form_action'] = 'settings/ajoutecheance';
			return view('settings/addechance', $this->view_data);
		}
	}

	public function editecheance($id)
	{
		if ($this->request->getMethod() === 'post') {
			$data = $this->request->getPost();
			$data['update_date'] = date('Y-m-d');
			$data['updated_by'] = session()->get('user')->id;
			unset($data['send']);

			$this->db->table('ref_type_occurences')->update($data, ['id' => $id]);
			return redirect()->to('settings/gestionCommercial');
		} else {
			$this->view_data['title'] = lang('application-edit');
			$this->view_data['form_action'] = 'settings/editecheance/' . $id;
			$echeance = $this->db->table('ref_type_occurences')->getWhere(['id' => $id])->getRow();
			$this->view_data['data'] = $echeance;
			return view('settings/addechance', $this->view_data);
		}
	}

	public function deleteecheance($id)
	{
		$this->db->table('ref_type_occurences')->update(['visible' => 0], ['id' => $id]);
		return redirect()->to('settings/gestionCommercial');
	}

	public function achat()
	{
		$livraison = $this->db->table('ref_type_occurences')->where(['visible' => 1, 'id_type' => 1])->get()->getResult();
		$commande = $this->db->table('referentiels')->where(['visible' => 1, 'id_type' => 2])->get()->getResult();

		$this->view_data['breadcrumb'] = lang('application_ref_achat');
		$this->view_data['breadcrumb_id'] = "achat";
		$this->view_data['livraison'] = $livraison;
		$this->view_data['commande'] = $commande;
		return view('settings/referentielAchat', $this->view_data);
	}

	public function vente()
	{
		$facture = $this->db->table('ref_type_occurences')->where(['visible' => 1, 'id_type' => 3])->get()->getResult();
		$devis = $this->db->table('ref_type_occurences')->where(['visible' => 1, 'id_type' => 4])->get()->getResult();
		$commande = $this->db->table('ref_type_occurences')->where(['visible' => 1, 'id_type' => 5])->get()->getResult();
		$payment = $this->db->table('ref_type_occurences')->where(['visible' => 1, 'id_type' => 8])->get()->getResult();
		$avoir = $this->db->table('ref_type_occurences')->where(['visible' => 1, 'id_type' => 25])->get()->getResult();

		$this->view_data['breadcrumb'] = lang('application_ref_vente');
		$this->view_data['breadcrumb_id'] = "vente";
		$this->view_data['facture'] = $facture;
		$this->view_data['devis'] = $devis;
		$this->view_data['commande'] = $commande;
		$this->view_data['payment'] = $payment;
		$this->view_data['avoir'] = $avoir;
		return view('settings/referentielVente', $this->view_data);
	}

	public function calendar()
	{
		$this->view_data['breadcrumb'] = lang('application_calendar');
		$this->view_data['breadcrumb_id'] = "calendar";

		if ($this->request->getMethod() === 'post') {
			$data = $this->request->getPost();
			unset($data['send']);
			$option = ["id_vcompanies" => session()->get('current_company')];
			$settings = $this->settingsModel->find($option);
			$settings->update($data);

			$message = $settings ?
				'success:' . lang('messages_save_settings_success') :
				'error:' . lang('messages_save_settings_error');

			session()->setFlashdata('message', $message);
			return redirect()->to('settings/calendar');
		} else {
			$option = ["id_vcompanies" => session()->get('current_company')];
			$this->view_data['settings'] = $this->settingsModel->find($option);
			$this->view_data['form_action'] = 'settings/calendar';
			return view('settings/calendar', $this->view_data);
		}
	}

	public function ticket()
	{
		$this->view_data['breadcrumb'] = lang('application_ticket');
		$this->view_data['breadcrumb_id'] = "ticket";
		$this->view_data['imap_loaded'] = extension_loaded('mysql');

		if ($this->request->getMethod() === 'post') {
			$data = $this->request->getPost();
			unset($data['send']);
			$data['ticket_config_active'] = $data['ticket_config_active'] ?? "0";
			$data['ticket_config_delete'] = $data['ticket_config_delete'] ?? "0";
			$data['ticket_config_ssl'] = $data['ticket_config_ssl'] ?? "0";
			$data['ticket_config_imap'] = $data['ticket_config_imap'] ?? "0";

			$option = ["id_vcompanies" => session()->get('current_company')];
			$settings = $this->settingsModel->find($option);
			$settings->update($data);

			$message = $settings ?
				'success:' . lang('messages_save_settings_success') :
				'error:' . lang('messages_save_settings_error');

			session()->setFlashdata('message', $message);
			return redirect()->to('settings/ticket');
		} else {
			$option = ["id_vcompanies" => session()->get('current_company')];
			$this->view_data['settings'] = $this->settingsModel->find($option);
			$this->view_data['types'] = TypeModel::where('inactive', '0')->findAll();
			$this->view_data['owners'] = UserModel::where('status', 'active')->findAll();
			$this->view_data['form_action'] = 'settings/ticket';
			return view('settings/ticket', $this->view_data);
		}
	}

	public function ticketType($id = false, $condition = false)
	{
		if ($condition === "delete") {
			$type = TypeModel::find($id);
			$type->update(['inactive' => '1']);
		} else {
			if ($this->request->getMethod() === 'post') {
				$data = $this->request->getPost();
				unset($data['send']);

				if ($id) {
					$type = TypeModel::find($id);
					$type->update($data);
				} else {
					$type = TypeModel::create($data);
				}

				$message = $type ?
					'success:' . lang('messages_save_settings_success') :
					'error:' . lang('messages_save_settings_error');

				session()->setFlashdata('message', $message);
				return redirect()->to('settings/ticket');
			} else {
				if ($id) {
					$this->view_data['type'] = TypeModel::find($id);
				}
				$this->view_data['title'] = lang('application_type');
				$this->view_data['form_action'] = 'settings/ticket_type/' . $id;
				return view('settings/_ticket_type', $this->view_data);
			}
		}
		$this->theme_view = 'modal_nojs';
	}

	public function testPostmaster()
	{
		$option = ["id_vcompanies" => session()->get('current_company')];
		$emailConfig = $this->settingsModel->find($option);

		$config = [
			'login' => $emailConfig->ticket_config_login,
			'pass' => $emailConfig->ticket_config_pass,
			'host' => $emailConfig->ticket_config_host,
			'port' => $emailConfig->ticket_config_port,
			'mailbox' => $emailConfig->ticket_config_mailbox,
			'service_flags' => ($emailConfig->ticket_config_imap == "1" ? "/imap" : "/pop3") .
				($emailConfig->ticket_config_ssl == "1" ? "/ssl" : "") .
				$emailConfig->ticket_config_flags
		];

		$this->load->library('PeekerConnect');
		$this->peeker_connect->initialize($config);

		$this->view_data['msgresult'] = $this->peeker_connect->is_connected() ? "success" : "error";
		$this->view_data['result'] = $this->peeker_connect->is_connected() ?
			"Connection to email mailbox successful!" :
			"Connection to email mailbox not successful!";

		$this->peeker_connect->message_waiting();
		$this->peeker_connect->close();
		$this->view_data['trace'] = $this->peeker_connect->trace();
		$this->view_data['title'] = lang('application_postmaster_test');
		return view('settings/_testpostmaster', $this->view_data);
	}

	public function customize()
	{
		$this->view_data['breadcrumb'] = lang('application_customize');
		$this->view_data['breadcrumb_id'] = "customize";
		$this->load->helper('file');

		$option = ["id_vcompanies" => session()->get('current_company')];
		$this->view_data['settings'] = $this->settingsModel->find($option);

		if ($this->request->getMethod() === 'post') {
			$data = $this->request->getPost('css-area');
			if (write_file('./assets/' . $this->view_data['settings']->template . '/css/user.css', $data)) {
				session()->setFlashdata('message', 'success:' . lang('messages_save_customize_success'));
			} else {
				session()->setFlashdata('message', 'error:' . lang('messages_save_customize_error'));
			}
			return redirect()->to('settings/customize');
		} else {
			$this->view_data['writable'] = is_writable('./assets/' . $this->view_data['settings']->template . '/css/user.css');
			$this->view_data['css'] = read_file('./assets/' . $this->view_data['settings']->template . '/css/user.css');
			$this->view_data['form_action'] = 'settings/customize';
			return view('settings/customize', $this->view_data);
		}
	}

	public function registration()
	{
		if ($this->request->getMethod() === 'post') {
			$data = $this->request->getPost();
			unset($data['send']);
			$data['registration'] = $data['registration'] ?? 0;
			$data["default_client_modules"] = !empty($data["access"]) ? implode(",", $data["access"]) : "";
			unset($data["access"]);

			$option = ["id_vcompanies" => session()->get('current_company')];
			$settings = $this->settingsModel->find($option);
			$settings->update($data);

			session()->setFlashdata('message', 'success:' . lang('messages_save_settings_success'));
			return redirect()->to('settings/registration');
		}

		$this->view_data['breadcrumb'] = lang('application_registration');
		$this->view_data['breadcrumb_id'] = "registration";
		$this->view_data['client_modules'] = ModuleModel::where('type', 'client')->orderBy('sort', 'asc')->findAll();
		$option = ["id_vcompanies" => session()->get('current_company')];
		$this->view_data['settings'] = $this->settingsModel->find($option);
		$this->view_data['form_action'] = 'settings/registration';
		return view('settings/registration', $this->view_data);
	}

	public function userDelete($user = false)
	{
		if ($this->user->id != $user) {
			$user = UserModel::find($user);
			$user->status = 'deleted';
			$user->save();
			session()->setFlashdata('message', 'success:' . lang('messages_delete_user_success'));
		} else {
			session()->setFlashdata('message', 'error:' . lang('messages_delete_user_error'));
		}
		return redirect()->to('settings/listUser');
	}
	public function user_create()
	{
		if ($this->request->getMethod() === 'post') {
			$this->load->library('upload', $this->getUploadConfig());

			if ($this->upload->do_upload()) {
				$uploadData = $this->upload->data();
				$this->request->setPost('userpic', $uploadData['file_name']);
			}

			$postData = $this->prepareUserData();
			$postData['hashed_password'] = hash_password($this->request->getPost('password'));

			// Create new user
			$this->db->insert('users', $postData);
			$userId = $this->db->insert_id();

			// Manage access rights
			$this->manageUserAccess($userId);

			return redirect('settings/listUser');
		}

		// Prepare data for view
		return $this->loadUserFormView('settings/_usernew', 'application_create_user');
	}

	public function user_update($userId)
	{
		$user = User::find($userId);
		if ($this->request->getMethod() === 'post') {
			$this->load->library('upload', $this->getUploadConfig());

			if ($this->upload->do_upload()) {
				$uploadData = $this->upload->data();
				$this->request->setPost('userpic', $uploadData['file_name']);
			}

			// Update user data
			$this->updateUserData($user);
			$this->manageUserAccess($user->id);

			$this->session->set_flashdata('message', 'success:' . lang('messages_save_user_success'));
			return redirect('settings/users');
		}

		// Prepare data for view
		return $this->loadUserFormView('settings/_userform', 'application_edit_user', $user);
	}

	private function getUploadConfig()
	{
		return [
			'upload_path' => './files/media/',
			'encrypt_name' => TRUE,
			'allowed_types' => 'gif|jpg|jpeg|png',
			'max_width' => '180',
			'max_height' => '180',
		];
	}

	private function prepareUserData()
	{
		$postData = $this->request->getPost();
		unset($postData['file-name'], $postData['send'], $postData['confirm_password']);
		$postData['status'] = 'active';
		return array_map('htmlspecialchars', $postData);
	}

	private function manageUserAccess($userId)
	{
		// Get access data
		$modules = Module::find('all', ['conditions' => ['type != ?', 'client']]);
		$allModule = implode(',', array_column($modules, 'id'));

		$accessCompanies = $this->request->getPost('accessCompany') ?: [$_SESSION['current_company']];
		foreach ($accessCompanies as $companyId) {
			$data = [
				'user_id' => $userId,
				'company_id' => $companyId,
				'menu' => $this->request->getPost('admin') ? $allModule : $this->getDefaultModule(),
				'submenu' => $this->getSubmenus(),
			];
			AccesRigth::create($data);
		}
	}

	private function loadUserFormView($view, $title, $user = null)
	{
		$this->view_data['title'] = lang($title);
		$this->view_data['user'] = $user;
		$this->view_data['form_action'] = "settings/user_" . ($user ? 'update/' . $user->id : 'create');
		$this->view_data['modules'] = Module::find('all', ['conditions' => ['type != ?', 'client']]);
		return view($view, $this->view_data);
	}

	public function logs($action = false)
	{
		$this->view_data['breadcrumb'] = lang('application_logs');
		$this->view_data['breadcrumb_id'] = "logs";

		if ($action === "clear") {
			delete_files('./application/logs/');
			$this->session->set_flashdata('message', 'success:' . lang('messages_log_cleared'));
			return redirect('settings/logs');
		}

		$this->view_data['logs'] = $this->getRecentLogs();
		$this->view_data['settings'] = Setting::find(['id_vcompanies' => $_SESSION['current_company']]);
		$this->view_data['form_action'] = 'settings/logs';
		return view('settings/logs', $this->view_data);
	}

	private function getRecentLogs()
	{
		$lognames = array_diff(get_filenames('./application/logs/'), ["index.html"]);
		krsort($lognames);
		$logs = [];

		foreach (array_slice($lognames, 0, 6) as $filename) {
			$logs[] = read_file('./application/logs/' . $filename);
		}
		return array_filter(explode("\n", implode($logs)));
	}

	public function smtp_settings()
	{
		$id = $_SESSION['current_company'];
		$configuration = $this->db->get_where('smtp_conf', ['id_company' => $id])->result();

		if ($this->request->getMethod() === 'post') {
			$this->updateSmtpConfig($id);
			return redirect('settings/smtp_settings');
		}

		$this->view_data['breadcrumb'] = lang('application_smtp_settings');
		$this->view_data['form_action'] = 'settings/smtp_settings/';
		$this->view_data['settings'] = Setting::find(['id_vcompanies' => $id]);
		return view('settings/smtp_settings', $this->view_data);
	}

	private function updateSmtpConfig($id)
	{
		$data = $this->request->getPost();
		$this->db->where('id_company', $id);
		$this->db->update('smtp_conf', $data);
	}

	public function sendTestMail()
	{
		sendMail('', 'Test', '', $this->request->getPost('dist'), '', 'Ceci est un mail de test.', 'settings/smtp_settings', 'settings/smtp_settings');
	}

	public function addUnit()
	{
		if ($this->request->getMethod() === 'post') {
			$data = [
				'description' => $this->input->post('description'),
				'value' => $this->input->post('value'),
			];
			$this->db->insert('item_units', $data);
			return redirect('settings/gestionCommercial');
		}

		$this->view_data['title'] = lang('application_add_unit');
		$this->view_data['form_action'] = 'settings/addUnit';
		return view('settings/_unit', $this->view_data);
	}

	public function updateUnit($id)
	{
		if ($this->request->getMethod() === 'post') {
			$data = [
				'description' => $this->input->post('description'),
				'value' => $this->input->post('value'),
			];
			$this->db->update('item_units', $data, ['id' => $id]);
			return redirect('settings/gestionCommercial');
		}

		$unit = $this->db->get_where('item_units', ['id' => $id])->row();
		$this->view_data['title'] = lang('application_update_unit');
		$this->view_data['form_action'] = 'settings/updateUnit/' . $id;
		$this->view_data['unit'] = $unit;
		return view('settings/_unit', $this->view_data);
	}
	private function getDefaultModule()
	{
		// Retrieve default modules for the current company
		$defaultModules = Module::find('all', [
			'conditions' => [
				'type != ? AND name != ? AND default_module = ?',
				'client',
				'settings',
				'1'
			],
			'order' => 'sort ASC'
		]);

		// Collect and return the IDs of the default modules
		return implode(',', array_column($defaultModules, 'id'));
	}

	private function getSubmenus()
	{
		// Retrieve all submenus that are not of type 'client'
		$submenus = Submenu::find('all', [
			'conditions' => ['type != ?', 'client'],
			'order' => 'sort ASC'
		]);

		// Collect and return the IDs of the submenus
		return implode(',', array_column($submenus, 'id'));
	}
	function deleteUnit($id)
	{
		$this->db->where('id', $id);
		$this->db->delete('item_units');
		redirect('settings/gestionCommercial');
	}

	function ajoutCompteBancaire()
	{
		if ($_POST) {
			$settings = setting::find(['id_vcompanies' => $_SESSION['current_company']]);

			if (!empty($_POST['default_compteBancaire'])) {
				$data = ['compteBancaire' => $this->compteBancaire->getLastId() + 1];
				$settings->update_attributes($data);
			}

			unset($_POST['send'], $_POST['default_compteBancaire']);
			$_POST['visible'] = 1;
			$_POST['created_date'] = date("Y-m-d H:i:s");

			$this->db->insert('comptes_bancaires', $_POST);
			redirect('settings/compteBancaire');
		} else {
			$this->theme_view = 'modal';
			$this->view_data['title'] = $this->lang->line('application-add');
			$this->view_data['form_action'] = 'settings/ajoutCompteBancaire';
			$this->content_view = 'settings/addCompteBancaire';
		}
	}

	function editCompteBancaire($id)
	{
		if ($_POST) {
			$settings = setting::find(['id_vcompanies' => $_SESSION['current_company']]);

			$data = [
				'compteBancaire' => !empty($_POST['default_compteBancaire']) ? $id : null
			];
			$settings->update_attributes($data);

			unset($_POST['default_compteBancaire'], $_POST['send']);
			$_POST['update_date'] = date("Y-m-d H:i:s");

			$this->db->where('id', $id);
			$this->db->update('comptes_bancaires', $_POST);
			redirect('settings/compteBancaire');
		} else {
			$this->view_data['settings'] = setting::find(['id_vcompanies' => $_SESSION['current_company']]);
			$this->theme_view = 'modal';
			$this->view_data['title'] = $this->lang->line('application_edit');

			$this->db->where('id', $id);
			$this->view_data['data'] = $this->db->get('comptes_bancaires')->row();
			$this->content_view = 'settings/addCompteBancaire';
		}
	}

	function ajoutTaxe()
	{
		if ($_POST) {
			$data = [
				'id_type' => 9,
				'name' => $this->input->post('name'),
				'description' => $this->input->post('description'),
				'created_date' => date("Y-m-d H:i:s"),
				'visible' => 1
			];
			$this->db->insert('ref_type_occurences', $data);

			$lastId = $this->referentiels->getLastId();
			// Mettre à jour la taxe TVA par défaut  
			if (!empty($_POST['tax'])) {
				$settings = setting::find(['id_vcompanies' => $_SESSION['current_company']]);
				$settings->update_attributes(['tax' => $lastId]);
			}

			redirect('settings/refvente');
		} else {
			$this->view_data['settings'] = setting::find(['id_vcompanies' => $_SESSION['current_company']]);
			$this->theme_view = 'modal';
			$this->view_data['title'] = $this->lang->line('application-add');
			$this->view_data['form_action'] = 'settings/ajoutTaxe';
			$this->content_view = 'settings/referentiel/addreftaxe';
		}
	}


	function deleteTaxe($id)
	{
		$this->db->where('id', $id);
		$this->db->set('visible', 0);
		$this->db->update('ref_type_occurences');
		redirect('settings/refvente');
	}

	function editTaxe($id)
	{
		if ($_POST) {
			$settings = setting::find(['id_vcompanies' => $_SESSION['current_company']]);

			$data = [
				'tax' => !empty($_POST['tax']) ? $id : null
			];
			$settings->update_attributes($data);

			$ins = [
				'name' => $this->input->post('name'),
				'description' => $this->input->post('description'),
				'update_date' => date("Y-m-d H:i:s"),
			];
			$this->db->where('id', $id);
			$this->db->update('ref_type_occurences', $ins);
			redirect('settings/refvente');
		} else {
			$this->view_data['settings'] = setting::find(['id_vcompanies' => $_SESSION['current_company']]);
			$this->theme_view = 'modal';
			$this->view_data['title'] = $this->lang->line('application_edit');
			$this->view_data['form_action'] = 'settings/editTaxe/' . $id;

			$this->db->where('id', $id);
			$this->view_data['data'] = $this->db->get('ref_type_occurences')->row();
			$this->content_view = 'settings/referentiel/addreftaxe';
		}
	}

	function Salarie()
	{
		$this->view_data['breadcrumb'] = $this->lang->line('application_rh_salarie');
		$this->view_data['breadcrumb_id'] = "Salarie";

		$types = [12 => 'situation', 13 => 'genre', 18 => 'contrat', 19 => 'fonction'];

		foreach ($types as $id_type => $key) {
			$this->db->where(['visible' => 1, 'id_type' => $id_type]);
			$this->view_data[$key] = $this->db->get('ref_type_occurences')->result();
		}

		$this->content_view = 'settings/referentielRHSalarie';
	}

	function editSalarie($id)
	{
		if ($_POST) {
			$ins = [
				'name' => $this->input->post('name'),
				'description' => $this->input->post('description'),
				'update_date' => date("Y-m-d H:i:s"),
			];
			$this->db->where('id', $id);
			$this->db->update('ref_type_occurences', $ins);
			redirect('settings/Salarie');
		} else {
			$this->theme_view = 'modal';
			$this->view_data['title'] = $this->lang->line('application_edit');
			$this->view_data['form_action'] = 'settings/editSalarie/' . $id;

			$this->db->where('id', $id);
			$this->view_data['data'] = $this->db->get('ref_type_occurences')->row();
			$this->content_view = 'settings/addref';
		}
	}


	function deleteSalarie($id)
	{
		$this->db->where('id', $id);
		$this->db->set('visible', 0);
		$this->db->update('ref_type_occurences');
		redirect('settings/Salarie');
	}

	function ajoutSitfam()
	{
		return $this->ajouterReference(12, 'settings/ajoutSitfam');
	}

	function ajoutgenre()
	{
		return $this->ajouterReference(13, 'settings/ajoutgenre');
	}

	function ajoutcontsalarie()
	{
		return $this->ajouterReference(18, 'settings/ajoutcontsalarie');
	}

	function ajoutFonction()
	{
		return $this->ajouterReference(19, 'settings/ajoutFonction');
	}
	private function ajouterReference($id_type, $form_action)
	{
		if ($_POST) {
			$data = [
				'id_type' => $id_type,
				'name' => $this->input->post('name'),
				'description' => $this->input->post('description'),
				'created_date' => date("Y-m-d H:i:s"),
				'visible' => 1
			];
			$this->db->insert('ref_type_occurences', $data);
			redirect('settings/Salarie');
		} else {
			$this->theme_view = 'modal';
			$this->view_data['title'] = $this->lang->line('application-add');
			$this->view_data['form_action'] = $form_action;
			$this->content_view = 'settings/addref';
		}
	}
	function editcompany()
	{
		$id = $_SESSION['current_company'];
		if ($_POST) {
			if (is_uploaded_file($_FILES['userfile']['tmp_name'])) {
				$this->uploadCompanyLogo();
			}

			$data = [
				"name" => $this->input->post('name'),
				"phone" => $this->input->post('phone'),
				"mobile" => $this->input->post('mobile'),
				"address" => $this->input->post('address'),
				"zipcode" => $this->input->post('zipcode'),
				"city" => $this->input->post('city'),
				"website" => $this->input->post('website'),
				"country" => $this->input->post('country'),
				"vat" => $this->input->post('vat'),
				"cnss" => $this->input->post('cnss')
			];

			if (!empty($_POST['company_logo'])) {
				$data["picture"] = $_POST['company_logo'];
			}

			$this->db->where('id', $id);
			$edit = $this->db->update('v_companies', $data);

			if (!$edit) {
				$this->session->set_flashdata('message', 'error:' . $this->lang->line('messages_update_company_error'));
			} else {
				$this->session->set_flashdata('message', 'success:' . $this->lang->line('messages_update_company_success'));
			}
			redirect('settings/editcompany/' . $id);
		} else {
			$this->loadCompanyEditView($id);
		}
	}

	private function uploadCompanyLogo()
	{
		$config = [
			'upload_path' => './files/media/',
			'encrypt_name' => FALSE,
			'overwrite' => TRUE,
			'allowed_types' => 'gif|jpg|jpeg|png|svg'
		];

		$this->load->library('upload', $config);

		if ($this->upload->do_upload("userfile")) {
			$data = $this->upload->data();
			$_POST['company_logo'] = $data['file_name'];
		}
	}

	private function loadCompanyEditView($id)
	{
		$this->view_data['breadcrumb'] = $this->lang->line('application_edit_company');
		$this->view_data['breadcrumb_id'] = "Societe";

		$this->db->where('id', $id);
		$this->view_data['company'] = $this->db->get('v_companies')->row();
		$this->view_data['form_action'] = 'settings/editcompany';
		$this->content_view = 'settings/_company';
	}
	function saveNotes()
	{
		$this->updateSetting('notes', $this->input->post('notes'));
		redirect('settings/gestionCommercial');
	}

	function saveFactureNotes()
	{
		$this->updateSetting('notes_facture', $this->input->post('notes_facture'));
		redirect('settings/gestionCommercial');
	}

	function updateDefaultCompteBancaire()
	{
		$this->updateSetting('compteBancaire', $this->input->post('compteBancaire'));
		redirect('settings/compteBancaire');
	}

	private function updateSetting($field, $value)
	{
		$option = ["id_vcompanies" => $_SESSION['current_company']];
		$core = Setting::find($option);
		$core->$field = $value;
		$core->save();
	}

	function compteBancaire()
	{
		$this->view_data['breadcrumb'] = $this->lang->line('application_compte_bancaire');
		$this->view_data['breadcrumb_id'] = "Societe";

		$company = $this->db->get('v_companies')->row();
		$compteBancaire = $this->db->get('comptes_bancaires')->result();

		$option = ["id_vcompanies" => $_SESSION['current_company']];
		$this->view_data['settings'] = Setting::find($option);
		$this->view_data['compteBancaire'] = $compteBancaire;
		$this->view_data['company'] = $company;
		$this->view_data['form_action'] = 'settings/updateDefaultCompteBancaire';

		$this->content_view = 'settings/compteBancaire';
	}

	function editPayment($id)
	{
		if ($_POST) {
			$this->updatePayment($id, [
				'name' => $this->input->post('name'),
				'description' => $this->input->post('description'),
				'update_date' => date("Y-m-d H:i:s"),
			]);
			redirect('settings/vente');
		} else {
			$this->loadPaymentEditView($id);
		}
	}

	private function updatePayment($id, $data)
	{
		$this->db->where('id', $id);
		$this->db->update('ref_type_occurences', $data);
	}

	private function loadPaymentEditView($id)
	{
		$this->theme_view = 'modal';
		$this->view_data['title'] = $this->lang->line('application_edit');
		$this->view_data['form_action'] = 'settings/editPayment/' . $id;

		$this->db->where('id', $id);
		$this->view_data['data'] = $this->db->get('ref_type_occurences')->row();

		$this->content_view = 'settings/addref';
	}

	function addPayment()
	{
		if ($_POST) {
			$this->insertPayment([
				'id_type' => 8,
				'name' => $this->input->post('name'),
				'description' => $this->input->post('description'),
				'created_date' => date("Y-m-d H:i:s"),
				'visible' => 1,
			]);
			redirect('settings/vente');
		} else {
			$this->loadPaymentAddView();
		}
	}

	private function insertPayment($data)
	{
		$this->db->insert('ref_type_occurences', $data);
	}

	private function loadPaymentAddView()
	{
		$this->theme_view = 'modal';
		$this->view_data['title'] = $this->lang->line('application-add');
		$this->view_data['form_action'] = 'settings/addPayment';

		$this->content_view = 'settings/addref';
	}

	function deletePayment($id)
	{
		$this->db->where('id', $id);
		$this->db->set('visible', 0);
		$this->db->update('ref_type_occurences');
		redirect('settings/vente');
	}
	function deleteCompteBancaire($id)
	{
		$this->updateCompteBancaireVisibility($id, 0);
		$this->updateCompanyCompteBancaireToNull();
		redirect('settings/_company');
	}

	private function updateCompteBancaireVisibility($id, $visibility)
	{
		$this->db->where('id', $id);
		$this->db->set('visible', $visibility);
		$this->db->update('comptes_bancaires');
	}

	private function updateCompanyCompteBancaireToNull()
	{
		$this->db->where('id_vcompanies', $_SESSION['current_company']);
		$this->db->set('compteBancaire', NULL);
		$this->db->update('core');
	}

	function checkUsername($username)
	{
		$user = $this->account->getByName($username);
		$output = !empty($user) && $user->status == 'active';
		header('Content-Type: application/json');
		echo json_encode($output);
		exit();
	}

	function compteBancairePreview($id = FALSE)
	{
		$this->load->helper(['dompdf', 'file']);
		$this->load->library('parser');

		$data["compteBancaire"] = $this->compteBancaire->getCompteById($id);
		$option = ["id_vcompanies" => $_SESSION['current_company']];
		$data["core_settings"] = Setting::find($option);
		$data["company"] = $this->db->where('id', $data["core_settings"]->id)->get('v_companies')->row();

		$parse_data = [
			'invoice_id' => $data["core_settings"]->invoice_prefix . $data["invoice"]->reference,
			'client_link' => $data["core_settings"]->domain,
			'company' => $data["core_settings"]->company,
		];

		$html = $this->load->view($data["core_settings"]->template . '/' . $data["core_settings"]->comptebancaire_pdf_template, $data, true);
		$html = $this->parser->parse_string($html, $parse_data);
		$filename = $data['compteBancaire']->nom;

		// Generate the PDF
		$this->pdf->load_view($html, $filename);
	}

	function settings_rh()
	{
		$table = "setting_document_rh";
		if ($this->db->count_all($table) == 0) {
			$this->db->insert($table, $_POST);
		} else {
			$this->db->where('id_setting_rh', 1)->update($table, $_POST);
		}
		redirect('settings/paiecnss');
	}

	function paiecnss()
	{
		$table = "setting_document_rh";
		$this->view_data['data'] = $this->db->get($table)->result();
		$this->view_data['form_action'] = 'settings/settings_rh/';

		if ($_POST) {
			$this->handlePostPaiecnss();
		} else {
			$this->preparePaiecnssView();
		}

		$this->content_view = 'settings/paiecnss';
	}

	private function handlePostPaiecnss()
	{
		if (isset($_POST["fonction007"])) {
			$this->insertPaiecnss();
		} else {
			$this->updatePaiecnss();
		}
	}

	private function insertPaiecnss()
	{
		$this->cleanPostData();
		$_POST['id_vcompanie'] = (int) $_SESSION['current_company'];
		$_POST['id_type'] = 19;
		$this->db->insert('ref_type_occurences', $_POST);
		$this->session->set_flashdata('message', 'success:' . $this->lang->line('application_modified'));
		redirect('settings/paiecnss');
	}

	private function updatePaiecnss()
	{
		$id = $_POST['idparam'];
		$this->cleanPostData();
		$_POST['id_companie'] = (int) $_SESSION['current_company'];
		$this->db->where('id', $id)->update('referentiels_rh_paies', $_POST);
		$this->session->set_flashdata('message', 'success:' . $this->lang->line('application_modified'));
		redirect('settings/paiecnss');
	}

	private function cleanPostData()
	{
		unset($_POST['send'], $_POST['fonction007'], $_POST['zomba'], $_POST['userfile'], $_POST['file-name'], $_POST['view'], $_POST['idparam']);
		if (isset($_POST["access"])) {
			$_POST["access"] = implode(",", $_POST["access"]);
		} else {
			unset($_POST["access"]);
		}
	}

	private function preparePaiecnssView()
	{
		$this->view_data['outils'] = $this->db->where('id_type', 19)->where('id_vcompanie', (int) $_SESSION['current_company'])->get('ref_type_occurences')->result();
		$item = $this->db->where('id_companie', (int) $_SESSION['current_company'])->get('referentiels_rh_paies')->result();

		if (empty($item)) {
			$this->db->insert('referentiels_rh_paies', ['id_companie' => (int) $_SESSION['current_company']]);
		}
		$this->view_data['item'] = $item;

		// Populate absence motifs
		$this->view_data['refTab']['motif_absence'] = $this->getReferentielData("type_code_motif_absence", 'application_motif_absence');
		// Populate leave statuses
		$this->view_data['refTab']['statut_conges'] = $this->getReferentielData("type_code_statut_conges", 'application_statut_conges');
	}

	private function getReferentielData($typeCode, $label)
	{
		return [
			'tab' => $this->referentiels->getAllReferentielsByCodeType($this->config->item($typeCode), true),
			'libelle' => $this->lang->line($label),
			'url_add_ref' => 'settings/ajoutMotifabsence',
			'url_update_ref' => 'settings/editMotifabsence',
			'url_delete_ref' => 'settings/desactiverMotifabsence',
			'masquer_statut' => true,
		];
	}

	function add_fonction()
	{
		if ($_POST) {
			// Logic to handle form submission would go here
		} else {
			$this->theme_view = 'modal';
			$this->view_data['title'] = $this->lang->line('application_neauveau_fonction');
			$this->view_data['form_action'] = 'settings/paiecnss/';
			$this->content_view = 'rhpaie/addfonction';
		}
	}

	function delete_fonction($id = FALSE)
	{
		$this->db->where('id', $id);
		$this->db->delete('ref_type_occurences');
		redirect('settings/paiecnss'); // Assuming you want to redirect after deletion
	}
	// Render Modules
	public function rendermodule($id)
	{
		$option = ["id" => $id];
		$submenu = Submenu::find($option);

		if ($submenu) {
			$module = $this->db->get_where('modules', ['id' => $submenu->id_modules])->row();
			header('Content-Type: application/json');
			echo json_encode(['id' => $module->id]);
		} else {
			// Handle case where submenu is not found
			header('Content-Type: application/json');
			echo json_encode(['error' => 'Submenu not found']);
		}
		exit();
	}

	// Render submenus 
	public function renderSubmenu($id)
	{
		$submenus = Submenu::find('all', ['conditions' => ["id_modules" => $id]]);
		$tab = array_map(function ($submenu) {
			return $submenu->link;
		}, $submenus);

		header('Content-Type: application/json');
		echo json_encode($tab);
		exit();
	}

	function AjoutAvoir()
	{
		if ($_POST) {
			$data = [
				'id_type' => 25,
				'name' => $this->input->post('name'),
				'description' => $this->input->post('description'),
				'created_date' => date("Y-m-d H:i:s"),
				'visible' => 1
			];
			$this->db->insert('ref_type_occurences', $data);
			redirect('settings/vente');
		} else {
			$this->theme_view = 'modal';
			$this->view_data['title'] = $this->lang->line('application-add');
			$this->view_data['form_action'] = 'settings/AjoutAvoir';
			$this->content_view = 'settings/addref';
		}
	}

	function saveAvoirNotes()
	{
		$option = ["id_vcompanies" => $_SESSION['current_company']];
		$core = Setting::find($option);

		if ($core) {
			$core->notes_avoir = $this->input->post('notes_avoir');
			$core->save();
		}

		redirect('settings/gestionCommercial');
	}

	function GetBetween($var1 = "", $var2 = "", $pool)
	{
		$temp1 = strpos($pool, $var1);

		if ($temp1 === false) {
			return ''; // Return empty string if $var1 is not found
		}

		$temp1 += strlen($var1);
		$result = substr($pool, $temp1);
		$dd = strpos($result, $var2);

		if ($dd === false) {
			$dd = strlen($result);
		}

		return substr($result, 0, $dd);
	}
	//liste des utilisateurs 
	function listUser($statut = 1)
	{
		$this->view_data['breadcrumb'] = $this->lang->line('application_users_access');
		$this->view_data['breadcrumb_id'] = "users";

		if (!in_array($statut, [0, 1])) {
			redirect("settings/listUser");
		}

		$options = [
			'conditions' => [
				'status ' . ($statut == 0 ? '=' : '!=') . ' ?',
				'deleted'
			]
		];

		$users = User::all($options);
		$this->view_data['users'] = $users;
		$this->view_data['statut'] = $statut;
		$this->content_view = 'settings/listuser';
	}

	function user_access_update($id = null)
	{
		if (!is_null($id)) {
			$user = User::find($id);
		}

		if ($_POST) {
			$user = User::find($_POST['id']);
			$dscreen = ['default_screen' => $_POST['default_screen']];
			unset($_POST['default_screen'], $_POST['send']);

			// Handle access rights
			$access = AccesRigth::find(['conditions' => ['user_id = ? AND company_id = ?', $_POST['id'], $_SESSION['current_company']]]);
			$_POST['menu'] = !empty($_POST["menu"]) ? implode(",", $_POST["menu"]) : '';
			$_POST['submenu'] = !empty($_POST["submenu"]) ? implode(",", $_POST["submenu"]) : '';

			// Add default modules
			$defaultModules = implode(',', array_column(Module::find('all', ['order' => 'sort ASC', 'conditions' => ['type != ? AND default_module = ?', 'client', '1']]), 'id'));
			$_POST["menu"] = $defaultModules . ',' . $_POST["menu"];

			// Sanitize input
			$_POST = array_map('htmlspecialchars', $_POST);

			// Password handling
			if (!empty($_POST["password"]) && $_POST["password"] === $_POST["confirm_password"]) {
				$dscreen['password'] = $_POST["password"];
			}
			unset($_POST['password'], $_POST['confirm_password']);

			// Admin status
			$dscreen['admin'] = !empty($_POST['admin']) ? 1 : 0;

			// Handle employee status
			if (!empty($_POST['salarie'])) {
				$this->load->database();
				if (verifSalaries($user->salaries_id) === "false") {
					$data = [
						'mail' => $_POST['email'],
						'nom' => $_POST['firstname'],
						'prenom' => $_POST['lastname'],
					];
					$this->db->insert('salaries', $data);
					$dscreen['salaries_id'] = $this->db->insert_id();
				}
			}

			// Update user status
			$dscreen['status'] = $_POST['status'];
			$this->db->where('id', $user->salaries_id);
			$this->db->update('salaries', ['etat' => $dscreen['status'] === 'inactive' ? '0' : '1']);

			// Clean up and update
			unset($_POST['id'], $_POST['email'], $_POST['firstname'], $_POST['lastname'], $_POST['admin'], $_POST['salarie'], $_POST['status']);
			$access->update_attributes($_POST);
			$user->update_attributes(array_map('htmlspecialchars', $dscreen));

			$this->session->set_flashdata('message', 'success:' . $this->lang->line('messages_save_user_access'));
			redirect('settings/listUser');
		} else {
			$this->view_data['user'] = $user;
			$this->theme_view = 'modal';
			$this->view_data['title'] = $this->lang->line('application_create_user');
			// All modules
			$this->view_data['modules'] = Module::find('all', ['order' => 'sort ASC', 'conditions' => ['type != ?', 'client']]);
			// All submenus
			$this->view_data['submenus'] = modules_sous::find('all', ['order' => 'sort ASC', 'conditions' => ['type != ?', 'client']]);
			// Access menu for this user
			$access = AccesRigth::find(['conditions' => ['user_id = ? AND company_id = ?', $user->id, $_SESSION['current_company']]]);
			$this->view_data['tabaccess'] = explode(",", $access->menu);
			// Access submenus this user
			$this->view_data['tabsubaccess'] = explode(",", $access->submenu);
			$this->view_data['form_action'] = 'settings/user_access_update/';
			$this->content_view = 'settings/_accessUser';
		}
	}

	// Choix template pdf 
	function choice_template()
	{
		$this->view_data['form_action'] = 'settings/saveTemplate';
		$this->view_data['breadcrumb'] = "Choix template";

		// Get the number of templates 
		$dir = "application/views/blueline/templates/invoice";
		$files = array_filter(scandir($dir), function ($file) {
			return $file !== 'nuts.php' && !is_dir($dir . '/' . $file);
		});

		$option = ["id_vcompanies" => $_SESSION['current_company']];
		$this->view_data['defaultTemplate'] = Setting::find($option)->default_template;
		$this->view_data['files'] = array_map(function ($file) {
			return pathinfo($file, PATHINFO_FILENAME);
		}, $files);

		$this->content_view = 'settings/choiceTemplate';
	}

	function preview($file, $attachment = FALSE)
	{
		$this->load->helper(['dompdf', 'file']);
		$this->load->library('parser');

		$option = ["id_vcompanies" => $_SESSION['current_company']];
		$data["core_settings"] = Setting::find($option);

		$path = explode('/', $data["core_settings"]->invoice_pdf_template);
		$templatePath = $data["core_settings"]->template . '/' . $path[0] . '/' . $path[1] . '/' . $file;

		$html = $this->load->view($templatePath, $data, true);
		$html = $this->parser->parse_string($html, $parse_data); // Ensure $parse_data is defined
		$filename = pathinfo($file, PATHINFO_FILENAME) . ' template';

		pdf_create($html, str_replace(
			array('à', 'á', 'â', 'ã', 'ä', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', 'À', 'Á', 'Â', 'Ã', 'Ä', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', ' '),
			array('a', 'a', 'a', 'a', 'a', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y', 'A', 'A', 'A', 'A', 'A', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'N', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', '_'),
			$filename
		), TRUE, $attachment);
	}

	function saveTemplate()
	{
		unset($_POST['send']);
		$this->db->where('id_vcompanies', $_SESSION['current_company']);
		$this->db->update('core', $_POST);
		redirect('settings/choice_template');
	}
	function checkMail($email)
	{
		// Sanitize the email input
		$email = htmlspecialchars(trim($email));

		// Fetch user with the specified email
		$user = User::find(['conditions' => ['email = ?', $email]]);

		// Check if the user exists and is active
		$output = !empty($user) && $user->status === 'active';

		// Return the result as JSON
		header('Content-Type: application/json');
		echo json_encode($output);
		exit();
	}



	// Set breadcrumb and submenu for project parameters
	function projectReferentielSubMenu($breadcrumb)
	{
		$this->view_data['breadcrumb'] = $breadcrumb;
		$this->view_data['breadcrumb_id'] = $breadcrumb;

		$this->view_data['submenu'] = array(
			$this->lang->line('application_ref') => 'projects-params',
			$this->lang->line('application_tache_categorie') => 'projects-params/taches-par-defaut',
		);
	}

	// Manage commercial settings
	function gestionCommercial()
	{
		$this->view_data['breadcrumb'] = $this->lang->line('application_gestioncommercial');
		$this->view_data['breadcrumb_id'] = "achat";

		// Fetching visible occurrences and item units
		$this->view_data['echeance'] = $this->db->get_where('ref_type_occurences', ['visible' => 1, 'id_type' => 20])->result();
		$this->view_data['item_units'] = $this->db->get('item_units')->result();

		// Other settings
		$settings = setting::find(['id_vcompanies' => $_SESSION['current_company']]);
		$this->view_data['timbre'] = $settings->timbre_fiscal;
		$this->view_data['settings'] = $settings;
		$this->view_data['paiement'] = $this->db->get_where('ref_type_occurences', ['visible' => 1, 'id_type' => 8])->result();
		$this->view_data['compteBancaire'] = $this->db->get('comptes_bancaires')->result();

		$this->content_view = 'settings/gestioncommercial';
	}

	// Display sales references
	public function refvente()
	{
		$this->view_data['breadcrumb'] = $this->lang->line('application_ref_vente');
		$this->view_data['breadcrumb_id'] = "refvente";

		// Payment methods
		$this->setReferential('MoyensPaiement', $this->config->item("type_id_moyens_paiement"), 'settings/ajoutReferentiel');

		// Tax
		$this->view_data['taxe'] = $this->referentiels->getReferentielsByIdType($this->config->item("type_id_tva"));
		$this->view_data['refTab']['TVA']['libelle'] = "Taux TVA";

		$this->content_view = 'settings/referentiel/vente';
	}

	// Set up a referential tab
	private function setReferential($key, $id_type, $url)
	{
		$this->view_data['refTab'][$key]['tab'] = $this->referentiels->getReferentielsByIdType($id_type);
		$this->view_data['refTab'][$key]['libelle'] = "Moyens de paiement";
		$this->view_data['refTab'][$key]['url_add_ref'] = "{$url}/{$id_type}/refvente";
		$this->view_data['refTab'][$key]['url_update_ref'] = 'settings/editReferentiel/refvente';
		$this->view_data['refTab'][$key]['url_delete_ref'] = 'settings/desactiverReferentiel/refvente';
	}

	// Project parameters index
	public function indexParamsProjets()
	{
		$this->projectReferentielSubMenu("projects-params");
		$this->view_data['refTab']['uniteTemps'] = [
			'tab' => $this->referentiels->getReferentielsByIdType($this->config->item("type_id_unite_temps"), false),
			'libelle' => $this->lang->line('application_unite_affichage_temps_taches'),
			'form_action' => 'settings/choisirUniteTemps'
		];

		$this->content_view = 'settings/referentielProjets';
	}

	// Add a new referential
	function ajoutReferentiel($id_type, $redirect)
	{
		if ($this->input->post()) {
			$data = [
				'id_type' => $id_type,
				'name' => $this->input->post('name'),
				'description' => $this->input->post('description'),
				'created_date' => date("Y-m-d H:i:s"),
				'visible' => 1
			];
			$this->db->insert('ref_type_occurences', $data);
			redirect($redirect);
		} else {
			$this->theme_view = 'modal';
			$this->view_data['title'] = $this->lang->line('application-add');
			$this->view_data['form_action'] = "settings/ajoutReferentiel/{$id_type}/{$redirect}";
			$this->content_view = 'settings/addref';
		}
	}

	// Edit a referential
	function editReferentiel($redirect, $id)
	{
		if ($this->input->post()) {
			$data = [
				'name' => $this->input->post('name'),
				'description' => $this->input->post('description'),
				'update_date' => date("Y-m-d H:i:s")
			];
			$this->referentiels->updateReferentielById($data, $id);
			redirect($redirect);
		} else {
			$this->theme_view = 'modal';
			$this->view_data['title'] = $this->lang->line('application_edit');
			$this->view_data['form_action'] = "settings/editReferentiel/{$redirect}/{$id}";
			$this->view_data['data'] = $this->referentiels->getReferentielsById($id);
			$this->content_view = 'settings/addref';
		}
	}

	// Disable a referential
	function desactiverReferentiel($redirect, $id)
	{
		$data = [
			'visible' => 0,
			'update_date' => date("Y-m-d H:i:s"),
		];
		$this->referentiels->updateReferentielById($data, $id);
		redirect($redirect);
	}

	// Default task references index
	function indexTacheParDefaut($categ_id = false)
	{
		$this->view_data['projets_categ'] = $this->referentiels->getReferentielsByIdType($this->config->item("type_id_type_categorie_projet"));
		if ($categ_id) {
			$this->view_data['categorie_tickets'] = Categorie_tickets::all(["categorie_type_id" => $categ_id]);
			$this->view_data['url_add_ref'] = "settings/addTicketsParDefaut/{$categ_id}";
			$this->view_data['url_update_ref'] = 'settings/editTicketsParDefaut';
			$this->view_data['url_delete_ref'] = 'settings/desactiverTicketsParDefaut';
		}

		$this->projectReferentielSubMenu("projects-params/taches-par-defaut");
		$this->content_view = 'settings/referentielTacheParDefault';
	}
	/**
	 * Ajouter une tâche par défaut 
	 * @param int $categ_id
	 */
	function addTicketsParDefaut($categ_id)
	{
		if ($this->input->post()) {
			$data = [
				'categorie_type_id' => $categ_id,
				'name' => $this->input->post('name'),
				'description' => $this->input->post('description'),
				'created_by' => $this->user->id,
				'created_at' => date("Y-m-d H:i:s"),
				'status' => 1
			];

			$this->db->insert('categorie_tickets', $data);
			redirect('projects-params/taches-par-defaut/view/' . $categ_id);
		} else {
			$this->theme_view = 'modal';
			$this->view_data['title'] = $this->lang->line('application-add');
			$this->view_data['form_action'] = 'settings/addTicketsParDefaut/' . $categ_id;
			$this->content_view = 'settings/addref';
		}
	}

	/**
	 * Edition d'une tâche par défaut
	 * @param int $categ_id
	 * @param int $id
	 */
	function editTicketsParDefaut($categ_id, $id)
	{
		if ($this->input->post()) {
			$data = [
				'name' => $this->input->post('name'),
				'description' => $this->input->post('description'),
				'updated_by' => $this->user->id,
				'updated_at' => date("Y-m-d H:i:s"),
			];

			$this->settingTables->updateDataById($data, $id, Categorie_tickets::table_name());

			redirect('projects-params/taches-par-defaut/view/' . $categ_id);
		} else {
			$this->theme_view = 'modal';
			$this->view_data['title'] = $this->lang->line('application_edit');
			$this->view_data['form_action'] = 'settings/editTicketsParDefaut/' . $categ_id . '/' . $id;
			$this->view_data['data'] = $this->settingTables->getDataById($id, Categorie_tickets::table_name());
			$this->content_view = 'settings/addref';
		}
	}

	/**
	 * Désactiver une référence
	 * @param int $categ_id
	 * @param int $id
	 */
	function desactiverTicketsParDefaut($categ_id, $id)
	{
		$data = [
			'status' => 0,
			'updated_by' => $this->user->id,
			'updated_at' => date("Y-m-d H:i:s"),
		];

		$this->settingTables->updateDataById($data, $id, Categorie_tickets::table_name());
		redirect('projects-params/taches-par-defaut/view/' . $categ_id);
	}

	/********************************************************************
	 * Choisir l'unité de temps
	 *******************************************************************/
	function choisirUniteTemps()
	{
		if ($this->input->post()) {
			// Désactiver toutes les unités de temps
			$this->referentiels->updateReferentielByTypeId(['visible' => 0], $this->config->item("type_id_unite_temps"));

			// Activer l'unité sélectionnée
			$type = Referentiel::find(['id' => $this->input->post('visible')]);
			if ($type) {
				$type->update_attributes(['visible' => 1]);
			}

			redirect('projects-params');
		}
	}

	/*******************************************************************
	 * Ajout d'un état
	 *******************************************************************/
	function ajoutEtatProjet()
	{
		if ($this->input->post()) {
			$data = [
				'id_type' => $this->config->item('type_id_etat_projet'),
				'name' => $this->input->post('name'),
				'description' => $this->input->post('description'),
				'created_date' => date("Y-m-d H:i:s"),
				'visible' => 1
			];

			$this->db->insert('ref_type_occurences', $data);
			redirect('projects-params');
		} else {
			$this->theme_view = 'modal';
			$this->view_data['title'] = $this->lang->line('application-add');
			$this->view_data['form_action'] = 'settings/ajoutEtatProjet';
			$this->content_view = 'settings/addref';
		}
	}

	/**
	 * Edition d'un état de projet
	 * @param int $id
	 */
	function editEtatProjet($id)
	{
		if ($this->input->post()) {
			$data = [
				'name' => $this->input->post('name'),
				'description' => $this->input->post('description'),
				'update_date' => date("Y-m-d H:i:s")
			];

			$this->referentiels->updateReferentielById($data, $id);
			redirect('projects-params');
		} else {
			$this->theme_view = 'modal';
			$this->view_data['title'] = $this->lang->line('application_edit');
			$this->view_data['form_action'] = 'settings/editEtatProjet/' . $id;
			$this->view_data['data'] = $this->referentiels->getReferentielsById($id);
			$this->content_view = 'settings/addref';
		}
	}

	/*******************************************************************
	 * Ajout d'un motif d'absence
	 *******************************************************************/
	function ajoutMotifAbsence()
	{
		if ($this->input->post()) {
			$type = RefType::find(['name' => $this->config->item('type_code_motif_absence')]);

			if (!$type) {
				show_error("Veuillez vérifier le paramétrage des motifs d'absences des congés", "404", 'Une erreur a été rencontrée');
			}

			$data = [
				'id_type' => $type->id,
				'name' => $this->input->post('name'),
				'description' => $this->input->post('description'),
				'created_date' => date("Y-m-d H:i:s"),
				'visible' => 1
			];

			$this->db->insert('ref_type_occurences', $data);
			redirect('settings/paiecnss');
		} else {
			$this->theme_view = 'modal';
			$this->view_data['title'] = $this->lang->line('application-add');
			$this->view_data['form_action'] = 'settings/ajoutMotifAbsence';
			$this->content_view = 'settings/addref';
		}
	}
	/**
	 * Edition d'un motif d'absence
	 * @param  [type]
	 * @return [type]
	 */
	/*******************************************************************
	 * Edition d'un motif d'absence
	 *******************************************************************/
	function editMotifAbsence($id)
	{
		if ($this->input->post()) {
			$ins = [
				'name' => $this->input->post('name'),
				'description' => $this->input->post('description'),
				'update_date' => date("Y-m-d H:i:s")
			];

			$this->referentiels->updateReferentielById($ins, $id);
			redirect('settings/paiecnss');
		} else {
			$this->theme_view = 'modal';
			$this->view_data['title'] = $this->lang->line('application_edit');
			$this->view_data['form_action'] = 'settings/editMotifAbsence/' . $id;
			$this->view_data['data'] = $this->referentiels->getReferentielsById($id);
			$this->content_view = 'settings/addref';
		}
	}

	/*******************************************************************
	 * Ajout d'un statut de congé
	 *******************************************************************/
	function ajoutStatutConges()
	{
		if ($this->input->post()) {
			$type = RefType::find(['name' => $this->config->item('type_code_statut_conges')]);

			if (!$type) {
				show_error("Veuillez vérifier le paramétrage des statuts d'absences des congés", "404", 'Une erreur a été rencontrée');
			}

			$data = [
				'id_type' => $type->id,
				'name' => $this->input->post('name'),
				'description' => $this->input->post('description'),
				'created_date' => date("Y-m-d H:i:s"),
				'visible' => 1
			];

			$this->db->insert('ref_type_occurences', $data);
			redirect('settings/paiecnss');
		} else {
			$this->theme_view = 'modal';
			$this->view_data['title'] = $this->lang->line('application-add');
			$this->view_data['form_action'] = 'settings/ajoutStatutConges';
			$this->content_view = 'settings/addref';
		}
	}

	/*******************************************************************
	 * Edition d'un statut de congé
	 *******************************************************************/
	function editStatutConges($id)
	{
		if ($this->input->post()) {
			$ins = [
				'name' => $this->input->post('name'),
				'description' => $this->input->post('description'),
				'update_date' => date("Y-m-d H:i:s")
			];

			$this->referentiels->updateReferentielById($ins, $id);
			redirect('settings/paiecnss');
		} else {
			$this->theme_view = 'modal';
			$this->view_data['title'] = $this->lang->line('application_edit');
			$this->view_data['form_action'] = 'settings/editStatutConges/' . $id;
			$this->view_data['data'] = $this->referentiels->getReferentielsById($id);
			$this->content_view = 'settings/addref';
		}
	}

	/*******************************************************************
	 * Notification Settings
	 *******************************************************************/
	function notification()
	{
		$this->load->database();
		$this->db->select('email_notification');
		$this->db->from('core');
		$this->db->where('id', 2);

		$result = $this->db->get()->row();
		$this->view_data['form_action'] = 'settings/editemail/';
		$this->view_data['data'] = $result;
		$this->content_view = 'settings/notification';
	}

	/*******************************************************************
	 * Edit Email Notification Settings
	 *******************************************************************/
	function editemail()
	{
		$this->load->database();
		unset($_POST['send']);

		$data = [
			'email_notification' => $this->input->post('email_notification')
		];

		$this->db->where('id', 2);
		$success = $this->db->update('core', $data);

		if (!$success) {
			$this->session->set_flashdata('message', 'error:' . $this->lang->line('messages_erreur_email_success'));
		} else {
			$this->session->set_flashdata('message', 'success:' . $this->lang->line('messages_save_email_success'));
		}

		redirect('settings/notification');
	}

}

