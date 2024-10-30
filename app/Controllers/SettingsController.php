<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class SettingsController extends BaseController
{
	function __construct()
	{
		parent::__construct();
		if ($this->client) {
		} elseif ($this->user) {
		} else {
			redirect('login');
		}
		$this->load->model('RefType_model','refType');
		$this->load->model('Ref_type_occurences_model','referentiels');	
		$this->load->model('compteBancaire_model','compteBancaire');	
		$this->load->model('user_model','account');	
		$this->load->model('User');
		$this->load->model('salarie_model','salaries');
		$this->load->model('Facture_model','invoice');
		$this->load->model('Setting_model', 'settingTables');
		$this->load->model('modules_sous_model');
		$this->load->helper('suivi_helper');
		//$this->load->library('My_PHPMailer');
		$access = FALSE;
		unset($_POST['DataTables_Table_0_length']);
		
		
		if (isset($_SESSION['current_company'])){
			$this->view_data['submenu'] = array(
				$this->lang->line('application_settings') => 'settings',
				$this->lang->line('application_edit_company')=>'settings/editcompany',
				$this->lang->line('application_users_access') => 'settings/listUser',
				$this->lang->line('application_GestionCommercial') => 'settings/gestionCommercial',
				$this->lang->line('application_ref_vente')=>'settings/refvente',	
				$this->lang->line('application_ref_societe')=>'settings/societe',
				$this->lang->line('application_compte_bancaire')=>'settings/compteBancaire',
				$this->lang->line('application_param_paie_cnss')=>'settings/paiecnss',
				$this->lang->line('application_param_smtp')=>'settings/smtp_settings',
				$this->lang->line('application_choice_templates')=>'settings/choice_template',
				$this->lang->line('application_notification_template')=>'settings/notification',
			);
		} else {
				$this->view_data['submenu'] = array(	                                						
					$this->lang->line('application_users') => 'settings/users',
					$this->lang->line('application_system_updates') => 'settings/updates',
				);
				$this->load->database();
		}
		$this->config->load('defaults');
		$option=array("id_vcompanies"=>$_SESSION['current_company']);
		$settings = Setting::find($option);
		$this->view_data['update_count'] = FALSE;
	}
	
	function index()
	{
		$this->view_data['breadcrumb'] = $this->lang->line('application_settings');
		$this->view_data['breadcrumb_id'] = "settings";
		$option=array("id_vcompanies"=>$_SESSION['current_company']);
		$this->view_data['settings'] = Setting::find($option);
		$currency=$this->db->query('select * from ref_type_occurences where id_type=10 and visible=1')->result();
		$this->view_data['currencys']=$currency;
		$echeances=$this->db->query('select * from ref_type_occurences where id_type=20 and visible=1')->result();
		$this->view_data['echeances']=$echeances;
		$this->view_data['form_action'] = 'settings/settings_update';
		if (isset($_SESSION['current_company'])){
			$this->content_view = 'settings/settings_all';
		}else{
			redirect('login');

		}
	
	}

	function chiffreDevise($name){
		$Idref= $this->refType->getRefTypeByName(urldecode($name))->id; 
		$chiffre = $this->referentiels->getReferentielsByIdType($Idref)->name;  	
		header('Content-Type: application/json');
		echo json_encode($chiffre);
		exit();
	}

	//mettre à jour les paramètres "Paramètres"
	function settings_update(){
		if($_POST){
			$option=array("id_vcompanies"=>$_SESSION['current_company']);
			$settings = Setting::find($option);
			$id=$_SESSION['current_company'];
			if($_POST['display_logo_facture']){
				$displayfacture=1;
			}else{
				$displayfacture=0;
			}
			if($_POST['display_logo_devis']){
				$displaydevis=1;
			}else{
			$displaydevis=0;
			}
			if($_POST['display_logo_commande']){
			$displaycommande=1;
			}else{
			$displaycommande=0;
			}
			if($_POST['display_logo_livraison']){
			$displaylivraison=1;
			}else{
			$displaylivraison=0;
			}
			if($_POST['display_logo_avoir']){
				$displayavoir=1;
			}else{
				$displayavoir=0;
			}
			$this->db->where('id_vcompanies',$id);
			$up=$this->db->get('core')->result()[0];
			if(!$up){
				$setting=$this->db->query('select * from core')->result()[0];
				unset($setting->id);
				$setting->id_vcompanies=$_SESSION['current_company'];
				$this->db->insert('core',$setting);
				$data=array(
							"email"=>$_POST['email'],
							"language"=>$_POST['language'],
							"date_format"=>$_POST['date_format'],
							"date_time_format"=>$_POST['date_time_format'],
							"currency"=>$_POST['currency'],
							"echeance"=>$_POST['echeance'],
							"money_currency_position"=>$_POST['money_currency_position'],
							"display_logo_facture"=>$displayfacture,
							"display_logo_devis"=>$displaydevis,
							"display_logo_commande"=>$displaycommande,
							"display_logo_livraison"=>$displaylivraison,
							"display_logo_avoir"=>$displayavoir,
							"chiffre"=>$_POST['chiffre'],
							"signataire"=>$_POST['signataire']
						);	
			$id=$_SESSION['current_company'];/**setting**/
			$this->db->where('id',$id);
			$this->db->set($data);
			$update=$this->db->update('core');	
			}else{
			$data=array(
						"email"=>$_POST['email'],
						"language"=>$_POST['language'],
						"date_format"=>$_POST['date_format'],
						"date_time_format"=>$_POST['date_time_format'],
						"currency"=>$_POST['currency'],
						"echeance"=>$_POST['echeance'],
						"money_currency_position"=>$_POST['money_currency_position'],
						"display_logo_facture"=>$displayfacture,
						"display_logo_devis"=>$displaydevis,
						"display_logo_commande"=>$displaycommande,
						"display_logo_livraison"=>$displaylivraison,
						"display_logo_avoir"=>$displayavoir,
						"chiffre"=>$_POST['chiffre'],
						"signataire"=>$_POST['signataire']
						);		
			$this->db->where('id_vcompanies',$id);
			$this->db->set($data);
			$update=$this->db->update('core');
			}
			if($update){
			$this->session->set_flashdata('message', 'success:'.$this->lang->line('messages_save_settings_success'));
			redirect('settings');
			}else{
				$this->session->set_flashdata('message', 'error:'.$this->lang->line('messages_save_settings_error'));
				redirect('settings');
			}
		}
	}
	function settings_reset($template = FALSE){
		$this->load->helper('file');
		$option=array("id_vcompanies"=>$_SESSION['current_company']);
		$settings = Setting::find($option);
		if($template){
			$data = read_file('./application/views/'.$settings->template.'/templates/default/'.$template.'.html');
			if(write_file('./application/views/'.$settings->template.'/templates/'.$template.'.html', $data)){
				$this->session->set_flashdata('message', 'success:'.$this->lang->line('messages_reset_mail_body_success'));
				redirect('settings/templates');
			}
		}	
	}
	function templates($template = "invoice"){
		$this->load->helper('file');
		$option=array("id_vcompanies"=>$_SESSION['current_company']);
		$settings = Setting::find($option);
		$filename = './application/views/'.$settings->template.'/templates/email_'.$template.'.html';
		$this->view_data['folder_path'] = '/application/views/'.$settings->template.'/templates/ ';
		if (!is_writable($filename)) {
		    $this->view_data['not_writable'] = true;
		}else{
			$this->view_data['not_writable'] = false;
		}
		$this->view_data['breadcrumb'] = $this->lang->line('application_templates');
		$this->view_data['breadcrumb_id'] = "templates";

		$this->view_data['breadcrumb_sub'] = $this->lang->line('application_'.$template);
		$this->view_data['breadcrumb_sub_id'] = $template;
		
		if($_POST){
			$data = html_entity_decode($_POST["mail_body"]);

			unset($_POST["mail_body"]);
			unset($_POST["send"]);
			
			$settings->update_attributes($_POST);
			if(write_file('./application/views/'.$settings->template.'/templates/email_'.$template.'.html', $data)){
			$this->session->set_flashdata('message', 'success:'.$this->lang->line('messages_save_template_success'));
			redirect('settings/templates/'.$template);
				}else{
					$this->session->set_flashdata('message', 'error:'.$this->lang->line('messages_save_template_error'));
					redirect('settings/templates/'.$template);
					}
			}else{

			$this->view_data['email'] = read_file('./application/views/'.$settings->template.'/templates/email_'.$template.'.html');
			$this->view_data['template'] = $template;
			$this->view_data['template_files'] = get_filenames('./application/views/'.$settings->template.'/templates/default/');
			$this->view_data['template_files'] = str_replace('.html', '', $this->view_data['template_files']);
			$this->view_data['template_files'] = str_replace('email_', '', $this->view_data['template_files']);
			$option=array("id_vcompanies"=>$_SESSION['current_company']);
			$this->view_data['settings'] = Setting::find($option);
			$this->view_data['form_action'] = 'settings/templates/'.$template;
			$this->content_view = 'settings/templates';
		}
	}
	function invoice_templates($dest = false, $template = FALSE){
		$this->load->helper('file');
		$option=array("id_vcompanies"=>$_SESSION['current_company']);
		$settings = Setting::find($option);
		$filename = './application/views/'.$settings->template.'/templates/invoice/default.php';
		$this->view_data['folder_path'] = '/application/views/'.$settings->template.'/templates/ ';
		$this->view_data['breadcrumb'] = $this->lang->line('application_pdf_templates');
		$this->view_data['breadcrumb_id'] = "pdf_templates";
		if($_POST)
		{
			unset($_POST["send"]);
			if(!isset($_POST["pdf_path"])){$_POST["pdf_path"] = 0;}
			$settings->update_attributes($_POST);
			if($settings){
					$this->session->set_flashdata('message', 'success:'.$this->lang->line('messages_save_template_success'));
					redirect('settings/invoice_templates/');
				}else{
					$this->session->set_flashdata('message', 'error:'.$this->lang->line('messages_save_template_error'));
					redirect('settings/invoice_templates/');
					}
			}else{
				if($dest && $template){
				$DBdest = $dest."_pdf_template";
				$attr = array();
				$attr[$DBdest] = 'templates/'.$dest.'/'.$template;
				$settings->update_attributes($attr);
				redirect('settings/invoice_templates');
			}else{
				$this->view_data['invoice_template_files'] = get_filenames('./application/views/'.$settings->template.'/templates/invoice/');
				$this->view_data['invoice_template_files'] = str_replace('.php', '', $this->view_data['invoice_template_files']);
				$this->view_data['estimate_template_files'] = get_filenames('./application/views/'.$settings->template.'/templates/estimate/');
				$this->view_data['estimate_template_files'] = str_replace('.php', '', $this->view_data['estimate_template_files']);
				$option=array("id_vcompanies"=>$_SESSION['current_company']);
				$this->view_data['settings'] = Setting::find($option);
				$active_template = end(explode("/", $this->view_data['settings']->invoice_pdf_template));
				$this->view_data['active_template'] = str_replace('.php', '', $active_template);
				$active_estimate_template = end(explode("/", $this->view_data['settings']->estimate_pdf_template));
				$this->view_data['active_estimate_template'] = str_replace('.php', '', $active_estimate_template);
				$this->view_data['form_action'] = 'settings/invoice_templates/'.$template;
				$this->content_view = 'settings/invoice_templates';
			}
		}	
	}
	
	function editpaiement($id) {
		if ($_POST) {
            $ins = array(
                'name' => $this->input->post('name'),
				'description'=>$this->input->post('description'),
                'update_date'=>date("Y-m-d h:i:s"),
            );
			$this->db->where('id',$id);
			$this->db->set($ins);
			$this->db->update('ref_type_occurences');
            redirect('settings/gestionCommercial');
        } else {
            $this->theme_view = 'modal';
            $this->view_data['title'] = $this->lang->line('application_edit');
            $this->view_data['form_action'] = 'settings/editsociete/'.$id;
			$this->db->where('id',$id);
			$data=$this->db->get('ref_type_occurences');
			$this->view_data['data']=$data->result()[0];
            $this->content_view = 'settings/addref';
        }
	}
	function deletepaiement($id){
		$this->db->where('id',$id);
		$this->db->set('visible',0);
		$this->db->update('ref_type_occurences');
		redirect('settings/gestionCommercial');
	}
	function Timbre(){
		if($_POST){
			unset($_POST['send']);
			unset($_POST['nomdevis']);
			unset($_POST['nomfacture']);
			unset($_POST['nomcommande']);
			unset($_POST['nomabonnement']);
			unset($_POST['nomlivraison']);
			unset($_POST['nomproject']);
			unset($_POST['nomavoir']);
			unset($_POST['estimate']);
			unset($_POST['invoice']);
			unset($_POST['subscription']);
			unset($_POST['commandePrefix']);
			unset($_POST['livraisonPrefix']);
			unset($_POST['projectPrefix']);
			unset($_POST['avoirPrefix']);
			unset($_POST['avoir']);
			$settings=setting::find(array('id_vcompanies'=>$_SESSION['current_company']));
			$settings->update_attributes($_POST);
			if($settings){
			$this->session->set_flashdata('message', 'success:'.$this->lang->line('messages_save_settings_success'));
			redirect('settings/gestionCommercial');
			}else{
				$this->session->set_flashdata('message', 'error:'.$this->lang->line('messages_save_settings_error'));
				redirect('settings/gestionCommercial');
			}
		}
	}
	function ajoutecheance(){
		if($_POST){
			unset($_POST['send']);
			$_POST['id_type']=20;
			$_POST['created_date']=date('Y-m-d');
			$_POST['visible']=1;
			$this->db->insert('ref_type_occurences',$_POST);
			redirect('settings/gestionCommercial');
		}else{
			$this->theme_view = 'modal';
            $this->view_data['title'] = $this->lang->line('application-add');
            $this->view_data['form_action'] = 'settings/ajoutecheance';
            $this->content_view = 'settings/addechance';
		}
	}
	function editecheance($id){
		if($_POST){
			$_POST['update_date']=date('Y-m-d');
			$_POST['updated_by']=$this->user->id;
			unset($_POST['send']);
			$this->db->where('id',$id);
			$this->db->set($_POST);
			$this->db->update('ref_type_occurences');
			redirect('settings/gestionCommercial');
		}else{
			$this->theme_view = 'modal';
            $this->view_data['title'] = $this->lang->line('application-add');
            $this->view_data['form_action'] = 'settings/editecheance/'.$id;
			$this->db->where('id',$id);
			$echeance=$this->db->get('ref_type_occurences')->result()[0];
			$this->view_data['data']=$echeance;
            $this->content_view = 'settings/addechance';
		}
	}
	function deleteecheance($id){
		$this->db->where('id',$id);
		$this->db->set('visible',0);
		$this->db->update('referentiels');
		redirect('settings/gestionCommercial');
	}
	function achat(){
		$cond=array("visible"=>1,
				   "id_type"=>1);
		$this->db->where($cond);
            $livraison = $this->db->get('ref_type_occurences')->result();
			$cond=array("visible"=>1,
						"id_type"=>2);
						$this->db->where($cond);
						$commande=$this->db->get('referentiels')->result();
		$this->view_data['breadcrumb'] = $this->lang->line('application_ref_achat');
		$this->view_data['breadcrumb_id'] = "achat";
		$this->view_data['livraison']=$livraison;
		$this->view_data['commande']=$commande;
		$this->content_view = 'settings/referentielAchat';
	}
	function vente(){
		
		$cond=array("visible"=>1,
				   "id_type"=>3);
		$this->db->where($cond);
        $facture = $this->db->get('ref_type_occurences')->result();
		$cond=array("visible"=>1,
					"id_type"=>4);
					$this->db->where($cond);
		$devis=$this->db->get('ref_type_occurences')->result();
		$cond=array("visible"=>1,
					"id_type"=>5);
					$this->db->where($cond);
		$commande=$this->db->get('ref_type_occurences')->result();
		$cond=array("visible"=>1,
					"id_type"=>8);
					$this->db->where($cond);
		$payment=$this->db->get('ref_type_occurences')->result();
		$cond=array("visible"=>1,
					"id_type"=>25);
					$this->db->where($cond);
		$avoir=$this->db->get('ref_type_occurences')->result();
		$this->view_data['breadcrumb'] = $this->lang->line('application_ref_vente');
		$this->view_data['breadcrumb_id'] = "vente";
		$this->view_data['facture']=$facture;
		$this->view_data['devis']=$devis;
		$this->view_data['commande']=$commande;
		$this->view_data['payment']=$payment;
		$this->view_data['avoir']=$avoir;
		$this->content_view = 'settings/referentielVente';
		
	}
	
	function calendar(){
		$this->view_data['breadcrumb'] = $this->lang->line('application_calendar');
		$this->view_data['breadcrumb_id'] = "calendar";

		if($_POST){
						
		unset($_POST['send']);
		$option=array("id_vcompanies"=>$_SESSION['current_company']);
		$settings = Setting::find($option);
		$settings->update_attributes($_POST);
		if($settings){
		$this->session->set_flashdata('message', 'success:'.$this->lang->line('messages_save_settings_success'));
 		redirect('settings/calendar');
			}else{
				$this->session->set_flashdata('message', 'error:'.$this->lang->line('messages_save_settings_error'));
	 			redirect('settings/calendar');
	 			}
 		}else{
 			$option=array("id_vcompanies"=>$_SESSION['current_company']);
 		$this->view_data['settings'] = Setting::find($option);
		$this->view_data['form_action'] = 'settings/calendar';
		$this->content_view = 'settings/calendar';
 		}
	}
	
	function ticket(){
		$this->view_data['breadcrumb'] = $this->lang->line('application_ticket');
		$this->view_data['breadcrumb_id'] = "ticket";
		$this->view_data['imap_loaded'] = false;
		if(extension_loaded('mysql')){
			$this->view_data['imap_loaded'] = true;
		}
		if($_POST){
						
		unset($_POST['send']);
		if(!isset($_POST['ticket_config_active'])){$_POST['ticket_config_active'] = "0";}
		if(!isset($_POST['ticket_config_delete'])){$_POST['ticket_config_delete'] = "0";}
		if(!isset($_POST['ticket_config_ssl'])){$_POST['ticket_config_ssl'] = "0";}
		if(!isset($_POST['ticket_config_imap'])){$_POST['ticket_config_imap'] = "0";}
		$option=array("id_vcompanies"=>$_SESSION['current_company']);
		$settings = Setting::find($option);
		$settings->update_attributes($_POST);
		if($settings){
		$this->session->set_flashdata('message', 'success:'.$this->lang->line('messages_save_settings_success'));
 		redirect('settings/ticket');
			}else{
				$this->session->set_flashdata('message', 'error:'.$this->lang->line('messages_save_settings_error'));
	 			redirect('settings/ticket');
	 			}
 		}else{
 			$option=array("id_vcompanies"=>$_SESSION['current_company']);
 		$this->view_data['settings'] = Setting::find($option);
 		$this->view_data['types'] = Type::find('all', array('conditions' => array('inactive = ?', '0')));
 		$this->view_data['owners'] = User::find('all', array('conditions' => array('status = ?', 'active')));
		$this->view_data['form_action'] = 'settings/ticket';
		$this->content_view = 'settings/ticket';
 		}
	}
	function ticket_type($id = FALSE, $condition = FALSE){
		if($condition == "delete"){
			$_POST["inactive"] = "1";
			$type = Type::find_by_id($id);
			$type->update_attributes($_POST);
		}else{

			if($_POST){
						
			unset($_POST['send']);
		
			if($id){
				$type = Type::find_by_id($id);
				$type->update_attributes($_POST);
				
			}else{
				$type = Type::create($_POST);
			}
			if($type){
			$this->session->set_flashdata('message', 'success:'.$this->lang->line('messages_save_settings_success'));
	 		redirect('settings/ticket');
				}else{
					$this->session->set_flashdata('message', 'error:'.$this->lang->line('messages_save_settings_error'));
		 			redirect('settings/ticket');
		 			}
	 		}else{
	 		if($id){
	 			$this->view_data['type'] = Type::find_by_id($id);
	 		}
	 		
	 		$this->view_data['title'] = $this->lang->line('application_type');
			$this->view_data['form_action'] = 'settings/ticket_type/'.$id;
			$this->content_view = 'settings/_ticket_type';
	 		}
 		}
 		$this->theme_view = 'modal_nojs';
	}
	
	function testpostmaster(){
			$option=array("id_vcompanies"=>$_SESSION['current_company']);
			$emailconfig = Setting::find($option);
			$config['login'] = $emailconfig->ticket_config_login;
			$config['pass'] = $emailconfig->ticket_config_pass;
			$config['host'] = $emailconfig->ticket_config_host;
			$config['port'] = $emailconfig->ticket_config_port;
			$config['mailbox'] = $emailconfig->ticket_config_mailbox;

			if($emailconfig->ticket_config_imap == "1"){$flags = "/imap";}else{$flags = "/pop3";}
			if($emailconfig->ticket_config_ssl == "1"){$flags .= "/ssl";}

			$config['service_flags'] = $flags.$emailconfig->ticket_config_flags; 

			$this->load->library('peeker_connect');
			$this->peeker_connect->initialize($config);
			
			if($this->peeker_connect->is_connected()){
				$this->view_data['msgresult'] = "success";
				$this->view_data['result'] = "Connection to email mailbox successful!";
			}else{
				$this->view_data['msgresult'] = "error";
				$this->view_data['result'] = "Connection to email mailbox not successful!";
			}
			$this->peeker_connect->message_waiting();
			
			$this->peeker_connect->close();
			$this->view_data['trace'] = $this->peeker_connect->trace();
			$this->content_view = 'settings/_testpostmaster';
			$this->theme_view = 'modal_nojs';
			$this->view_data['title'] = $this->lang->line('application_postmaster_test');
	}
	
	function customize(){
		$this->view_data['breadcrumb'] = $this->lang->line('application_customize');
		$this->view_data['breadcrumb_id'] = "customize";

		$this->load->helper('file');
		$option=array("id_vcompanies"=>$_SESSION['current_company']);
		$this->view_data['settings'] = Setting::find($option);
		if($_POST){
		$data = $_POST['css-area'];
		if(write_file('./assets/'.$this->view_data['settings']->template.'/css/user.css', $data)){
		$this->session->set_flashdata('message', 'success:'.$this->lang->line('messages_save_customize_success'));
 		redirect('settings/customize');
			}else{
				$this->session->set_flashdata('message', 'error:'.$this->lang->line('messages_save_customize_error'));
	 			redirect('settings/customize');
	 			}
 		}else{
 			$this->view_data['writable'] = FALSE;
		if (is_writable('./assets/'.$this->view_data['settings']->template.'/css/user.css')) {
    		$this->view_data['writable'] = TRUE;
		}
 		$this->view_data['css'] = read_file('./assets/'.$this->view_data['settings']->template.'/css/user.css');
		$this->view_data['form_action'] = 'settings/customize';
		$this->content_view = 'settings/customize';
 		}
	}

	function registration(){
		if($_POST){
				unset($_POST['send']);

				if(!isset($_POST['registration'])){$_POST['registration'] = 0;}
				if(!empty($_POST["access"])){
				$_POST["default_client_modules"] = implode(",", $_POST["access"]);
				}else{
					$_POST["default_client_modules"] = "";
				}
				unset($_POST["access"]);
				$option=array("id_vcompanies"=>$_SESSION['current_company']);
				$settings = Setting::find($option);
				$settings->update_attributes($_POST);
				
	
			if($settings){
			$this->session->set_flashdata('message', 'success:'.$this->lang->line('messages_save_settings_success'));
	 		redirect('settings/registration');
	 		}
		}
		$this->view_data['breadcrumb'] = $this->lang->line('application_registration');
		$this->view_data['breadcrumb_id'] = "registration";

		$this->view_data['client_modules'] = Module::find('all', array('order' => 'sort asc', 'conditions' => array('type = ?', 'client')));
        $option=array("id_vcompanies"=>$_SESSION['current_company']);
		$this->view_data['settings'] = Setting::find($option);
        $this->view_data['form_action'] = 'settings/registration';
		$this->content_view = 'settings/registration';
	}

	function user_delete($user = FALSE){

		if($this->user->id != $user) {
		$user = User::find_by_id($user); 
		$user->status = 'deleted';
		$user->save();
		$this->session->set_flashdata('message', 'success:'.$this->lang->line('messages_delete_user_success'));
		}else{
		$this->session->set_flashdata('message', 'error:'.$this->lang->line('messages_delete_user_error'));
		}
		redirect('settings/listUser');
	}

	function user_create(){
		if($_POST){	
			$config['upload_path'] = './files/media/';
			$config['encrypt_name'] = TRUE;
			$config['allowed_types'] = 'gif|jpg|jpeg|png';
			$config['max_width'] = '180';
			$config['max_height'] = '180';
			$this->load->library('upload', $config);
			if ( $this->upload->do_upload())
			{
				$data = array('upload_data' => $this->upload->data());
				$_POST['userpic'] = $data['upload_data']['file_name'];
			}
			unset($_POST['file-name']);
			unset($_POST['send']);
			unset($_POST['confirm_password']);
			$_POST['status'] = 'active';
			//default modules 
			$Dmodules = Module::find('all', array('order' => 'sort asc', 'conditions' => array('type != ? and name != ? and default_module = ?', 'client','settings','1')));

			//all modules 
			$modules = Module::find('all', array('order' => 'sort asc', 'conditions' => array('type != ? and name != ?', 'client','settings')));  
			// all submenus 
			$submenus = $this->modules_sous_model->getAll(); //Modules_sous::find('all', array('order' => 'sort asc', 'conditions' => array('type != ?', 'client')));
            $allModule = $allSubmenu = $defaultModule = "";
			foreach($modules as $val){
				$allModule = $allModule.$val->id.','; 
			}
			foreach($submenus as $val){
				$allSubmenu = $allSubmenu.$val->id.','; 
			}
			foreach($Dmodules as $val){
				$defaultModule = $defaultModule.$val->id.','; 
			}

            $defaultModule = trim($defaultModule, ',');
            $allModule = trim($allModule, ',');
            $allSubmenu = trim($allSubmenu, ',');

            //Create acces table
			if($_POST['accessCompany'] == null){
				$_POST['accessCompany'][0] = $_SESSION['current_company'] ; 
			}
			$post_data = array(
				'email'=> $_POST['email'],
				'firstname'=> $_POST['firstname'],
				'lastname'=> $_POST['lastname'],
				'admin' => $_POST['admin'],
				'status' => $_POST['status'],
			);
            unset($post_data['accessCompany']);

            unset($post_data['accessCompany']);
            $post_data['hashed_password'] = hash_password($_POST['password']);;
			//var_dump($_POST);exit;
			unset($post_data['password']);
            
			//create new salarie
			if($_POST['salaries'])
			{
				$this->load->database();
				
				$data_salarie = array(
					'mail'=> $_POST['email'],
					'nom'=> $_POST['firstname'],
					'prenom'=> $_POST['lastname'],
				);
				$this->db->set($data_salarie);
				$query = $this->db->insert('salaries');
				$lastId= $this->salaries->getLastId(); 
				$post_data['salaries_id']=$lastId;
			}
			
			$post_data = array_map('htmlspecialchars', $post_data);
			//$user = User::create($_POST);
			//$this->session->set_flashdata('message', 'success:'.$this->lang->line('messages_create_user_success'));
			$this->db->insert('users',$post_data);
            $data['user_id'] = $this->db->insert_id();

            foreach($_POST['accessCompany'] as $val){
                //if user : admin : all the access
                if($_POST['admin'] == 1){
                    $data['company_id'] = $val;
                    $data['menu'] = $allModule;
                    $data['submenu'] = $allSubmenu;
                } else {
                    $data['company_id'] = $val;
                    $data['menu'] = $defaultModule;
                }
                $accesTable = AccesRigth::create($data);
            }

			

            redirect('settings/listUser');
			}else
			{
				$this->theme_view = 'modal';
				$this->view_data['title'] = $this->lang->line('application_create_user');
				// all modules 
				$this->view_data['modules'] = Module::find('all', array('order' => 'sort asc', 'conditions' => array('type != ? and name != ?', 'client','settings')));  
				//all companies 
				//$this->view_data['companies'] = V_companie::find('all'); 
				// all submenus 
				//$this->view_data['submenus'] = Submenu::find('all', array('order' => 'sort asc', 'conditions' => array('type != ?', 'client')));
				$this->view_data['form_action'] = 'settings/user_create/';
				$this->content_view = 'settings/_usernew';
			}
	}

	function user_update($user = FALSE){
 		$user = User::find($user);
 		if($_POST){
			$config['upload_path'] = './files/media/';
			$config['encrypt_name'] = TRUE;
			$config['allowed_types'] = 'gif|jpg|jpeg|png';
			$config['max_width'] = '180';
			$config['max_height'] = '180';
			$this->load->library('upload', $config);
			if ( $this->upload->do_upload())
			{
				$data = array('upload_data' => $this->upload->data());
				$_POST['userpic'] = $data['upload_data']['file_name'];
			}				
			unset($_POST['file-name']);
			unset($_POST['send']);
			unset($_POST['confirm_password']);
			// all modules 
			$modules = Module::find('all', array('order' => 'sort asc', 'conditions' => array('type != ? and name != ?', 'client','settings')));  
			// all submenus 
			
			$submenus = Submenu::find('all', array('order' => 'sort asc', 'conditions' => array('type != ?', 'client')));	
			//default modules 
			$Dmodules = Module::find('all', array('order' => 'sort asc', 'conditions' => array('type != ? and name != ? and default_module = ?', 'client','settings','1'))); 
			foreach($modules as $val){
				$allModule = $allModule.$val->id.','; 
			}
			foreach($submenus as $val){
				$allSubmenu = $allSubmenu.$val->id.','; 
			}
			foreach($Dmodules as $val){
				$defaultModule = $defaultModule.$val->id.','; 
			}
			//update the acces table 
			$this->view_data['companiesChosen'] = AccesRigth::find('all', array('conditions' => array('user_id=?',$user->id))); 
			//update acces table 
			foreach($_POST['accessCompany'] as $val){
				$accessExit = AccesRigth::find('all', array('conditions' => array('user_id=? and company_id=?',$user->id,$val))); 
				if(!empty($accessExit)){
				}else {
					$data['user_id'] = $user->id; 
					//if user : admin : all the access 
					if($_POST['admin'] == 1){
						$data['company_id'] = $val; 
						$data['menu'] = $allModule;
						$data['submenu'] = $allSubmenu; 
					} else {
						$data['company_id'] = $val; 
						$data['menu'] = $defaultModule; 
					}
					$accesTable = AccesRigth::create($data);
				}
			}
			//delete uncheked access
			foreach($this->view_data['companiesChosen'] as $chosen){
				$exit = false;
				foreach($_POST['accessCompany'] as $val){
					if($chosen->company_id == $val){
						$exit = true; 
						break; 
					}
				}
				if($exit == false){
					//delete this access 
					$accesstodelete= AccesRigth::find(array('conditions' => array('user_id=? and company_id=?',$user->id,$chosen->company_id))); 
					$accesstodelete->delete(); 
				}
			}
			unset($_POST['accessCompany']);
			$_POST = array_map('htmlspecialchars', $_POST);
			if(empty($_POST['password'])){ unset($_POST['password']);}
			if($_POST['admin'] == "0" && $_POST['username'] == "Admin"){ $_POST['admin'] = "1";}
			$user->update_attributes($_POST);
			$this->session->set_flashdata('message', 'success:'.$this->lang->line('messages_save_user_success'));
			redirect('settings/users');
 		}else{
			// the companies which has access to them
			$this->view_data['companiesChosen'] = AccesRigth::find('all', array('conditions' => array('user_id=?',$user->id)));  
			//all companies 
			$this->view_data['companies'] = V_companie::find('all');
			foreach($this->view_data['companies'] as $comp){
				foreach($this->view_data['companiesChosen'] as $compChosen){
					if($comp->id == $compChosen->company_id){
						$comp->id= $compChosen; 
					}
				}
			}
			$this->view_data['user'] = $user;
			$this->theme_view = 'modal';
			$this->view_data['modules'] = Module::find('all', array('order' => 'sort asc', 'conditions' => array('type != ?', 'client')));
			$this->view_data['title'] = $this->lang->line('application_edit_user');
			$this->view_data['form_action'] = 'settings/user_update/'.$user->id;
			$this->content_view = 'settings/_userform';
 		}	
	}
	
	function logs($val = FALSE){
		$this->view_data['breadcrumb'] = $this->lang->line('application_logs');
		$this->view_data['breadcrumb_id'] = "logs";

		$this->load->helper('file');
		if($val == "clear"){
				delete_files('./application/logs/');		
				$this->session->set_flashdata('message', 'success:'.$this->lang->line('messages_log_cleared'));
	 			redirect('settings/logs');

 		}else{
 		$lognames =	get_filenames('./application/logs/');
 		$lognames = array_diff($lognames, array("index.html"));
 		$this->view_data['logs'] = "";
 		$i=0;
 		krsort($lognames);
 		foreach ($lognames as $value) if ($i < 6)  {
 			$this->view_data['logs'] .= read_file('./application/logs/'.$value);
 			$i +=1;
 		}
 		$this->view_data['logs'] = explode("\n", $this->view_data['logs']);
 		$this->view_data['logs'] = array_diff($this->view_data['logs'], array("<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>", ""));
 		rsort($this->view_data['logs']);
		 $option=array("id_vcompanies"=>$_SESSION['current_company']);
 		$this->view_data['settings'] = Setting::find($option);
		$this->view_data['form_action'] = 'settings/logs';
		$this->content_view = 'settings/logs';
 		}
	}
  
	function smtp_settings(){
		$id=$_SESSION['current_company'];
		$this->db->where('id_company',$id);
		$configuration=$this->db->get('smtp_conf')->result();
		$this->load->helper('file');	
		foreach ($configuration as $key=>$val) {
			$crypto = $val->smtp_crypto;
			$data = '<?php if ( ! defined("BASEPATH")) exit("No direct script access allowed");
			$config["useragent"]        = "'.$val->useragent.'";      
			$config["protocol"]         = "'.$val->protocol.'";
			$config["mailpath"]         = "'.$val->mailpath.'";
			$config["smtp_host"]        = "'.$val->smtp_host.'";
			$config["smtp_user"]        = "'.$val->smtp_user.'";
			$config["smtp_pass"]        = "'.$val->smtp_pass.'";
			$config["smtp_port"]        = "'.$val->smtp_port.'";
			$config["smtp_timeout"]     = "'.$val->smtp_timeout.'";      
			$config["smtp_crypto"]      = "'.$crypto.'";    
			$config["smtp_debug"]       = "'.$val->smtp_debug.'";      
			$config["wordwrap"]         = '.$val->wordwrap.';
			$config["wrapchars"]        = '.$val->wrapchars.';
			$config["mailtype"]         = "'.$val->mailtype.'";          
			$config["charset"]          = "'.$val->charset.'";
			$config["validate"]         = '.$val->validate.';
			$config["priority"]         = '.$val->priority.';                
			$config["crlf"]             = "'.$val->crlf.'";                     
			$config["newline"]          = "'.$val->newline.'";                    
			$config["bcc_batch_mode"]   = '.$val->bcc_batch_mode.';
			$config["bcc_batch_size"]   = '.$val->bcc_batch_size.';
			';
		}	
		write_file('application/config/email.php', $data);
		$this->config->load('email');
		if(isset($_POST["protocol"])){
		$conf=array("useragent"=>"PHPMailer",
			"protocol"=>$_POST['protocol'],
			"mailpath"=>"/usr/sbin/sendmail",
			"smtp_host"=>$_POST["smtp_host"],
			"smtp_user"=>$_POST["smtp_user"],
			"smtp_pass"=>addslashes($_POST["smtp_pass"]),
			"smtp_port"=>$_POST["smtp_port"],
			"smtp_timeout"=>$_POST["smtp_timeout"],
			"smtp_crypto"=>$_POST["smtp_crypto"],
			"smtp_debug"=>$_POST["smtp_debug"],
			"wordwrap"=>"true",
			"wrapchars"=>"76",
			"mailtype"=>"html",
			"charset"=>"utf-8",
			"validate"=>"true",
			"priority"=>"3",
			"crlf"=>"\r\n",
			"newline"=>"\r\n",
			"bcc_batch_mode"=>"false",
			"bcc_batch_size"=>"200",
			"id_company"=>$_SESSION['current_company']
			);
			
            $this->db->where('id_company',$id);
						$this->db->set($conf);
						$this->db->update('smtp_conf');
						
			redirect('settings/smtp_settings/', 'refresh'); 
		}
		else{
		$this->view_data['breadcrumb'] = $this->lang->line('application_smtp_settings');
		$this->view_data['breadcrumb_id'] = "smtpsettings";
		$option=array("id_vcompanies"=>$_SESSION['current_company']);
		$this->view_data['settings'] = Setting::find($option); 		
		$this->view_data['form_action'] = 'settings/smtp_settings/';
		$this->view_data['form_action2'] = 'settings/sendTestMail/';
		$this->content_view = 'settings/smtp_settings';
		}
		
	}
	
	function sendTestMail(){
		sendMail('','Test','',$_POST['dist'],'','Ceci est un mail de test.','settings/smtp_settings','settings/smtp_settings') ; 		
	}

	/*function ajouter(){
		if ($_POST) {
            $data = array(
				'id_type'=>1,
                'name' => $this->input->post('name'),
				'description'=>$this->input->post('description'),
                'created_date'=>date("Y-m-d h:i:s"),
				'visible'=>1
            );
            $this->db->insert('referentiels', $data);
            redirect('settings/achat');
        } else {
            $this->theme_view = 'modal';
            $this->view_data['title'] = $this->lang->line('application-add');
            $this->view_data['form_action'] = 'settings/ajouter';
            $this->content_view = 'settings/addref';
        }
	}
	
	function deleteref($id) {
		$this->db->where('id',$id);
		$this->db->set('visible',0);
		$this->db->update('referentiels');
		redirect('settings/achat');
	}
	
	function edit($id) {
		if ($_POST) {
            $ins = array(
                'name' => $this->input->post('name'),
				'description'=>$this->input->post('description'),
                'update_date'=>date("Y-m-d h:i:s"),
            );
			$this->db->where('id',$id);
			$this->db->set($ins);
			$this->db->update('referentiels');
            redirect('settings/achat');
        } else {
            $this->theme_view = 'modal';
            $this->view_data['title'] = $this->lang->line('application_edit');
            $this->view_data['form_action'] = 'settings/edit/'.$id;
			$this->db->where('id',$id);
			$data=$this->db->get('referentiels');
			$this->view_data['data']=$data->result()[0];
            $this->content_view = 'settings/addref';
        }
	}			
	
	function ajoutcomm(){
		if ($_POST) {
            $data = array(
				'id_type'=>2,
                'name' => $this->input->post('name'),
				'description'=>$this->input->post('description'),
                'created_date'=>date("Y-m-d h:i:s"),
				'visible'=>1
            );
            $this->db->insert('referentiels', $data);
            redirect('settings/achat');
        } else {
            $this->theme_view = 'modal';
            $this->view_data['title'] = $this->lang->line('application-add');
            $this->view_data['form_action'] = 'settings/ajoutcomm';
            $this->content_view = 'settings/addref';
        }
	}
	
	function editcomm($id){
		if ($_POST) {
            $ins = array(
                'name' => $this->input->post('name'),
				'description'=>$this->input->post('description'),
                'update_date'=>date("Y-m-d h:i:s"),
            );
			$this->db->where('id',$id);
			$this->db->set($ins);
			$this->db->update('referentiels');
            redirect('settings/achat');
        } else {
            $this->theme_view = 'modal';
            $this->view_data['title'] = $this->lang->line('application_edit');
            $this->view_data['form_action'] = 'settings/editcomm/'.$id;
			$this->db->where('id',$id);
			$data=$this->db->get('referentiels');
			$this->view_data['data']=$data->result()[0];
            $this->content_view = 'settings/addref';
        }
	}
	
	function Ajoutfacture(){
		if ($_POST) {
            $data = array(
				'id_type'=>3,
                'name' => $this->input->post('name'),
				'description'=>$this->input->post('description'),
                'created_date'=>date("Y-m-d h:i:s"),
				'visible'=>1
            );
			$this->db->insert('referentiels', $data);
            redirect('settings/vente');
        } else {
            $this->theme_view = 'modal';
            $this->view_data['title'] = $this->lang->line('application-add');
            $this->view_data['form_action'] = 'settings/Ajoutfacture';
            $this->content_view = 'settings/addref';
        }
	}
	
	function editFacture($id){
		if ($_POST) {
            $ins = array(
                'name' => $this->input->post('name'),
				'description'=>$this->input->post('description'),
                'update_date'=>date("Y-m-d h:i:s"),
            );
			$this->db->where('id',$id);
			$this->db->set($ins);
			$this->db->update('referentiels');
            redirect('settings/vente');
        } else {
            $this->theme_view = 'modal';
            $this->view_data['title'] = $this->lang->line('application_edit');
            $this->view_data['form_action'] = 'settings/editFacture/'.$id;
			$this->db->where('id',$id);
			$data=$this->db->get('referentiels');
			$this->view_data['data']=$data->result()[0];
            $this->content_view = 'settings/addref';
        }
	}
	
	function Ajoutdevis(){
	 if ($_POST) {
            $data = array(
				'id_type'=>4,
                'name' => $this->input->post('name'),
				'description'=>$this->input->post('description'),
                'created_date'=>date("Y-m-d h:i:s"),
				'visible'=>1
            );
            $this->db->insert('referentiels', $data);
            redirect('settings/vente');
        } else {
            $this->theme_view = 'modal';
            $this->view_data['title'] = $this->lang->line('application-add');
            $this->view_data['form_action'] = 'settings/Ajoutdevis';
            $this->content_view = 'settings/addref';
        }
	}
	
	function deletevente($id){
		$this->db->where('id',$id);
		$this->db->set('visible',0);
		$this->db->update('referentiels');
		redirect('settings/vente');
	}
	
	function Ajoutboncom(){
	 if ($_POST) {
            $data = array(
				'id_type'=>5,
                'name' => $this->input->post('name'),
				'description'=>$this->input->post('description'),
                'created_date'=>date("Y-m-d h:i:s"),
				'visible'=>1
            );
            $this->db->insert('referentiels', $data);
            redirect('settings/vente');
        } else {
            $this->theme_view = 'modal';
            $this->view_data['title'] = $this->lang->line('application-add');
            $this->view_data['form_action'] = 'settings/Ajoutboncom';
            $this->content_view = 'settings/addref';
        }
	}
		
	function AjoutForme(){
	 if ($_POST) {
            $data = array(
				'id_type'=>6,
                'name' => $this->input->post('name'),
				'description'=>$this->input->post('description'),
                'created_date'=>date("Y-m-d h:i:s"),
				'visible'=>1
            );
            $this->db->insert('referentiels', $data);
            redirect('settings/societe');
        } else {
            $this->theme_view = 'modal';
            $this->view_data['title'] = $this->lang->line('application-add');
            $this->view_data['form_action'] = 'settings/AjoutForme';
            $this->content_view = 'settings/addref';
        }
	}
	
	function editDevise($id) {
		if ($_POST) { 
			//get devise 
			$devise = $this->referentiels->getReferentielsById($id); 
			//update descrption in ref 
			$d = array(
				'description'=>$this->input->post('description')
            );
			$this->db->where('id',$id);
			$this->db->set($d);
			$this->db->update('referentiels'); 
			//update descrption in refType 
			$data = array(
				'description'=>$this->input->post('description')
            );
			$this->db->where('name',$devise->name);
			$this->db->set($data);  
			$this->db->update('ref_type');  
			//update chiffre
			$IdRefType= $this->refType->getRefTypeByName($devise->name)->id;
			$dataRef = array(
				'name'=>$this->input->post('nbrChiffre')
            );
			$this->db->where('id_type',$IdRefType);
			$this->db->set($dataRef);
			$this->db->update('referentiels'); 
            redirect('settings/societe');
        } else {
            $this->theme_view = 'modal';
            $this->view_data['title'] = $this->lang->line('application_edit');
            $this->view_data['form_action'] = 'settings/editDevise/'.$id;
			$this->db->where('id',$id);
			$this->view_data['data']=$this->db->get('referentiels')->result()[0];
			$this->db->where('name',$this->view_data['data']->name);
			$refType=$this->db->get('ref_type')->result()[0];
			$this->db->where('id_type',$refType->id);
			$this->view_data['chiffre'] = $this->db->get('referentiels')->result()[0]->name; 
            $this->content_view = 'settings/addrefDevise';
        }
	}
	
	

	function ajoutTVA(){
	 	if ($_POST) {
            $data = array(
				'id_type'=>7,
                'name' => $this->input->post('name'),
				'description'=>$this->input->post('description'),
                'created_date'=>date("Y-m-d h:i:s"),
				'visible'=>1
            );
            $this->db->insert('referentiels', $data);
            redirect('settings/societe');
        } else {
            $this->theme_view = 'modal';
            $this->view_data['title'] = $this->lang->line('application-add');
            $this->view_data['form_action'] = 'settings/ajoutTVA';
            $this->content_view = 'settings/addref';
        }
	}
	
	function ajoutPaiement(){
	    if ($_POST) {
            $data = array(
				'id_type'=>8,
                'name' => $this->input->post('name'),
				'description'=>$this->input->post('description'),
                'created_date'=>date("Y-m-d h:i:s"),
				'visible'=>1
            );
            $this->db->insert('referentiels', $data);
            redirect('settings/gestionCommercial');
        } else {
            $this->theme_view = 'modal';
            $this->view_data['title'] = $this->lang->line('application-add');
            $this->view_data['form_action'] = 'settings/ajoutPaiement';
            $this->content_view = 'settings/addref';
        }
	}*/

	function addUnit() {
		if ($_POST) {
			$data = array(
				'description' => $this->input->post('description'),
				'value' => $this->input->post('value')
			);

			$this->db->insert('item_units', $data);
			redirect('settings/gestionCommercial');
		} else {
			$this->theme_view = 'modal';
            $this->view_data['title'] = $this->lang->line('application_add_unit');
            $this->view_data['form_action'] = 'settings/addUnit';
            $this->content_view = 'settings/_unit';
		}
	}

	function updateUnit($id) {
		if ($_POST) {
			$data = array(
				'description' => $this->input->post('description'),
				'value' => $this->input->post('value')
			);

			$this->db->where('id', $id);
			$this->db->update('item_units', $data);
			redirect('settings/gestionCommercial');
		} else {
			$this->db->where('id', $id);
			$unit = $this->db->get('item_units')->result()[0];

			$this->theme_view = 'modal';
            $this->view_data['title'] = $this->lang->line('application_update_unit');
            $this->view_data['form_action'] = 'settings/updateUnit/' . $id;
            $this->view_data['unit'] = $unit;
            $this->content_view = 'settings/_unit';
		}
	}

	function deleteUnit($id) {
		$this->db->where('id', $id);
		$this->db->delete('item_units');
		redirect('settings/gestionCommercial');
	}
	
	function ajoutCompteBancaire(){
	    if ($_POST) {
			$settings=setting::find(array('id_vcompanies'=>$_SESSION['current_company']));
			if($_POST['default_compteBancaire'] == 'on'){
				$data= array('compteBancaire' => $this->compteBancaire->getLastId() +1);  
				$settings->update_attributes($data); 
				unset($_POST['default_compteBancaire']); 
			}
			unset($_POST['send']); 
			$_POST['visible'] = 1; 
			$_POST['created_date'] = date("Y-m-d h:i:s"); 
            $this->db->insert('comptes_bancaires', $_POST);
            redirect('settings/compteBancaire');
        } else {
            $this->theme_view = 'modal';
            $this->view_data['title'] = $this->lang->line('application-add');
            $this->view_data['form_action'] = 'settings/ajoutCompteBancaire';
            $this->content_view = 'settings/addCompteBancaire';
        }
	}
	
	function editCompteBancaire($id) {
		if ($_POST) {
			$settings=setting::find(array('id_vcompanies'=>$_SESSION['current_company']));
			if($_POST['default_compteBancaire'] == 'on'){
				$data= array('compteBancaire' => $id);  
			} else {
				$data= array('compteBancaire' => 'NULL'); 
			}
			$settings->update_attributes($data); 
			unset($_POST['default_compteBancaire']); 
			unset($_POST['send']); 
			$_POST['update_date'] = date("Y-m-d h:i:s"); 
			$this->db->where('id',$id);
			$this->db->set($_POST);
			$this->db->update('comptes_bancaires');
            redirect('settings/compteBancaire');
        } else { 
			$this->view_data['settings']=setting::find(array('id_vcompanies'=>$_SESSION['current_company'])); 
            $this->theme_view = 'modal';
            $this->view_data['title'] = $this->lang->line('application_edit');
			$this->db->where('id',$id);
			$data = $this->db->get('comptes_bancaires');
			$this->view_data['data']=$data->result()[0];
            $this->content_view = 'settings/addCompteBancaire';
        }
	}
	
	// Ajouter un nouveau taux de TVA
	function ajoutTaxe()
	{
	    if ($_POST) {
            $data = array(
				'id_type'=>9,
                'name' => $this->input->post('name'),
				'description'=>$this->input->post('description'),
                'created_date'=>date("Y-m-d h:i:s"),
				'visible'=>1
            );
            $this->db->insert('ref_type_occurences', $data);
			
			$lastId = $this->referentiels->getLastId(); 
			// Mettre à jour la taxe TVA par défaut  
			if($_POST['tax'] == 'on'){
				$settings=setting::find(array('id_vcompanies'=>$_SESSION['current_company']));
				$data2= array('tax' => $lastId);
				$settings->update_attributes($data2);
			} 
			
			redirect('settings/refvente');
        } else 	{
			$this->view_data['settings']=setting::find(array('id_vcompanies'=>$_SESSION['current_company']));
            $this->theme_view = 'modal';
            $this->view_data['title'] = $this->lang->line('application-add');
            $this->view_data['form_action'] = 'settings/ajoutTaxe';
            $this->content_view = 'settings/referentiel/addreftaxe';
        }
	}
	
	// Supprimer un taux de TVA
	function deleteTaxe($id){
		$this->db->where('id',$id);
		$this->db->set('visible',0);
		$this->db->update('ref_type_occurences');
		redirect('settings/refvente');
	}
	
	function editTaxe($id) {
		if ($_POST) {
			// get the default tax  
			$settings=setting::find(array('id_vcompanies'=>$_SESSION['current_company']));
			if($_POST['tax'] == 'on'){
				$data= array('tax' => $id);  
			} else {
				$data= array('tax' => 'NULL'); 
			}
			$settings->update_attributes($data); 
            $ins = array(
                'name' => $this->input->post('name'),
				'description'=>$this->input->post('description'),
                'update_date'=>date("Y-m-d h:i:s"),
            );
			$this->db->where('id',$id);
			$this->db->set($ins);
			$this->db->update('referentiels');
            redirect('settings/refvente');
        } else {
			$this->view_data['settings']=setting::find(array('id_vcompanies'=>$_SESSION['current_company']));
            $this->theme_view = 'modal';
            $this->view_data['title'] = $this->lang->line('application_edit');
            $this->view_data['form_action'] = 'settings/editTaxe/'.$id;
			$this->db->where('id',$id);
			$data=$this->db->get('ref_type_occurences');
			$this->view_data['data']=$data->result()[0];
            $this->content_view = 'settings/referentiel/addreftaxe';
        }
	}
	
	/*function ajoutDevise(){
    	if ($_POST) {
			//insert new devise
			$data1 = array(
                'id_type'=> 10,
				'name' => $this->input->post('name'),
				'description'=>$this->input->post('description'),
				'visible' => 1,
            );
			$this->db->insert('referentiels', $data1); 
			//insert chiffre per virgule 
			$d = array(
                'name' => $this->input->post('name'),
				'description'=>$this->input->post('description')
            );
			$this->db->insert('ref_type', $d);
			$lastId= $this->refType->getLastId(); 
            $data = array(
				'id_type'=>$lastId,
                'name' => $_POST['nbrChiffre'],
				'description'=>$this->input->post('name'),
                'created_date'=>date("Y-m-d h:i:s"),
				'visible'=>1
            );
            $this->db->insert('referentiels', $data); 
            redirect('settings/societe');
        } else {
            $this->theme_view = 'modal';
            $this->view_data['title'] = $this->lang->line('application-add');
            $this->view_data['form_action'] = 'settings/ajoutDevise';
            $this->content_view = 'settings/addrefDevise';
        }
	}
	
	function refrh(){
		$this->view_data['breadcrumb'] = $this->lang->line('application_ref_rh');
		$this->view_data['breadcrumb_id'] = "rh";
		$cond=array("visible"=>1,
					"id_type"=>11);
		$this->db->where($cond);
		$journé=$this->db->get('referentiels')->result();
		$cond=array("visible"=>1,
					"id_type"=>14);
					$this->db->where($cond);
		$paiement=$this->db->get('referentiels')->result();
		$cond=array("visible"=>1,
					"id_type"=>15);
					$this->db->where($cond);
					$pret=$this->db->get('referentiels')->result();
		$cond=array("visible"=>1,
					"id_type"=>16);
					$this->db->where($cond);
					$remboursement=$this->db->get('referentiels')->result();
		$cond=array("visible"=>1,
					"id_type"=>17);
					$this->db->where($cond);
					$absence=$this->db->get('referentiels')->result();
		$this->view_data['absence']=$absence;
		$this->view_data['remboursement']=$remboursement;
		$this->view_data['pret']=$pret;
		$this->view_data['journé']=$journé;
		$this->view_data['paiement']=$paiement;
		$this->content_view = 'settings/referentielRH';
	}
	
	function editrh($id){
	 if ($_POST) {
            $ins = array(
                'name' => $this->input->post('name'),
				'description'=>$this->input->post('description'),
                'update_date'=>date("Y-m-d h:i:s"),
            );
			$this->db->where('id',$id);
			$this->db->set($ins);
			$this->db->update('referentiels');
            redirect('settings/refrh');
        } else {
            $this->theme_view = 'modal';
            $this->view_data['title'] = $this->lang->line('application_edit');
            $this->view_data['form_action'] = 'settings/editrh/'.$id;
			$this->db->where('id',$id);
			$data=$this->db->get('referentiels');
			$this->view_data['data']=$data->result()[0];
            $this->content_view = 'settings/addref';
        }
	}
	
	function deleterh($id){
		$this->db->where('id',$id);
		$this->db->set('visible',0);
		$this->db->update('referentiels');
		redirect('settings/refrh');
	}
	
	function ajoutTypejourne(){
		if ($_POST) {
            $data = array(
				'id_type'=>11,
                'name' => $this->input->post('name'),
				'description'=>$this->input->post('description'),
                'created_date'=>date("Y-m-d h:i:s"),
				'visible'=>1
            );
            $this->db->insert('referentiels', $data);
            redirect('settings/refrh');
        } else {
            $this->theme_view = 'modal';
            $this->view_data['title'] = $this->lang->line('application-add');
            $this->view_data['form_action'] = 'settings/ajoutTypejourne';
            $this->content_view = 'settings/addref';
        }
	}
	
	function ajoutTypepai(){
		if ($_POST) {
            $data = array(
				'id_type'=>14,
                'name' => $this->input->post('name'),
				'description'=>$this->input->post('description'),
                'created_date'=>date("Y-m-d h:i:s"),
				'visible'=>1
            );
            $this->db->insert('referentiels', $data);
            redirect('settings/refrh');
        } else {
            $this->theme_view = 'modal';
            $this->view_data['title'] = $this->lang->line('application-add');
            $this->view_data['form_action'] = 'settings/ajoutTypepai';
            $this->content_view = 'settings/addref';
        }
	}
	
	function ajoutTypepret(){
		if ($_POST) {
            $data = array(
				'id_type'=>15,
                'name' => $this->input->post('name'),
				'description'=>$this->input->post('description'),
                'created_date'=>date("Y-m-d h:i:s"),
				'visible'=>1
            );
            $this->db->insert('referentiels', $data);
            redirect('settings/refrh');
        } else {
            $this->theme_view = 'modal';
            $this->view_data['title'] = $this->lang->line('application-add');
            $this->view_data['form_action'] = 'settings/ajoutTypepret';
            $this->content_view = 'settings/addref';
        }
	}
	
	function ajoutTyperembrou(){
		if ($_POST) {
            $data = array(
				'id_type'=>16,
                'name' => $this->input->post('name'),
				'description'=>$this->input->post('description'),
                'created_date'=>date("Y-m-d h:i:s"),
				'visible'=>1
            );
            $this->db->insert('referentiels', $data);
            redirect('settings/refrh');
        } else {
            $this->theme_view = 'modal';
            $this->view_data['title'] = $this->lang->line('application-add');
            $this->view_data['form_action'] = 'settings/ajoutTyperembrou';
            $this->content_view = 'settings/addref';
        }
	}
	
	function ajoutcodeab(){
		if ($_POST) {
            $data = array(
				'id_type'=>17,
                'name' => $this->input->post('name'),
				'description'=>$this->input->post('description'),
                'created_date'=>date("Y-m-d h:i:s"),
				'visible'=>1
            );
            $this->db->insert('referentiels', $data);
            redirect('settings/refrh');
        } else {
            $this->theme_view = 'modal';
            $this->view_data['title'] = $this->lang->line('application-add');
            $this->view_data['form_action'] = 'settings/ajoutcodeab';
            $this->content_view = 'settings/addref';
        }
	}*/
	
	function Salarie(){
		$this->view_data['breadcrumb'] = $this->lang->line('application_rh_salarie');
		$this->view_data['breadcrumb_id'] = "Salarie";
		$cond=array("visible"=>1,
					"id_type"=>12);
		$this->db->where($cond);
		$situation=$this->db->get('ref_type_occurences')->result();
		$cond=array("visible"=>1,
					"id_type"=>13);
					$this->db->where($cond);
		$genre=$this->db->get('ref_type_occurences')->result();
		$cond=array("visible"=>1,
					"id_type"=>18);
					$this->db->where($cond);
		$contrat=$this->db->get('ref_type_occurences')->result();
		$cond=array("visible"=>1,
					"id_type"=>19);
					$this->db->where($cond);
		$fonction=$this->db->get('ref_type_occurences')->result();
		$this->view_data['fonction']=$fonction;
		$this->view_data['contrat']=$contrat;
		$this->view_data['genre']=$genre;
		$this->view_data['situation']=$situation;
		$this->content_view = 'settings/referentielRHSalarie';
	}
	
	function editSalarie($id){
		if ($_POST) {
            $ins = array(
                'name' => $this->input->post('name'),
				'description'=>$this->input->post('description'),
                'update_date'=>date("Y-m-d h:i:s"),
            );
			$this->db->where('id',$id);
			$this->db->set($ins);
			$this->db->update('ref_type_occurences');
            redirect('settings/Salarie');
        } else {
            $this->theme_view = 'modal';
            $this->view_data['title'] = $this->lang->line('application_edit');
            $this->view_data['form_action'] = 'settings/editSalarie/'.$id;
			$this->db->where('id',$id);
			$data=$this->db->get('ref_type_occurences');
			$this->view_data['data']=$data->result()[0];
            $this->content_view = 'settings/addref';
        }
	}
	
	
	
	function deleteSalarie($id){
		$this->db->where('id',$id);
		$this->db->set('visible',0);
		$this->db->update('ref_type_occurences');
		redirect('settings/Salarie');
	}
	
	function ajoutSitfam(){
		if ($_POST) {
            $data = array(
				'id_type'=>12,
                'name' => $this->input->post('name'),
				'description'=>$this->input->post('description'),
                'created_date'=>date("Y-m-d h:i:s"),
				'visible'=>1
            );
            $this->db->insert('ref_type_occurences', $data);
            redirect('settings/Salarie');
        } else {
            $this->theme_view = 'modal';
            $this->view_data['title'] = $this->lang->line('application-add');
            $this->view_data['form_action'] = 'settings/ajoutSitfam';
            $this->content_view = 'settings/addref';
        }
	}
	
	function ajoutgenre(){
		if ($_POST) {
            $data = array(
				'id_type'=>13,
                'name' => $this->input->post('name'),
				'description'=>$this->input->post('description'),
                'created_date'=>date("Y-m-d h:i:s"),
				'visible'=>1
            );
            $this->db->insert('ref_type_occurences', $data);
            redirect('settings/Salarie');
        } else {
            $this->theme_view = 'modal';
            $this->view_data['title'] = $this->lang->line('application-add');
            $this->view_data['form_action'] = 'settings/ajoutgenre';
            $this->content_view = 'settings/addref';
        }
	}
	
	function ajoutcontsalarie(){
		if ($_POST) {
            $data = array(
				'id_type'=>18,
                'name' => $this->input->post('name'),
				'description'=>$this->input->post('description'),
                'created_date'=>date("Y-m-d h:i:s"),
				'visible'=>1
            );
            $this->db->insert('ref_type_occurences', $data);
            redirect('settings/Salarie');
        } else {
            $this->theme_view = 'modal';
            $this->view_data['title'] = $this->lang->line('application-add');
            $this->view_data['form_action'] = 'settings/ajoutcontsalarie';
            $this->content_view = 'settings/addref';
        }
	}
	
	function ajoutFonction(){
		if ($_POST) {
            $data = array(
				'id_type'=>19,
                'name' => $this->input->post('name'),
				'description'=>$this->input->post('description'),
                'created_date'=>date("Y-m-d h:i:s"),
				'visible'=>1
            );
            $this->db->insert('ref_type_occurences', $data);
            redirect('settings/Salarie');
        } else {
            $this->theme_view = 'modal';
            $this->view_data['title'] = $this->lang->line('application-add');
            $this->view_data['form_action'] = 'settings/ajoutFonction';
            $this->content_view = 'settings/addref';
        }
	}
	
	function editcompany(){
		$id=$_SESSION['current_company'];
		if($_POST){
			if(is_uploaded_file($_FILES['userfile']['tmp_name'])){

				$config['upload_path'] = './files/media/';
				$config['encrypt_name'] = FALSE;
				$config['overwrite'] = TRUE;
				$config['allowed_types'] = 'gif|jpg|jpeg|png|svg';

				$this->load->library('upload', $config);

				if ( $this->upload->do_upload("userfile"))
					{
						$data = array('upload_data' => $this->upload->data());
						$_POST['company_logo'] = $data['upload_data']['file_name'];
					}
				}
				$data=array("name"=>$this->input->post('name'),
						"phone"=>$this->input->post('phone'),
						"mobile"=>$this->input->post('mobile'),
						"address"=>$this->input->post('address'),
						"zipcode"=>$this->input->post('zipcode'),
						"city"=>$this->input->post('city'),
						"website"=>$this->input->post('website'),
						"country"=>$this->input->post('country'),
						"vat"=>$this->input->post('vat'),
                        "cnss"=>$this->input->post('cnss'));
						
				if (isset($_POST['company_logo'])) {
							$data["picture"] = $_POST['company_logo'];
				}
				$this->db->where('id',$id);
				$this->db->set($data);
				$edit=$this->db->update('v_companies');
			if(!$edit){$this->session->set_flashdata('message', 'error:'.$this->lang->line('messages_update_company_error'));}
       		else{$this->session->set_flashdata('message', 'success:'.$this->lang->line('messages_update_company_success'));}
						redirect('settings/editcompany/'.$id);
			}else{
				$this->view_data['breadcrumb'] = $this->lang->line('application_edit_company');
				$this->view_data['breadcrumb_id'] = "Societe";
				$this->db->where('id',$id);
				$company=$this->db->get('v_companies')->result()[0];
				$this->view_data['paiement']=$paiement;
				$this->view_data['company']=$company;
				$this->view_data['form_action'] = 'settings/editcompany';
				$this->content_view = 'settings/_company';
			}
		}

	function saveNotes() {
		$option=array("id_vcompanies"=>$_SESSION['current_company']);
		$core = Setting::find($option);
		$core->notes = $this->input->post('notes');
		$core->save();
		redirect('settings/gestionCommercial');
	}

	function saveFactureNotes() {
		$option=array("id_vcompanies"=>$_SESSION['current_company']);
		$core = Setting::find($option);
		$core->notes_facture = $this->input->post('notes_facture');
		$core->save();
		redirect('settings/gestionCommercial');
	}
	
	function updateDefaultCompteBancaire() {
		$option=array("id_vcompanies"=>$_SESSION['current_company']);
		$core = Setting::find($option);
		$core->compteBancaire = $this->input->post('compteBancaire');
		$core->save();
		redirect('settings/compteBancaire');
	}
	
	function compteBancaire()	{
		$this->view_data['breadcrumb'] = $this->lang->line('application_compte_bancaire');
		$this->view_data['breadcrumb_id'] = "Societe";
		$company=$this->db->get('v_companies')->result()[0];
		$compteBancaire=$this->db->query("select * from comptes_bancaires")->result();
		$option=array("id_vcompanies"=>$_SESSION['current_company']);
		$this->view_data['settings'] = Setting::find($option); 
		$this->view_data['paiement']=$paiement;
		$this->view_data['compteBancaire']=$compteBancaire;
		$this->view_data['company']=$company;
		$this->view_data['form_action'] = 'settings/updateDefaultCompteBancaire';
		$this->content_view = 'settings/compteBancaire';	
	}
	
	function editPayment($id){
		if ($_POST) {
            $ins = array(
                'name' => $this->input->post('name'),
				'description'=>$this->input->post('description'),
                'update_date'=>date("Y-m-d h:i:s"),
            );
			$this->db->where('id',$id);
			$this->db->set($ins);
			$this->db->update('ref_type_occurences');
            redirect('settings/vente');
        } else { 
            $this->theme_view = 'modal';
            $this->view_data['title'] = $this->lang->line('application_edit');
            $this->view_data['form_action'] = 'settings/editFacture/'.$id;
			$this->db->where('id',$id);
			$data=$this->db->get('ref_type_occurences');
			$this->view_data['data']=$data->result()[0];
            $this->content_view = 'settings/addref';
        }
	}
	
	function addPayment(){
		if ($_POST) {
            $data = array(
				'id_type'=>8,
                'name' => $this->input->post('name'),
				'description'=>$this->input->post('description'),
                'created_date'=>date("Y-m-d h:i:s"),
				'visible'=>1
            );
			$this->db->insert('ref_type_occurences',$data);
            redirect('settings/vente');
        } else {
            $this->theme_view = 'modal';
            $this->view_data['title'] = $this->lang->line('application-add');
            $this->view_data['form_action'] = 'settings/addPayment';
            $this->content_view = 'settings/addref';
        }
	}


	
	function deletePayment($id){
		$this->db->where('id',$id);
		$this->db->set('visible',0);
		$this->db->update('ref_type_occurences');
		redirect('settings/vente');
	}
	
	function deleteCompteBancaire($id){
		$this->db->where('id',$id);
		$this->db->set('visible',0);
		$this->db->update('comptes_bancaires');
		$this->db->where('id_vcompanies',$_SESSION['current_company']);
		$this->db->set('compteBancaire',NULL);
		$this->db->update('core');
		redirect('settings/_company');
	}
	
	function checkUsername($username){ 
		$user =	$this->account->getByName($username);  
		foreach($user as $user){
			if(!empty($user) && $user->status == 'active'){
				$output = true ; 
			} else {
				$output = false;
			}
		}
		header('Content-Type: application/json');
		echo json_encode($output);
		exit();
	}
	
	function compteBancairePreview($id = FALSE, $attachment = FALSE){
		$this->load->helper(array('dompdf', 'file')); 
		$this->load->library('parser');
		$this->load->database();
		$data["compteBancaire"] = $this->compteBancaire->getCompteById($id); 
		$option=array("id_vcompanies"=>$_SESSION['current_company']);
		$data["core_settings"] = Setting::find($option);
		
		
		$this->db->where('id',$data["core_settings"]->id);
		$data["company"]=$this->db->get('v_companies')->result()[0];
		
		$parse_data = array(
            				'invoice_id' => $data["core_settings"]->invoice_prefix.$data["invoice"]->reference,
            				'client_link' => $data["core_settings"]->domain,
            				'company' => $data["core_settings"]->company,
            				);
		$html = $this->load->view($data["core_settings"]->template. '/' .$data["core_settings"]->comptebancaire_pdf_template, $data, true); 
		$html = $this->parser->parse_string($html, $parse_data);
		$filename = $data['compteBancaire']->nom;    
		
		//Générer le pdf
        $this->pdf->load_view($html, $filename);
	}

	function settings_rh(){
		$table="setting_document_rh";
		$query = $this->db->get($table);
		if($query->num_rows()==0){
			$this->db->insert($table, $_POST);
			redirect('settings/paiecnss');
		}else{
			$this->db->where('id_setting_rh', 1);
			$this->db->set($_POST)->update($table);
			redirect('settings/paiecnss');
		}
	}

	function paiecnss() {
		$table="setting_document_rh";
		$data =$this->db->get($table);
		$this->view_data['data'] = $data->result();
		$this->view_data['form_action'] = 'settings/settings_rh/';
		$this->content_view = 'settings/settingsrh';
		if($_POST){
			if(isset($_POST["fonction007"]))
			{
				unset($_POST['send']);
				unset($_POST['fonction007']);
				unset($_POST['zomba']);
				unset($_POST['userfile']);
				unset($_POST['file-name']);
				unset($_POST['view']);
				unset($_POST['idparam']);
				$_POST['id_vcompanie'] = (int)$_SESSION['current_company'];
				$_POST['id_type'] = 19;
				if(isset($_POST["access"]))
				{ $_POST["access"] = implode(",", $_POST["access"]); }
				else{
				unset($_POST["access"]);}
			 	$a = $this->db->insert('ref_type_occurences',$_POST);
				$this->session->set_flashdata('message', 'success:'.$this->lang->line('application_modified'));
			 	 redirect('settings/paiecnss');
			}
			$id = $_POST['idparam'] ; 
			unset($_POST['send']);
			unset($_POST['zomba']);
			unset($_POST['userfile']);
			unset($_POST['file-name']);
			unset($_POST['view']);
			unset($_POST['idparam']);
			$_POST['id_companie'] = (int)$_SESSION['current_company'];

			if(isset($_POST["access"]))

				{ $_POST["access"] = implode(",", $_POST["access"]); }
			else
				{unset($_POST["access"]);}
		 		$this->db->where('id', $id);
			 	$a = $this->db->update('referentiels_rh_paies',$_POST);
			$this->session->set_flashdata('message', 'success:'.$this->lang->line('application_modified'));
			redirect('settings/paiecnss');
		}
		else{
			$outils =$this->db->select('*')->from('ref_type_occurences')->where('id_type',19)->where('id_vcompanie',(int)$_SESSION['current_company'])->get()->result();
			$this->view_data['outils']=$outils;
			$item=$this->db->select('*')->from('referentiels_rh_paies')->where(	'id_companie',(int)$_SESSION['current_company'])->get()->result();
			$i=0;
			foreach ($item as $key => $value) {
				$i++;
			}
			if($i==0)
			{
				$_POST = null;
				$_POST['id_companie'] = (int)$_SESSION['current_company'];
				$this->db->insert('referentiels_rh_paies', $_POST);
			}
			$item=$this->db->select('*')->from('referentiels_rh_paies')->where('id_companie',(int)$_SESSION['current_company'])->get()->result();
			$this->view_data['item'] = $item;
			foreach ($item as $key ) {
			$idparam = $key->id;
			}

            //Motif d'absence
            $this->view_data['refTab']['motif_absence']['tab'] = $this->referentiels->getAllReferentielsByCodeType($this->config->item("type_code_motif_absence"), true);
            $this->view_data['refTab']['motif_absence']['libelle']=$this->lang->line('application_motif_absence');
            $this->view_data['refTab']['motif_absence']['url_add_ref']='settings/ajoutMotifabsence';
            $this->view_data['refTab']['motif_absence']['url_update_ref']='settings/editMotifabsence';
            $this->view_data['refTab']['motif_absence']['url_delete_ref']='settings/desactiverMotifabsence';
            $this->view_data['refTab']['motif_absence']['masquer_statut'] = true;

            //Statut de congés
            $this->view_data['refTab']['statut_conges']['tab'] = $this->referentiels->getAllReferentielsByCodeType($this->config->item("type_code_statut_conges"), true);
            $this->view_data['refTab']['statut_conges']['libelle']=$this->lang->line('application_statut_conges');
            $this->view_data['refTab']['statut_conges']['url_add_ref']='settings/ajoutStatutConges';
            $this->view_data['refTab']['statut_conges']['url_update_ref']='settings/editStatutConges';
            $this->view_data['refTab']['statut_conges']['url_delete_ref']='settings/desactiverStatutConges';
            $this->view_data['refTab']['statut_conges']['masquer_statut'] = true;

            $this->view_data['idparam	'] = $idparam;
			$this->view_data['form_action'] = 'settings/paiecnss/';
			$this->view_data['form_action_1'] = 'settings/settings_rh/';
			$this->content_view = 'settings/paiecnss';
		}
	}

	function add_fonction()	{	
		if($_POST){
		}else{
			$this->theme_view = 'modal';
			$this->view_data['title'] = $this->lang->line('application_neauveau_fonction');
			$this->view_data['form_action'] = 'settings/paiecnss/';
			$this->content_view = 'rhpaie/addfonction';
		}
	}
	
	function delete_fonction($id = FALSE){	
 		$this->db->where('id', $id);
        $this->db->delete('ref_type_occurences');
		$this->content_view = 'rhpaie/gestionprime';
	}
	// Render Modules
	public function rendermodule($id){
		$option=array("id"=>$id);
		$submenu = Submenu::find($option);
		$module = $this->db->query('Select * from modules m where m.id="'.$submenu->id_modules.'"')->result()[0];
		header('Content-Type: application/json');
		echo json_encode($module->id);
		exit();
	}
	
	// Render submenus 
	public function renderSubmenu($id){ 
		$option=array("id_modules"=>$id);
		
		$submenu = Submenu::find('all',array('conditions' => array("id_modules"=>$id)));
		$count = count($submenu);
		for($i = 0;$i < $count; $i++)
		{
			$tab[$i] = $submenu[$i]->link; 
		} 
		header('Content-Type: application/json');
		echo json_encode($tab);
		exit();
	}
	
	function AjoutAvoir(){
		if ($_POST) {
            $data = array(
				'id_type'=>25,
                'name' => $this->input->post('name'),
				'description'=>$this->input->post('description'),
                'created_date'=>date("Y-m-d h:i:s"),
				'visible'=>1
            );
			$this->db->insert('ref_type_occurences', $data);
            redirect('settings/vente');
        } else {
            $this->theme_view = 'modal';
            $this->view_data['title'] = $this->lang->line('application-add');
            $this->view_data['form_action'] = 'settings/AjoutAvoir';
            $this->content_view = 'settings/addref';
        }
	}
	
	function saveAvoirNotes() {
		$option=array("id_vcompanies"=>$_SESSION['current_company']);
		$core = Setting::find($option);
		$core->notes_avoir = $this->input->post('notes_avoir');
		$core->save();
		redirect('settings/gestionCommercial');
	}
	
	function GetBetween($var1="",$var2="",$pool){
		$temp1 = strpos($pool,$var1)+strlen($var1);
		$result = substr($pool,$temp1,strlen($pool));
		$dd=strpos($result,$var2);
		if($dd == 0){
		$dd = strlen($result);
		}
		return substr($result,0,$dd);
	}
	
	// Référentiel de la société (Forme juridique / régime TVA / Taux TVA)
	/*
	function societe(){
		$this->view_data['breadcrumb'] = $this->lang->line('application_ref_societe');
		$this->view_data['breadcrumb_id'] = "Societe";
		$option=array("id_vcompanies"=>$_SESSION['current_company']);
		$this->view_data["settings"] = Setting::find($option);
		$cond=array("visible"=>1,
					"id_type"=>6);
		$this->db->where($cond);
		$forme=$this->db->get('referentiels')->result();
		$cond=array("visible"=>1,
					"id_type"=>7);
		$this->db->where($cond);
		$regime=$this->db->get('referentiels')->result();
		$cond=array("visible"=>1,
					"id_type"=>9);
					$this->db->where($cond);
		$taxe=$this->db->get('referentiels')->result();
		$cond=array("visible"=>1,
					"id_type"=>10);
					$this->db->where($cond);
		$devise=$this->db->get('referentiels')->result();			
		$this->view_data['forme']=$forme;
		$this->view_data['regime']=$regime;	
		$this->view_data['taxe']=$taxe;
		$this->view_data['devise']=$devise;
		$this->content_view = 'settings/referentielSociete';
	}*/


	//liste des utilisateurs 
	function listUser($statut = 1){
		$this->view_data['breadcrumb'] = $this->lang->line('application_users_access');
		$this->view_data['breadcrumb_id'] = "users";
		if(! in_array($statut, array(0,1))){
			redirect("settings/listUser");
		}
		if($statut == 0){ //inactif
			$options = array('conditions' => array('status = ? ', 'deleted'));
		}else{
			$options = array('conditions' => array('status != ? ', 'deleted'));
		}
		$users = User::all($options);
		$this->view_data['users'] = $users;
		$this->view_data['statut'] = $statut;
		$this->content_view = 'settings/listuser';
	}

	function user_access_update($id = null){
		if(! is_null($id))
 			$user = User::find($id);
 		if($_POST){
			
            $user = User::find($_POST['id']);

            $dscreen = array('default_screen'=>$_POST['default_screen']);
            unset($_POST['default_screen']);

            unset($_POST['send']);
			//company ?? 
			$access = AccesRigth::find(array('conditions' => array('user_id=? and company_id=?',$_POST['id'],$_SESSION['current_company'])));
			if(!empty($_POST["menu"])){
				$_POST["menu"] = implode(",", $_POST["menu"]);
			}
			if(!empty($_POST["submenu"])){
				$_POST["submenu"] = implode(",", $_POST["submenu"]);
			}
			$modules = Module::find('all', array('order' => 'sort asc', 'conditions' => array('type != ?  and default_module = ?', 'client','1')));
           
			$defaultModule = "";
            foreach($modules as $val){
				$defaultModule = $defaultModule.$val->id.','; 
			}
			$_POST["menu"] = $defaultModule.$_POST["menu"];
			unset($_POST["defaultAccess"]); 
			$_POST = array_map('htmlspecialchars', $_POST);
		

			//Mot de passe
			if(isset($_POST["password"]) && isset($_POST["confirm_password"])){
				if($_POST["password"] == $_POST["confirm_password"]){
					$dscreen['password'] = $_POST["password"];
				}
			}
			unset($_POST['password']);
			unset($_POST['confirm_password']);
			
			if($_POST['admin']){
				
				$dscreen['admin']=1;
			}else{
				$dscreen['admin']=0;
			}
			
			if($_POST['salarie']){
				$this->load->database();
				if(verifSalaries($user->salaries_id) == "false"){
				
					$data = array(
						'mail'=> $_POST['email'],
						'nom'=> $_POST['firstname'],
						'prenom'=> $_POST['lastname'],
					);
					$this->db->set($data);
					$query = $this->db->insert('salaries');
					$lastId= $this->salaries->getLastId(); 
				    $dscreen['salaries_id']=$lastId;
				}
				
			}
			//modifier l'etat de salarie
			$dscreen['status'] = $_POST['status'];
//var_dump($dscreen['status']);exit;
			if($dscreen['status'] == 'inactive'){
				$useridsalarie = $user->salaries_id;
				$data = array(
				   'etat'=>'0',
				);
				$this->db->where('id',$useridsalarie);
				$this->db->set($data);
				$query = $this->db->update('salaries');
			}else{
				$useridsalarie = $user->salaries_id;
				$data = array(
					'etat'=>'1',
				);
				$this->db->where('id',$useridsalarie);
				$this->db->set($data);
				$query = $this->db->update('salaries');
			}
			
			unset($_POST['id']); 
			unset($_POST['email']);
            unset($_POST['firstname']);
			unset($_POST['lastname']);
			unset($_POST['admin']);
			unset($_POST['salarie']);
			unset($_POST['status']);
			//var_dump($access);exit;

            $access->update_attributes($_POST);
            $dscreen = array_map('htmlspecialchars', $dscreen);
            $user->update_attributes($dscreen);
            $this->session->set_flashdata('message', 'success:'.$this->lang->line('messages_save_user_access'));
			redirect('settings/listUser');
 		}else{
			$this->view_data['user'] = $user;
 			$this->theme_view = 'modal';
			$this->view_data['title'] = $this->lang->line('application_create_user');
			// all modules 
			$this->view_data['modules'] = Module::find('all', array('order' => 'sort asc', 'conditions' => array('type != ? ', 'client')));  
			// all submenus
			$this->view_data['submenus'] = modules_sous::find('all', array('order' => 'sort asc', 'conditions' => array('type != ?', 'client')));
			//all companies 
			//$this->view_data['companies'] = V_companie::find('all');
			//acces menu for this user 
			$access = AccesRigth::find(array('conditions' => array('user_id=? and company_id=?',$user->id,$_SESSION['current_company'])));
			$this->view_data['tabaccess'] = explode(",", $access->menu);
			//acces submenus this user 
			$submenusaccess = AccesRigth::find(array('conditions' => array('user_id=? and company_id=?',$user->id,$_SESSION['current_company'])));
			$this->view_data['tabsubaccess'] = explode(",", $submenusaccess->submenu);			
            $this->view_data['form_action'] = 'settings/user_access_update/';
            $this->content_view = 'settings/_accessUser';
 		}	
		
	}
	
	//choix template pdf 
	function choice_template(){
		$this->view_data['form_action'] = 'settings/saveTemplate';
		$this->view_data['breadcrumb'] = "Choix template";
		// get the number of templates 
		$dir = "application/views/blueline/templates/invoice";
		$files = array(); 
		foreach (new DirectoryIterator($dir) as $file) {
			if ($file->isFile()) {
				if($file->getFilename() != 'nuts.php'){
					$fi = explode('.',$file->getFilename());
					array_push($files,$fi[0]) ;
				}
			}
		}
		$option=array("id_vcompanies"=>$_SESSION['current_company']);
 		$this->view_data['defaultTemplate']  = Setting::find($option)->default_template;
		$this->view_data['files'] = $files ; 
		$this->content_view = 'settings/choiceTemplate'; 
	}
	
	function preview($file, $attachment = FALSE){	 
		$this->load->helper(array('dompdf', 'file')); 
		$this->load->library('parser');
		$option=array("id_vcompanies"=>$_SESSION['current_company']);
		$data["core_settings"] = Setting::find($option);
		$path = explode('/',$data["core_settings"]->invoice_pdf_template);
		$html = $this->load->view($data["core_settings"]->template. '/' .$path[0].'/'.$path[1].'/'.$file, $data, true); 
		//$html = $this->load->view($data["core_settings"]->template. '/templates/invoice/default', $data, true); 
		$html = $this->parser->parse_string($html, $parse_data);
		$filename = $file[0].' template'; 
		 pdf_create($html,  str_replace(array('à','á','â','ã','ä', 'ç', 'è','é','ê','ë', 'ì','í','î','ï', 'ñ', 'ò','ó','ô','õ','ö', 'ù','ú','û','ü', 'ý','ÿ', 'À','Á','Â','Ã','Ä', 'Ç', 'È','É','Ê','Ë', 'Ì','Í','Î','Ï', 'Ñ', 'Ò','Ó','Ô','Õ','Ö', 'Ù','Ú','Û','Ü', 'Ý', ' ')
			, array('a','a','a','a','a', 'c', 'e','e','e','e', 'i','i','i','i', 'n', 'o','o','o','o','o', 'u','u','u','u', 'y','y', 'A','A','A','A','A', 'C', 'E','E','E','E', 'I','I','I','I', 'N', 'O','O','O','O','O', 'U','U','U','U', 'Y', '_'),
			$filename), TRUE, $attachment);
	}
	
	function saveTemplate(){	 
		unset($_POST['send']); 
		$this->db->where('id_vcompanies', $_SESSION['current_company']);
		$this->db->update('core',$_POST);
		redirect('settings/choice_template'); 
		
	}
	
	function checkMail($email){ 
		$options = array("email"=>$email);
		$user = User::find($options); 
		if(!empty($user) && $user->status == 'active'){
			$output = true ; 
		} else {
			$output = false;
		}
		header('Content-Type: application/json');
		echo json_encode($output);
		exit();
	}
	
	

	//////////////////////////////////////// PARAMETRES  ////////////////////////////////
	function projectReferentielSubMenu( $breadcrumb){
		$this->view_data['breadcrumb'] = $breadcrumb;
		$this->view_data['breadcrumb_id'] = $breadcrumb;

		$this->view_data['submenu'] = array(
			$this->lang->line('application_ref') => 'projects-params',
			$this->lang->line('application_tache_categorie')=>'projects-params/taches-par-defaut',
		);
	}

	/*
	Afficher le référentiel GEST COM de VISION
	*/

	function gestionCommercial(){
		$this->view_data['breadcrumb'] = $this->lang->line('application_gestioncommercial');
		$this->view_data['breadcrumb_id'] = "achat";
		$this->db->where('visible',1);
		$echeance=$this->db->query('select * from ref_type_occurences where visible=1 and id_type=20')->result();
		$item_units = $this->db->query("SELECT * FROM item_units")->result();
		$this->view_data['item_units'] = $item_units;
		$this->view_data['echeance']=$echeance;
		$this->view_data['form_action']="settings/Timbre";
		$settings=setting::find(array("id_vcompanies"=>$_SESSION['current_company']));
		$this->view_data['timbre']=$settings->timbre_fiscal;
		$this->view_data['settings']=$settings;
		$cond=array("visible"=>1,
					"id_type"=>8);
					$this->db->where($cond);
		$paiement=$this->db->get('ref_type_occurences')->result();
		$compteBancaire=$this->db->query('select * from comptes_bancaires')->result();
		$this->view_data['paiement']=$paiement;
		$this->view_data['compteBancaire']=$compteBancaire;

		//Unité


		$this->content_view = 'settings/gestioncommercial';
	}

	/*
	Afficher le référentiel VENTE de VISION
	*/
	public function refvente()
	{
		
		$this->view_data['breadcrumb'] = $this->lang->line('application_ref_vente');
		$this->view_data['breadcrumb_id'] = "refvente";
		/*************** VENTE *****************/
		// Moyens de paiement
		$id_type = $this->config->item("type_id_moyens_paiement");
		$this->view_data['refTab']['MoyensPaiement']['tab'] = $this->referentiels->getReferentielsByIdType($id_type);
		$this->view_data['refTab']['MoyensPaiement']['libelle']= "Moyens de paiement";
		$this->view_data['refTab']['MoyensPaiement']['url_add_ref']='settings/ajoutReferentiel/'.$id_type.'/refvente';
		$this->view_data['refTab']['MoyensPaiement']['url_update_ref']='settings/editReferentiel/refvente';
		$this->view_data['refTab']['MoyensPaiement']['url_delete_ref']='settings/desactiverReferentiel/refvente';
		// Etat de la facture		
		/*$id_type = $this->config->item("type_id_etat_facture");
		$this->view_data['refTab']['EtatFacture']['tab'] = $this->referentiels->getReferentielsByIdType($id_type);
		$this->view_data['refTab']['EtatFacture']['libelle']= "Etats d'une facture";
		$this->view_data['refTab']['EtatFacture']['url_add_ref']='settings/ajoutReferentiel/'.$id_type.'/refvente';
		$this->view_data['refTab']['EtatFacture']['url_update_ref']='settings/editReferentiel/refvente';
		$this->view_data['refTab']['EtatFacture']['url_delete_ref']='settings/desactiverReferentiel/refvente';
		// Etat devis		
		$id_type = $this->config->item("type_id_etat_devis");
		$this->view_data['refTab']['EtatDevis']['tab'] = $this->referentiels->getReferentielsByIdType($id_type);
		$this->view_data['refTab']['EtatDevis']['libelle']= "Etats d'un devis";
		$this->view_data['refTab']['EtatDevis']['url_add_ref']='settings/ajoutReferentiel/'.$id_type.'/refvente';
		$this->view_data['refTab']['EtatDevis']['url_update_ref']='settings/editReferentiel/refvente';
		$this->view_data['refTab']['EtatDevis']['url_delete_ref']='settings/desactiverReferentiel/refvente';
		// Etat avoir		
		$id_type = $this->config->item("type_id_etat_avoir");
		$this->view_data['refTab']['EtatAvoir']['tab'] = $this->referentiels->getReferentielsByIdType($id_type);
		$this->view_data['refTab']['EtatAvoir']['libelle']= "Etats d'un avoir";
		$this->view_data['refTab']['EtatAvoir']['url_add_ref']='settings/ajoutReferentiel/'.$id_type.'/refvente';
		$this->view_data['refTab']['EtatAvoir']['url_update_ref']='settings/editReferentiel/refvente';
		$this->view_data['refTab']['EtatAvoir']['url_delete_ref']='settings/desactiverReferentiel/refvente';
		// Etat Bon de commande
		$id_type = $this->config->item("type_id_etat_commande");
		$this->view_data['refTab']['EtatCommande']['tab'] = $this->referentiels->getReferentielsByIdType($id_type);
		$this->view_data['refTab']['EtatCommande']['libelle']= "Etats d'un bon de commande";
		$this->view_data['refTab']['EtatCommande']['url_add_ref']='settings/ajoutReferentiel/'.$id_type.'/refvente';
		$this->view_data['refTab']['EtatCommande']['url_update_ref']='settings/editReferentiel/refvente';
		$this->view_data['refTab']['EtatCommande']['url_delete_ref']='settings/desactiverReferentiel/refvente';*/
		// TVA
		$id_type = $this->config->item("type_id_tva");
		
		$this->view_data['taxe'] = $this->referentiels->getReferentielsByIdType($id_type);
		$this->view_data['refTab']['TVA']['libelle']= "Taux TVA";
		//$this->view_data['refTab']['TVA']['url_add_ref']='settings/ajoutReferentiel/'.$id_type.'/refvente';
		//$this->view_data['refTab']['TVA']['url_update_ref']='settings/editReferentiel/refvente';
		//$this->view_data['refTab']['TVA']['url_delete_ref']='settings/desactiverReferentiel/refvente';

		$this->content_view = 'settings/referentiel/vente';
	}

	/**
	 * Ecran de paramètres d'un projet
	 * @return [type]
	 */
	public function indexParamsProjets(){
		$this->projectReferentielSubMenu("projects-params");

  				
		$this->view_data['refTab']['uniteTemps']['tab']= $this->referentiels->getReferentielsByIdType($this->config->item("type_id_unite_temps"), false);
      	$this->view_data['refTab']['uniteTemps']['libelle']=$this->lang->line('application_unite_affichage_temps_taches');
		$this->view_data['refTab']['uniteTemps']['form_action'] = 'settings/choisirUniteTemps';

		/*
		//Catégorie Projet
		$id_type = $this->config->item("type_id_type_categorie_projet");
		$this->view_data['refTab']['categProjet']['tab'] = $this->referentiels->getReferentielsByIdType($id_type);
		$this->view_data['refTab']['categProjet']['libelle']=$this->lang->line('application_categories_projet');
		$this->view_data['refTab']['categProjet']['url_add_ref']='settings/ajoutReferentiel/'.$id_type.'/projects-params';
		$this->view_data['refTab']['categProjet']['url_update_ref']='settings/editReferentiel/projects-params';
		$this->view_data['refTab']['categProjet']['url_delete_ref']='settings/desactiverReferentiel/projects-params';
		// Etat d'un projet
		$id_type = $this->config->item("type_id_etat_projet");
		$this->view_data['refTab']['etatProjet']['tab'] = $this->referentiels->getReferentielsByIdType($id_type);
		$this->view_data['refTab']['etatProjet']['libelle']= "Etat Projet";
		$this->view_data['refTab']['etatProjet']['url_add_ref']='settings/ajoutReferentiel/'.$id_type.'/projects-params';
		$this->view_data['refTab']['etatProjet']['url_update_ref']='settings/editReferentiel/projects-params';
		$this->view_data['refTab']['etatProjet']['url_delete_ref']='settings/desactiverReferentiel/projects-params';
		// Type d'une tâche
		$id_type =$this->config->item("type_id_type_tache");
		$this->view_data['refTab']['typeTache']['tab'] = $this->referentiels->getReferentielsByIdType($id_type);
		$this->view_data['refTab']['typeTache']['libelle']=$this->lang->line('application_type_taches');
		$this->view_data['refTab']['typeTache']['url_add_ref']='settings/ajoutReferentiel/'.$id_type.'/projects-params';
		$this->view_data['refTab']['typeTache']['url_update_ref']='settings/editReferentiel/projects-params';
		$this->view_data['refTab']['typeTache']['url_delete_ref']='settings/desactiverReferentiel/projects-params';
		$this->view_data['refTab']['typeTache']['masquer_statut'] = true;
		// Statut d'une tâche
		$id_type =$this->config->item("type_id_statut_ticket");
		$this->view_data['refTab']['statutTache']['tab'] = $this->referentiels->getReferentielsByIdType($id_type);
		$this->view_data['refTab']['statutTache']['libelle']= "Statut Tâche";
		$this->view_data['refTab']['statutTache']['url_add_ref']='settings/ajoutReferentiel/'.$id_type.'/projects-params';
		$this->view_data['refTab']['statutTache']['url_update_ref']='settings/editReferentiel/projects-params';
		$this->view_data['refTab']['statutTache']['url_delete_ref']='settings/desactiverReferentiel/projects-params';*/

		$this->content_view = 'settings/referentielProjets';
	}
	
		
	/*******************************************************************
	 * Ajout d'un nouveau referenciel
	 * @return [type]
	 *******************************************************************/
	function ajoutReferentiel($id_type, $redirect){
	 	if ($_POST) {
	 		$data = array(
				'id_type'=>$id_type,
                'name' => $this->input->post('name'),
				'description'=>$this->input->post('description'),
                'created_date'=>date("Y-m-d h:i:s"),
				'visible'=> 1
            );
            $this->db->insert('ref_type_occurences', $data);
            redirect($redirect);
        } else {
            $this->theme_view = 'modal';
            $this->view_data['title'] = $this->lang->line('application-add');
            $this->view_data['form_action'] = 'settings/ajoutReferentiel/'.$id_type.'/'.$redirect;
            $this->content_view = 'settings/addref';
        }
	}

	/**
	 * Edition d'un referentiel
	 * @param  [type]
	 * @return [type]
	 */
	function editReferentiel($redirect,$id) {
		if ($_POST) {
			$etat = $this->input->post('visible');
			$ins = array(
                'name' => $this->input->post('name'),
				'description'=>$this->input->post('description'),
				'update_date'=>date("Y-m-d h:i:s")
            );
            $this->referentiels->updateReferentielById($ins, $id);
			//var_dump($redirect);exit;
            redirect($redirect);
        } else {
			$this->theme_view = 'modal';
            $this->view_data['title'] = $this->lang->line('application_edit');
            $this->view_data['form_action'] = 'settings/editReferentiel/'.$redirect.'/'.$id;
			$this->view_data['data']= $this->referentiels->getReferentielsById($id);
            $this->content_view = 'settings/addref';
        }
	}

	/**
	 * Désactiver un référenciel
	 * @param  [type]
	 * @return [type]
	 */
	function desactiverReferentiel($redirect,$id){
		$ins = array(
                'visible' => 0,
                'update_date'=>date("Y-m-d h:i:s"),
            );
        $this->referentiels->updateReferentielById($ins, $id);
		redirect($redirect);
	}

	/***************************************************************
	 * Index du référentiel des tâches par defaut
	 * @param  boolean $categ_id [description]
	 * @return [type]            [description]
	 ****************************************************************/
	function indexTacheParDefaut($categ_id = false){
		$projets_categ = $this->referentiels->getReferentielsByIdType($this->config->item("type_id_type_categorie_projet"));
		
		if($categ_id){
			$categorie_tickets = Categorie_tickets::all(array("categorie_type_id"=>$categ_id));
			$this->view_data['categorie_tickets']=$categorie_tickets;
			$this->view_data['url_add_ref']='settings/addTicketsParDefaut/'.$categ_id;
			$this->view_data['url_update_ref']='settings/editTicketsParDefaut';
			$this->view_data['url_delete_ref']='settings/desactiverTicketsParDefaut';
		}
		
		$this->view_data['projets_categ']=$projets_categ;
		$this->view_data['categ_id'] = $categ_id;
		$this->projectReferentielSubMenu("projects-params/taches-par-defaut");
		$this->content_view = 'settings/referentielTacheParDefault';		
	}

	/**
	 * Ajouter une tâche par défaut 
	 * @param [type] $categ_id [description]
	 */
	function addTicketsParDefaut($categ_id){
		if ($_POST) {
	 		$data = array(
				'categorie_type_id'=>$categ_id,
                'name' => $this->input->post('name'),
				'description'=>$this->input->post('description'),
                'created_by'=>$this->user->id,
				'created_at'=>date("Y-m-d h:i:s"),
				'status'=> 1
            );
            $this->db->insert('categorie_tickets', $data);
            redirect('projects-params/taches-par-defaut/view/'.$categ_id);
        } else {
            $this->theme_view = 'modal';
            $this->view_data['title'] = $this->lang->line('application-add');
            $this->view_data['form_action'] = 'settings/addTicketsParDefaut/'.$categ_id;
            $this->content_view = 'settings/addref';
        }
	}


	/**
	 * Edition d'une tâche par défaut
	 * @param  [type]
	 * @return [type]
	 */
	function editTicketsParDefaut($categ_id, $id) {
		if ($_POST) {
			$ins = array(
                'name' => $this->input->post('name'),
				'description'=>$this->input->post('description'),
			    'updated_by'=>$this->user->id,
				'updated_at'=>date("Y-m-d h:i:s"),
            );
            $this->settingTables->updatDataById($ins, $id, Categorie_tickets::table_name());
			
            redirect('projects-params/taches-par-defaut/view/'.$categ_id);
       } else {
			$this->theme_view = 'modal';
            $this->view_data['title'] = $this->lang->line('application_edit');
            $this->view_data['form_action'] = 'settings/editTicketsParDefaut/'.$categ_id.'/'.$id;
            $this->view_data['data']= $this->settingTables->getDataById($id, Categorie_tickets::table_name());
            $this->content_view = 'settings/addref';
        }
	}

	/**
	 * Désactiver une référence
	 * @param  [type]
	 * @return [type]
	 */
	function desactiverTicketsParDefaut($categ_id, $id){
		$ins = array(
                'status' => 0,
                'updated_by'=>$this->user->id,
				'updated_at'=>date("Y-m-d h:i:s"),
             );
        $this->settingTables->updatDataById($ins, $id,  Categorie_tickets::table_name());
	    redirect('projects-params/taches-par-defaut/view/'.$categ_id);
   }

   /********************************************************************
	 * Choisir l'unité de temps
	 * @return [type]
	 *******************************************************************/
	function choisirUniteTemps(){
	 	if ($_POST) {
	 		//désactiver tout le type
	 		$this->referentiels->updateReferentielByTypeId(array("visible"=>0), $this->config->item("type_id_unite_temps") );

	 		//activer le choix sélectionner
			$type=Referentiel::find(array("id"=>$_POST['visible']));
			$type->update_attributes(array('visible'=>1));
			
            redirect('projects-params');
        } 
	}


	/*******************************************************************
	 * Ajout d'un état
	 * @return [type]
	 *******************************************************************/
	function ajoutEtatProjet(){
	 	if ($_POST) {
	 		$data = array(
				'id_type'=>$this->config->item('type_id_etat_projet'),
                'name' => $this->input->post('name'),
				'description'=>$this->input->post('description'),
                'created_date'=>date("Y-m-d h:i:s"),
				'visible'=> 1
            );
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
	 * @param  [type]
	 * @return [type]
	 */
	function editEtatProjet($id) {
		if ($_POST) {
			$etat = $this->input->post('visible');
			$ins = array(
                'name' => $this->input->post('name'),
				'description'=>$this->input->post('description'),
				'update_date'=>date("Y-m-d h:i:s")
            );
            $this->referentiels->updateReferentielById($ins, $id);
			
            redirect('projects-params');
        } else {
			$this->theme_view = 'modal';
            $this->view_data['title'] = $this->lang->line('application_edit');
            $this->view_data['form_action'] = 'settings/editEtatProjet/'.$id;
			$this->view_data['data']= $this->referentiels->getReferentielsById($id);
            $this->content_view = 'settings/addref';
        }
	}

    /*******************************************************************
     * Ajout d'un motif d'absence
     * @return [type]
     *******************************************************************/
    function ajoutMotifAbsence(){
        if ($_POST) {
            $type = RefType::find($option=array("name"=>$this->config->item('type_code_motif_absence')));
            if(!$type or is_null($type)){
                show_error("Veuillez vérifier le paramtérage des motifs d'absences des congés", "404", $heading = 'Une erreur a été rencontrée');
            }
            $data = array(
                'id_type'=> $type->id ,
                'name' => $this->input->post('name'),
                'description'=>$this->input->post('description'),
                'created_date'=>date("Y-m-d h:i:s"),
                'visible'=> 1
            );
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
    function editMotifAbsence($id) {
        if ($_POST) {
            $ins = array(
                'name' => $this->input->post('name'),
                'description'=>$this->input->post('description'),
                'update_date'=>date("Y-m-d h:i:s")
            );
            $this->referentiels->updateReferentielById($ins, $id);

            redirect('settings/paiecnss');
        } else {
            $this->theme_view = 'modal';
            $this->view_data['title'] = $this->lang->line('application_edit');
            $this->view_data['form_action'] = 'settings/editEtatProjet/'.$id;
            $this->view_data['data']= $this->referentiels->getReferentielsById($id);
            $this->content_view = 'settings/addref';
        }
    }


    /*******************************************************************
     * Ajout d'un motif d'absence
     * @return [type]
     *******************************************************************/
    function ajoutStatutConges(){
        if ($_POST) {
            $type = RefType::find($option=array("name"=>$this->config->item('type_code_statut_conges')));
            if(!$type or is_null($type)){
                show_error("Veuillez vérifier le paramtérage des statuts d'absences des congés", "404", $heading = 'Une erreur a été rencontrée');
            }
            $data = array(
                'id_type'=> $type->id ,
                'name' => $this->input->post('name'),
                'description'=>$this->input->post('description'),
                'created_date'=>date("Y-m-d h:i:s"),
                'visible'=> 1
            );
            $this->db->insert('ref_type_occurences', $data);
            redirect('settings/paiecnss');
        } else {
            $this->theme_view = 'modal';
            $this->view_data['title'] = $this->lang->line('application-add');
            $this->view_data['form_action'] = 'settings/ajoutStatutConges';
            $this->content_view = 'settings/addref';
        }
    }

    /**
     * Edition d'un motif d'absence
     * @param  [type]
     * @return [type]
     */
    function editStatutConges($id) {
        if ($_POST) {
            $ins = array(
                'name' => $this->input->post('name'),
                'description'=>$this->input->post('description'),
                'update_date'=>date("Y-m-d h:i:s")
            );
            $this->referentiels->updateReferentielById($ins, $id);

            redirect('settings/paiecnss');
        } else {
            $this->theme_view = 'modal';
            $this->view_data['title'] = $this->lang->line('application_edit');
            $this->view_data['form_action'] = 'settings/editStatutConges/'.$id;
            $this->view_data['data']= $this->referentiels->getReferentielsById($id);
            $this->content_view = 'settings/addref';
        }
    }

	//fonction notification//015649017676

	function notification(){
		$this->load->database();
		$this->db->select('email_notification');
		$this->db->from('core');
		$this->db->where('id', '2');
		$liste = $this->db->get()->result()[0];
		$this->view_data['form_action'] = 'settings/editemail/';
		$this->view_data['data']= $liste;
		$this->content_view = 'settings/notification';

}

//function editliste email notification
function editemail(){
	$this->load->database();
	unset($_POST['send']);
	$data = array(
		'email_notification'=> $_POST['email_notification']
	);
	$id = 2;
	$this->db->set($data);
	$this->db->where('id', $id);
	$query = $this->db->update('core');

	   if(!$query){$this->session->set_flashdata('message', 'error:'.$this->lang->line('messages_erreur_email_success'));}
	   else{$this->session->set_flashdata('message', 'success:'.$this->lang->line('messages_save_email_success'));}
	redirect('settings/notification');
}
	

}

