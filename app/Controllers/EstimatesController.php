<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class EstimatesController extends BaseController {
               
	var $idTypeRefDevis;

	function __construct()
	{
		parent::__construct();
		if($this->client){
		}elseif($this->user){
		}else{
			redirect('login');
		}
		$this->load->database();

		$this->load->model('item_model','item');
		$this->load->model('projects_model','project');
		$this->load->model('Facture_model','facture');
		$this->load->model('Ref_type_occurences_model','referentiels');
		$this->load->model('RefType_model','refType');
		$this->load->model('invoiceHasItem_model','invoiceHasItem');
		$this->load->model('Company_model','company');
		$this->load->model('estimate_model');
		$this->load->model('salarie_model');

			$this->load->helper('calcul_helper');

		$this->idTypeRefDevis= $this->config->item("type_id_etat_devis");  
		$submenus= $this->referentiels->getReferentielsByIdType($this->idTypeRefDevis);
		
		$this->view_data['submenu'][$this->lang->line('application_all')] = 'estimates'; 
		foreach($submenus as $submenu){
			if($submenu->name !="Sent" && $submenu->name !="Pending"){
				if($this->lang->line('application_'.$submenu->name) == false){
					$this->view_data['submenu'][$submenu->name] = 'estimates/filter'.$submenu->name; 
				}else{
					$this->view_data['submenu'][$this->lang->line('application_'.$submenu->name)] = 'estimates/filter'.$submenu->name; 
				}
			}
		}
	}	
	
	// Afficher la liste des devis
	function index()
	{
//////////$this->view_data['estimates']=$this->estimate_model->idsal($ids);
		$options = array('conditions' => array('estimate != ? ORDER BY id DESC',0));
		$this->view_data['estimates'] = Invoice::find('all', $options);

		//get the name of devis 
		$invoices = $this->view_data['estimates']; 
		$document = $_GET['document'];
		$department = $_GET['department'];

        switch ($department) {
            case 'mms':
                $invoices = $this->estimate_model->getmms();
                break;
		    case 'bim2d':
				$invoices = $this->estimate_model->getbim2d();
				break;
			case 'bim3d':
				$invoices = $this->estimate_model->getbim3d();
						//var_dump($invoicess);exit;
				break;

            default:
                break;
        }

		  switch ($document) {
            case 'devis':
				$invoices = $this->estimate_model->getdevisdocument();
                break;
            case 'attachement':
                $invoices = $this->estimate_model->getattdocument();
                break;
		
			default:
                break;
        }
		//var_dump(  $invoices);exit;
	  
	

		$this->view_data['estimates'] = array(
            'document' => $document,
			'department' => $department

        );



		    //var_dump($this->view_data['estimates'] );exit;
    // chiffre devise 
    $this->view_data['chiffre'] = (object) array(); 
    foreach ($invoices as $invoice){
        $refTypeCurrency = $this->refType->getRefTypeByName($invoice->currency)->id; 				
        $this->view_data['estimates'] = $this->referentiels->getReferentielsByIdType($refTypeCurrency)->name;
    }

    foreach ($invoices as $invoice)
    {  
             
        $idType= $this->refType->getRefTypeByName($invoice->currency)->id;   
        $chiffre = $this->referentiels->getReferentielsByIdType($idType)[0]->name;
        //$chiffre->description = $invoice->currency; 
        //chiffre de devise 
        $invoice->currency = $chiffre;
    }
    $this->view_data['estimates']= $invoices; 
    //var_dump($invoices);exit;
    $days_in_this_month = days_in_month(date('m'), date('Y'));
    $lastday_in_month =  date('Y-m-'.$days_in_this_month);
    $firstday_in_month =  date('Y-m-1');
    /* les statistiques à refaire
    $declined=$this->db->query('Select count(id) as total from invoices where estimate_status="Declined" and estimate_accepted_date !=0 and estimate_accepted_date <="'.$lastday_in_month.'" and estimate_accepted_date >= "'.$firstday_in_month.'"');
    $this->view_data['estimates_refused_this_month'] = $declined->result()[0]->total;
    $accepted=$this->db->query('Select count(id) as total from invoices where estimate_status="Accepted" and estimate_accepted_date !=0 and estimate_accepted_date <="'.$lastday_in_month.'" and estimate_accepted_date >= "'.$firstday_in_month.'"');
    $this->view_data['estimates_accepted_this_month'] = $accepted->result()[0]->total;
    $factured=$this->db->query('Select COUNT(id) as total from invoices where estimate_status="Invoiced" and estimate_accepted_date !=0 and estimate_accepted_date <="'.$lastday_in_month.'" and estimate_accepted_date >= "'.$firstday_in_month.'"');
    $this->view_data['estimates_factured_this_month']=$factured->result()[0]->total;
    */
    $now = time();
    $beginning_of_week = strtotime('last Monday', $now); // BEGINNING of the week
    $end_of_week = strtotime('next Sunday', $now) + 86400; // END of the last day of the week
    $this->view_data['estimates_due_this_month_graph'] = Invoice::find_by_sql('select count(id) AS "amount", DATE_FORMAT(`due_date`, "%w") AS "date_day", DATE_FORMAT(`due_date`, "%Y-%m-%d") AS "date_formatted" from invoices where UNIX_TIMESTAMP(`due_date`) >= "'.$beginning_of_week.'" AND UNIX_TIMESTAMP(`due_date`) <= "'.$end_of_week.'" AND estimate != 0');
    $this->view_data['estimates_paid_this_month_graph'] = Invoice::find_by_sql('select count(id) AS "amount", DATE_FORMAT(`paid_date`, "%w") AS "date_day", DATE_FORMAT(`paid_date`, "%Y-%m-%d") AS "date_formatted" from invoices where UNIX_TIMESTAMP(`paid_date`) >= "'.$beginning_of_week.'" AND UNIX_TIMESTAMP(`paid_date`) <= "'.$end_of_week.'" AND estimate != 0');
    $this->content_view = 'estimates/all';
	}
	
	//filtrer les devis
	function filter($condition = FALSE,$year=FALSE)
	{
		if($condition == "False"){ 
			$estimates = $this->db->get('invoices')->result();
			foreach ($estimates as $key =>$estimate) {
				$date = DateTime::createFromFormat("Y-m-d", $estimate->issue_date);
				$date = $date->format("Y");
				if($date != $year){ 
					unset($estimates[$key]);
				}
			}
		}else { 
			$condition = urldecode($condition);
			//get the id of status 
			$idType= $this->refType->getRefTypeByName('Devis')->id;  
			$idState = $this->referentiels->getReferentiels($idType,$condition)->id; 
			$this->db->where("status =".$idState);
			$estimates = $this->db->get('invoices')->result();
		}
		foreach ($estimates as $estimate)
		{		
			
			$idType= $this->refType->getRefTypeByName($estimate->currency)->id;   
			$chiffre = $this->referentiels->getReferentielsByIdType($idType);
			$chiffre->description = $estimate->currency; 
			//chiffre de devise 
			$estimate->currency = $chiffre; 
			$estimate->company = Company::find($estimate->company_id); 
		}  
		$this->view_data['estimates'] = $estimates;
		$opt=array("id_vcompanies"=>$_SESSION['current_company']);
		$this->view_data['settings'] = Setting::find($opt);
		$this->content_view = 'estimates/all';
	}
	
	// Créer un devis
	function create()
	{	
		if($_POST)
		{ 
			$estimate_reference = Setting::first();
			$refType = $this->refType->getRefTypeByName($_POST['currency'])->id; 
			$chiffre= $this->referentiels->getReferentielsByIdType($refType)->name; 
			//ajouter client passager 
			if ($_POST['company'] == 1)
			{ 
				$company = Company::last();
				$ref = $company->reference; 
				$data = array(
						'name' => $_POST['nomClient'],
						'reference' => $ref +1,
						'passager' => '1',
						'tva' => $_POST['tva'],
						'guarantee'=> $_POST['guarantee']
				);
				$this->db->insert('companies',$data);
				$_POST['company_id'] = Company::last()->id;
				//company ref
				$new_company_reference = $estimate_reference->company_reference + 1; 
				$estimate_reference->update_attributes(array('company_reference' => $new_company_reference
				)); 
				unset($_POST['company']); 
			}
			//Project ref in devis 
			$tot=(float)($this->project->calculeheure($_POST['project_id'])->periode);
				$toth = str_replace(".5", ".3", $tot);
			$_POST['calcul_heure'] = ((float)($toth));
			$_POST['project_surface'] = ((float)($this->project->calculquantite($_POST['project_id'])->quantite));
               $_POST['delivery'] = $this->project->getProjectRef($_POST['project_id'])->delivery;
			    $_POST['chef_projet_client'] = $this->project->getProjectClientName($_POST['project_id'])->name_client;
				  $_POST['chef_projet'] = $this->project->getProjectName($_POST['project_id'])->name_bim;
				 $_POST['project_name'] = $this->project->getProjectRef($_POST['project_id'])->name;
				$_POST['project_ref'] = $this->project->getProjectRef($_POST['project_id'])->project_num;
				 //var_dump((float)($this->project->calculeheure($_POST['project_id'])->periode)); exit;	
				
				//var_dump($toth); exit;
			if($_POST['name']!= ''){
				// last project : ref = ref +1 
				$lastProject = Project::last();
				$_POST['project_id'] = $lastProject->reference + 1; 
				//-------------------------- Project name 		
				$project_pieces = explode("-",strrev($estimate_reference->project_prefix));
				$var = date("Y-m-d",strtotime($_POST['start']));
				$pieces = explode("-", $var);
				$piecesYear = $pieces[0]; 
				$piecesMounth = $pieces[1];
				$subpiecesYear = explode('0', $pieces[0]);
				//si le num de ref est inf à 10 
				if ($_POST['project_id'] < 10)
				{
					$_POST['project_id']= '0'.$_POST['project_id'];
				}				
				// année 
				if ($project_pieces[0]=='YY')
				{
					$numero = strrev($project_pieces[1]).$subpiecesYear[1].$_POST['project_id'] ;
				}
				//année + mois 
				else
				{	
					$numero = strrev($project_pieces[2]).$subpiecesYear[1].$piecesMounth.$_POST['project_id'];	
				}
				$d = array(
						'name' =>$_POST['name'],
						'reference' =>$lastProject->reference + 1 ,
						'datetime' => time(), 
						'progress' => 0, 
						'start' => $_POST['start'], 
						'end' => $_POST['end'],
						'project_num' => $numero, 
						'company_id' => $_POST['company_id']
				);
				$this->db->insert('projects',$d);
				//increment ref in core 
				$new_project_reference = $estimate_reference->project_reference+1;
				$estimate_reference->update_attributes(array('project_reference' => $new_project_reference)); 
				$_POST['project_ref']= $numero; 			
			}
			unset($_POST['name']); 
			unset($_POST['start']);
			unset($_POST['end']); 
			unset($_POST['nomClient']); 
			unset($_POST['tva']); 
			unset($_POST['guarantee']); 
			unset($_POST['send']);
			unset($_POST['_wysihtml5_mode']);
			unset($_POST['files']);
			$_POST['estimate'] = 1;
			$_POST['creation_date'] = date("Y-m-d");
			//get referentiels of  devis 
			$idType= $this->refType->getRefTypeByName('Devis')->id;  
			
			$year = date("Y"); 
			$lastestimate= Invoice::last();
			$lastRef = explode('-',(date_format($lastestimate->creation_date,'Y-m-d'))); 
			if($lastRef[0] != $year){
				$_POST['estimate_reference'] = '01';		
			}else {
				$_POST['estimate_reference'] = $_POST['reference'];
			}
			//-------------------------- Devis name 	
			$estimate_pieces = explode("-",strrev($estimate_reference ->estimate_prefix));
			$var = date("y-m-d",strtotime($_POST['issue_date']));
			$pieces = explode("-", $var);
			$piecesYear = $pieces[0]; 
			$piecesMounth = $pieces[1];
			$subpiecesYear = explode(' ', $pieces[0]);	
			// année 
			if ($estimate_pieces[0] == "YY")
			{
				$_POST['estimate_num'] = strrev($estimate_pieces[1]).$subpiecesYear[0].
				$_POST['estimate_reference'];
			}
			//année + mois 
			else{	
				$_POST['estimate_num'] = strrev($estimate_pieces[2]).$subpiecesYear[0].$piecesMounth.
				$_POST['estimate_reference'];	
			}
			
			$estimate = Invoice::create($_POST);
			$new_estimate_reference = $_POST['estimate_reference']+1;
			$estimate_reference->update_attributes(array('estimate_reference' => $new_estimate_reference));
       		if(!$estimate){$this->session->set_flashdata('message', 'error:'.$this->lang->line('messages_create_estimate_error'));}
       		else{$this->session->set_flashdata('message', 'success:'.$this->lang->line('messages_create_estimate_success'));}
			redirect('estimates');
		}else
		{
			$this->view_data['estimates'] = Invoice::all();
			$this->view_data['projects'] = Project::all();
			$this->view_data['companies'] = Company::find('all',array('conditions' => array('inactive=?',0)));			
			$this->view_data['next_reference'] = Invoice::last();
			$idType = $this->refType->getRefTypeByName("Devise")->id; 
			$this->view_data['currencys'] = $this->referentiels->getReferentielsByIdType($idType);
			$option=array("id_vcompanies"=>$_SESSION['current_company']);
			$settings=setting::find($option);
			$this->view_data['current_echeance']=date('Y-m-d', strtotime($date . " +".$settings->echeance."days"));
			$current_date = explode('-',date('Y-m-d'));
			if($current_date[1] == "01" && $current_date[2] == "01"){
				$settings->update_attributes(array('estimate_reference' => "1")); 
			}
			$this->view_data['state'] = $this->referentiels->getReferentielsByIdType($this->idTypeRefDevis); 
			$this->view_data['current_date'] = date('Y-m-d'); 
			//revert ref estimate 
			$year = date("Y"); 
			$lastestimate= Invoice::last();
			$lastRef = explode('-',(date_format($lastestimate->creation_date,'Y-m-d'))); 
			if($lastRef[0] != $year ){
				$settings->estimate_reference = 1; 		
			}
			$this->view_data['core_settings'] = $settings;
			$this->theme_view = 'modal';
			$this->view_data['title'] = $this->lang->line('application_create_estimate');
			$this->view_data['form_action'] = 'estimates/create';
			$this->content_view = 'estimates/_estimate';
		}	
	}	
	



	function createb()
	{	
		if($_POST)
		{ 
			$estimate_reference = Setting::first();
			$refType = $this->refType->getRefTypeByName($_POST['currency'])->id; 
			$chiffre= $this->referentiels->getReferentielsByIdType($refType)->name; 
			//ajouter client passager 
			if ($_POST['company'] == 1)
			{ 
				$company = Company::last();
				$ref = $company->reference; 
				$data = array(
						'name' => $_POST['nomClient'],
						'reference' => $ref +1,
						'passager' => '1',
						'tva' => $_POST['tva'],
						'guarantee'=> $_POST['guarantee']
				);
				$this->db->insert('companies',$data);
				$_POST['company_id'] = Company::last()->id;
				//company ref
				$new_company_reference = $estimate_reference->company_reference + 1; 
				$estimate_reference->update_attributes(array('company_reference' => $new_company_reference
				)); 
				unset($_POST['company']); 
			}
			//Project ref in devis 
			
			$_POST['calcul_heure'] = ((float)($this->project->calculeheure($_POST['project_id'])->periode));
			$_POST['project_surface'] = ((float)($this->project->calculquantite($_POST['project_id'])->quantite));
               $_POST['delivery'] = $this->project->getProjectRef($_POST['project_id'])->delivery;
			    $_POST['chef_projet_client'] = $this->project->getProjectClientName($_POST['project_id'])->name_client;
				  $_POST['chef_projet'] = $this->project->getProjectName($_POST['project_id'])->name_bim;
				 $_POST['project_name'] = $this->project->getProjectRef($_POST['project_id'])->name;
				$_POST['project_ref'] = $this->project->getProjectRef($_POST['project_id'])->project_num;
				// var_dump($this->project->getProjectName($_POST['project_id'])->name_bim); exit;	
			
			if($_POST['name']!= ''){
				// last project : ref = ref +1 
				$lastProject = Project::last();
				$_POST['project_id'] = $lastProject->reference + 1; 
				//-------------------------- Project name 		
				$project_pieces = explode("-",strrev($estimate_reference->project_prefix));
				$var = date("Y-m-d",strtotime($_POST['start']));
				$pieces = explode("-", $var);
				$piecesYear = $pieces[0]; 
				$piecesMounth = $pieces[1];
				$subpiecesYear = explode('0', $pieces[0]);
				//si le num de ref est inf à 10 
				if ($_POST['project_id'] < 10)
				{
					$_POST['project_id']= '0'.$_POST['project_id'];
				}				
				// année 
				if ($project_pieces[0]=='YY')
				{
					$numero = strrev($project_pieces[1]).$subpiecesYear[1].$_POST['project_id'] ;
				}
				//année + mois 
				else
				{	
					$numero = strrev($project_pieces[2]).$subpiecesYear[1].$piecesMounth.$_POST['project_id'];	
				}
				$d = array(
						'name' =>$_POST['name'],
						'reference' =>$lastProject->reference + 1 ,
						'datetime' => time(), 
						'progress' => 0, 
						'start' => $_POST['start'], 
						'end' => $_POST['end'],
						'project_num' => $numero, 
						'company_id' => $_POST['company_id']
				);
				$this->db->insert('projects',$d);
				//increment ref in core 
				$new_project_reference = $estimate_reference->project_reference+1;
				$estimate_reference->update_attributes(array('project_reference' => $new_project_reference)); 
				$_POST['project_ref']= $numero; 			
			}
			unset($_POST['name']); 
			unset($_POST['start']);
			unset($_POST['end']); 
			unset($_POST['nomClient']); 
			unset($_POST['tva']); 
			unset($_POST['guarantee']); 
			unset($_POST['send']);
			unset($_POST['_wysihtml5_mode']);
			unset($_POST['files']);
			$_POST['estimate'] = 1;
			$_POST['creation_date'] = date("Y-m-d");
			//get referentiels of  devis 
			$idType= $this->refType->getRefTypeByName('Devis')->id;  
			
			$year = date("Y"); 
			$lastestimate= Invoice::last();
			$lastRef = explode('-',(date_format($lastestimate->creation_date,'Y-m-d'))); 
			if($lastRef[0] != $year){
				$_POST['estimate_reference'] = '01';		
			}else {
				$_POST['estimate_reference'] = $_POST['reference'];
			}
			//-------------------------- Devis name 	
			$estimate_pieces = explode("-",strrev($estimate_reference ->estimate_prefix));
			$var = date("y-m-d",strtotime($_POST['issue_date']));
			$pieces = explode("-", $var);
			$piecesYear = $pieces[0]; 
			$piecesMounth = $pieces[1];
			$subpiecesYear = explode(' ', $pieces[0]);	
			// année 
			if ($estimate_pieces[0] == "YY")
			{
				$_POST['estimate_num'] = strrev($estimate_pieces[1]).$subpiecesYear[0].
				$_POST['estimate_reference'];
			}
			//année + mois 
			else{	
				$_POST['estimate_num'] = strrev($estimate_pieces[2]).$subpiecesYear[0].$piecesMounth.
				$_POST['estimate_reference'];	
			}
			
			$estimate = Invoice::create($_POST);
			$new_estimate_reference = $_POST['estimate_reference']+1;
			$estimate_reference->update_attributes(array('estimate_reference' => $new_estimate_reference));
       		if(!$estimate){$this->session->set_flashdata('message', 'error:'.$this->lang->line('messages_create_estimate_error'));}
       		else{$this->session->set_flashdata('message', 'success:'.$this->lang->line('messages_create_estimate_success'));}
			redirect('estimates');
		}else
		{
			$this->view_data['estimates'] = Invoice::all();
			$this->view_data['projects'] = Project::all();
			$this->view_data['companies'] = Company::find('all',array('conditions' => array('inactive=?',0)));			
			$this->view_data['next_reference'] = Invoice::last();
			$idType = $this->refType->getRefTypeByName("Devise")->id; 
			$this->view_data['currencys'] = $this->referentiels->getReferentielsByIdType($idType);
			$option=array("id_vcompanies"=>$_SESSION['current_company']);
			$settings=setting::find($option);
			$this->view_data['current_echeance']=date('Y-m-d', strtotime($date . " +".$settings->echeance."days"));
			$current_date = explode('-',date('Y-m-d'));
			if($current_date[1] == "01" && $current_date[2] == "01"){
				$settings->update_attributes(array('estimate_reference' => "1")); 
			}
			$this->view_data['state'] = $this->referentiels->getReferentielsByIdType($this->idTypeRefDevis); 
			$this->view_data['current_date'] = date('Y-m-d'); 
			//revert ref estimate 
			$year = date("Y"); 
			$lastestimate= Invoice::last();
			$lastRef = explode('-',(date_format($lastestimate->creation_date,'Y-m-d'))); 
			if($lastRef[0] != $year ){
				$settings->estimate_reference = 1; 		
			}
			$this->view_data['core_settings'] = $settings;
			$this->theme_view = 'modal';
			$this->view_data['title'] ='Attachement BIM';
			$this->view_data['form_action'] = 'estimates/createb';
			$this->content_view = 'estimates/_estimateb';
		}	
	}	
	
	
	
	// Mettre à jour un devis
	// Mettre à jour un devis
	function update($id = FALSE, $getview = FALSE)
	{	
		if($_POST)
		{
			$id = $_POST['id'];
			
			$estimate_reference = Setting::first();
			$refType = $this->refType->getRefTypeByName($_POST['currency'])->id; 
			$chiffre= $this->referentiels->getReferentielsByIdType($refType)->name; 
			$estimate = Invoice::find($id);	
			if ($estimate->company_id !=0){
				$company= Company::find($estimate->company_id); 
			}
			
			//ajouter client passager 
			if ($_POST['company'] == 1)
			{ 
				if($company->passager != 1){
					$company = Company::last();
					$ref = $company->reference; 
					$data = array(
							'name' => $_POST['nomClient'],
							'id_vcompanies' =>$_SESSION['current_company'],
							'reference' => $ref +1,
							'passager' => '1',
							'tva' => $_POST['tva'],
							'guarantee'=> $_POST['guarantee']
							//'timbre_fiscal' => $_POST['timbre_fiscal']
					);
					$this->db->insert('companies',$data);
					$_POST['company_id'] = Company::last()->id;
					//company ref
					$new_company_reference = $estimate_reference->company_reference + 1; 
					$estimate_reference->update_attributes(array('company_reference' => $new_company_reference
					)); 
				}else {
					$data = array(
							'name' => $_POST['nomClient'],
							'tva' => $_POST['tva'],
							'guarantee'=> $_POST['guarantee']
							//'timbre_fiscal' => $_POST['timbre_fiscal']
					);
					$company->update_attributes($data);  
				}
				unset($_POST['company']); 
			}				
			//Project ref in facture 
			if($_POST['project_id'] != 0){
			
			 $_POST['project_surface'] = $this->project->getProjectRef($_POST['project_id'])->surface;
				 $_POST['project_name'] = $this->project->getProjectRef($_POST['project_id'])->name;
				$_POST['project_ref'] = $this->project->getProjectRef($_POST['project_id'])->project_num;
			}
			if($_POST['name']!= ''){
				// last project : ref = ref +1 
				$lastProject = Project::last();
				$_POST['project_id'] = $lastProject->reference + 1; 
				//-------------------------- Project name 		
				$project_pieces = explode("-",strrev($estimate_reference->project_prefix));
				$var = date("Y-m-d",strtotime($_POST['start']));
				$pieces = explode("-", $var);
				$piecesYear = $pieces[0]; 
				$piecesMounth = $pieces[1];
				$subpiecesYear = explode('0', $pieces[0]);
				//si le num de ref est inf à 10 
				if ($_POST['project_id'] < 10)
				{
					$_POST['project_id']= '0'.$_POST['project_id'];
				}				
				// année 
				if ($project_pieces[0]=='YY')
				{
					$numero = strrev($project_pieces[1]).$subpiecesYear[1].$_POST['project_id'] ;
				}
				//année + mois 
				else
				{	
					$numero = strrev($project_pieces[2]).$subpiecesYear[1].$piecesMounth.$_POST['project_id'];	
				}
				$d = array(
						'name' =>$_POST['name'],
						'id_vcompanies' =>$_SESSION['current_company'],
						'reference' =>$lastProject->reference + 1 ,
						'datetime' => time(), 
						'progress' => 0, 
						'start' => $_POST['start'], 
						'end' => $_POST['end'],
						'project_num' => $numero, 
						'company_id' => $_POST['company_id']
				); 
				$this->db->insert('projects',$d);
				//increment ref in core 
				$new_project_reference = $estimate_reference->project_reference+1;
				$estimate_reference->update_attributes(array('project_reference' => $new_project_reference)); 	
				$estimate = Invoice::find($_POST['id']);	
				$estimate->update_attributes(array('project_id' => $estimate_reference->project_reference-1));
				$_POST['project_ref']= $numero;
			}
			unset($_POST['name']); 
			unset($_POST['reference']); 
			unset($_POST['start']);
			unset($_POST['end']); 
			unset($_POST['nomClient']); 
			unset($_POST['tva']); 
			unset($_POST['guarantee']); 
			unset($_POST['send']);
			unset($_POST['_wysihtml5_mode']);
			unset($_POST['files']);
			$view = FALSE;
			if(isset($_POST['view']))
			{
				$view = $_POST['view'];
			}
			unset($_POST['view']);
			
			//get referentiels of devis 
			$idType= $this->refType->getRefTypeByName('Devis')->id;  

			$this->db->where('id',$id);
			$this->db->set($_POST);
			$invoice=$this->db->update('invoices');
       		if(!$estimate){$this->session->set_flashdata('message', 'error:'.$this->lang->line('messages_save_estimate_error'));}
       		else{$this->session->set_flashdata('message', 'success:'.$this->lang->line('messages_save_estimate_success'));}		
			if($view == 'true'){redirect('estimates/view/'.$id);}else{redirect('estimates');}
		}else
		{
			$this->view_data['projects'] = Project::all();	
			$this->view_data['companies'] = Company::find('all',array('conditions' => array('inactive=? ',0)));
			$this->view_data['estimate'] = Invoice::find($id);
			//get name status of devis 
			
			$idType = $this->refType->getRefTypeByName("Devise")->id; 
			$this->view_data['currencys'] = $this->referentiels->getReferentielsByIdType($idType); 
			
			$this->view_data['state'] = $this->referentiels->getReferentielsByIdType($this->idTypeRefDevis);
			
			if($getview == "view"){$this->view_data['view'] = "true";}
			$this->theme_view = 'modal';
			$this->view_data['title'] = $this->lang->line('application_edit_estimate');
			$this->view_data['form_action'] = 'estimates/update';
			if($this->view_data['estimate']->company_id !=0)
			{
				$this->view_data['company']=Company::find($this->view_data['estimate']->company_id);
			}
			//get projet by ref  
			$this->view_data['projectId'] = $this->project->getProjectByid($this->view_data['estimate']->project_id )->id;
	
			$this->content_view = 'estimates/_estimate';
		}	
	}	
	
	
	// Afficher le détail d'un devis
	function view($id = FALSE)
	{

		$this->view_data['submenu'] = array(
			$this->lang->line('application_back') => 'estimates',
		);

		$this->view_data['estimate'] = Invoice::find($id);
		
		if($this->view_data['estimate']->project_id != 0){
			$option=array("reference"=>$this->view_data['estimate']->project_id);
			$this->view_data['project'] = Project::find($option);
		}
		//chiffre devise 
		$refType = $this->refType->getRefTypeByName($this->view_data['estimate']->currency)->id; 
		$chiffre= $this->referentiels->getReferentielsByIdType($refType)[0]->name; 
		//var_dump($chiffre);exit;	
		$this->view_data['chiffre'] = $chiffre; 
		$this->view_data['company']= Company::find_by_id($this->view_data['estimate']->company_id); 
		$option=array("id_vcompanies"=>$_SESSION['current_company']);
		$data["core_settings"] = Setting::find($option);
		$estimate = $this->view_data['estimate'];
		$this->view_data['items'] = $this->db->query("SELECT * FROM invoice_has_items WHERE invoice_id = $id ORDER BY position ASC")->result();
		//calculate sum
		$i = 0; $sum = 0; 
		foreach ($this->view_data['items'] as $value) {
			$SousTotal = ($value->amount * $value->value ) - ( $value->amount * $value->value * $value->discount) / 100;
			$SousTotalTVA = $SousTotal + ($SousTotal * $value->tva) / 100;
            $totalTVA += $SousTotalTVA;
            $total += $SousTotal;	
		}

		//exonéré du TVA
		if($this->view_data['company']->tva == 1){
			$sum = $total;
		}else {
			$sum = $totalTVA; 			
		}
		//var_dump($id);

		//total hors tax 
		$sumht=$total;
		// discount in invoice   
		$sum = $sum - ($sum/100)* $this->view_data['estimate']->discount; 
		$sumht = $sumht -($sumht/100)* $this->view_data['estimate']->discount;  
		//Calcul retenue guarantee
		if($this->view_data['company']->guarantee == 1){
			$sum = $sum - ($sum * 10)/100;
		}
        /**********************/
		$estimate->sumht = $sumht; 
		$estimate->sum = $sum;
		$estimate->save();
		
		$this->view_data['project']=$this->project->getProjectById($this->view_data['estimate']->project_id);
		$contact_id = $this->view_data['company']->client_id;
		$this->view_data['contact_principale'] = $this->company->getClientById($contact_id);
		$this->content_view = 'estimates/view';
	}

	function estimateToInvoice($id = FALSE, $getview = FALSE)
	{	
	
		$option=array("id_vcompanies"=>$_SESSION['current_company']);
		$settings=setting::find($option);
		$this->db->where('id',$id);
		$devis=$this->db->get('invoices')->result()[0];
		unset($devis->id);
		unset($devis->id_facture);
		$devis->estimate=0;
		$devis->estimate_reference=$settings->invoice_reference+1;
		
		$devis->reference=$settings->invoice_reference+1;
		
		$this->db->order_by("id","desc");
		$factureid=$this->db->get('facture')->result()[0]->id;
		$factureid=$factureid+1;
		
		$new_estimate_reference = $settings->invoice_reference+2;
		
		$option=array("id_vcompanies"=>$_SESSION['current_company']);
		$estimate_reference = Setting::find($option);
		$estimate_reference->update_attributes(array('invoice_reference' => $new_estimate_reference));
		
		$set=array("id_facture"=>$factureid,
				   "status"=> $this->config->item("occ_devis_facture"),
				   "estimate_accepted_date"=>date('Y-m-d'),
				   "timbre_fiscal"=>$settings->timbre_fiscal
				   );
		$this->db->where('id',$id);
		$this->db->set($set);
		$this->db->update('invoices');
		$devis->id=$factureid;
		unset($devis->due_date);

		$factured=$this->db->insert('facture',$devis);
		$idfacture=$this->db->query('select id from facture ORDER BY id desc')->result()[0]->id;
		$this->db->where('invoice_id',$id);
		$items=$this->db->get('invoice_has_items')->result();

		foreach ($items as $item ) {
			unset($item->id);
			unset($item->invoice_id);
			$item->facture_id=$idfacture;
			$item->name=$idfacture;
		$this->db->insert('facture_has_items',$item);
		}
		var_dump($item->name);exit;	
		if(!$factured){$this->session->set_flashdata('message', 'error:'.$this->lang->line('messages_delete_factured_error'));}
				else{$this->session->set_flashdata('message', 'success:'.$this->lang->line('messages_delete_factured_success'));}
				redirect('invoices/view/'.$idfacture);
			
		}
		
	function delete($id = FALSE)
	{	
		$estimate = Invoice::find($id);
		$estimate->delete();
		$this->content_view = 'estimates/all';
		if(!$estimate){$this->session->set_flashdata('message', 'error:'.$this->lang->line('messages_delete_estimate_error'));}
			else{$this->session->set_flashdata('message', 'success:'.$this->lang->line('messages_delete_estimate_success'));}
			redirect('estimates');
	}
	function previewe($id = FALSE){

		$this->load->helper(array( 'file')); 
		$this->load->library('parser');

		$data["estimate"] = Invoice::find($id); 
		// $cc=$this->$data["estimate"]->project_id;
		

		$this->db->where('invoice_id',$id);
		$this->db->order_by('position','asc');
		$data['items'] = $this->db->get('invoice_has_items')->result();
		$countDiscount = $this->db->query("SELECT discount FROM invoice_has_items WHERE invoice_id = '".$id."' AND discount > 0")->result();
                $data['countDiscount'] = count($countDiscount);
		
		$this->db->where('id',$_SESSION['current_company']);
		$data['vcompanies']=$this->db->get('v_companies')->result();
		$data["core_settings"] = Setting::first();
		if($data["estimate"]->project_id != 0){
			$data['num_project']= $this->project->getProjectById($data["estimate"]->project_id)->id;
			
		}
		
		//var_dump($id);exit;
		$this->db->where('id',$_SESSION['current_company']);
		$logo=$this->db->get('v_companies')->result()[0]->picture;
		$data['logo']=$logo;
		$this->db->where('id',$data['estimate']->company_id);
		$company=$this->db->get('companies')->result()[0];
		$data['company']=$company;
		//var_dump($data);
		$this->db->where('id',$data['company']->client_id);
		$due_date = date($data["core_settings"]->date_format, human_to_unix($data["estimate"]->due_date.' 00:00:00'));  
		//chiffre of Devise
		$ref=$this->refType->getRefTypeByName($data["estimate"]->currency)->id; 
		 	
		$data['chiffre']=$this->referentiels->getReferentielsByIdType($this->refType->getRefTypeByName($data["estimate"]->currency)->id)[0]->name; 
		
		$parse_data = array(
							'due_date' => $due_date,
							'estimate_id' => $data["core_settings"]->estimate_prefix.$data["estimate"]->estimate_reference,
							'client_link' => $data["core_settings"]->domain,
							'company' => $data["core_settings"]->company,
							);
							
							
		$cc=$data['estimate']->project_id;
		//var_dump($cc);exit;

		$refP=($this->estimate_model->getByRef($cc)->refp);
	   $data['refP']=$refP;
	 //var_dump($refP); exit;


	$this->estimate_model->getByIdp($cc);

	$totalg=((float)($this->project->calculquantitelg($cc)->quantitelg));
	$data['totalg']=$totalg;
	//var_dump($totalg); exit;
		$html = $this->load->view($data["core_settings"]->template.'/templates/estimate/bluelinee', $data, true); 
		//var_dump($data["core_settings"]->template.'/templates/estimate/bluelinee', $data, true);
		$html = $this->parser->parse_string($html, $parse_data);
		$filename = $data['estimate']->project_name.'_ATT('.$data['estimate']->project_ref.')';
		//var_dump($data["core_settings"]);
		//Générer le pdf
		$this->pdf->load_view($html, $filename);
	}
	
	function previewb($id = FALSE){




		$this->load->helper(array( 'file')); 
		$this->load->library('parser');

		$data["estimate"] = Invoice::find($id); 
		// $cc=$this->$data["estimate"]->project_id;
		

		$this->db->where('invoice_id',$id);
		$this->db->order_by('position','asc');
		$data['items'] = $this->db->get('invoice_has_items')->result();
		$countDiscount = $this->db->query("SELECT discount FROM invoice_has_items WHERE invoice_id = '".$id."' AND discount > 0")->result();
                $data['countDiscount'] = count($countDiscount);
		
		$this->db->where('id',$_SESSION['current_company']);
		$data['vcompanies']=$this->db->get('v_companies')->result();
		
		$data["core_settings"] = Setting::first();
		if($data["estimate"]->project_id != 0){
			$data['num_project']= $this->project->getProjectById($data["estimate"]->project_id)->id;
			
		}
		
	
		$this->db->where('id',$_SESSION['current_company']);

		$logo=$this->db->get('v_companies')->result()[0]->picture;
		$data['logo']=$logo;
		$this->db->where('id',$data['estimate']->company_id);

		


		$company=$this->db->get('companies')->result()[0];
		$data['company']=$company;
		//var_dump($data);
		$this->db->where('id',$data['company']->client_id);
		$due_date = date($data["core_settings"]->date_format, human_to_unix($data["estimate"]->due_date.' 00:00:00'));  
		//chiffre of Devise
		$ref=$this->refType->getRefTypeByName($data["estimate"]->currency)->id; 
		 	
		$data['chiffre']=$this->referentiels->getReferentielsByIdType($this->refType->getRefTypeByName($data["estimate"]->currency)->id)[0]->name; 
		
		$parse_data = array(
							'due_date' => $due_date,
							'estimate_id' => $data["core_settings"]->estimate_prefix.$data["estimate"]->estimate_reference,
							'client_link' => $data["core_settings"]->domain,
							'company' => $data["core_settings"]->company,
							);
							
							
		$cc=$data['estimate']->project_id;
		$refP=($this->estimate_model->getByRef($cc)->refp);
	   $data['refP']=$refP;
	 //var_dump($refP); exit;
	$this->estimate_model->getByIdp($cc);
	$totalg=((float)($this->project->calculquantitelg($cc)->quantitelg));
	$data['totalg']=$totalg;

	$this->db->where('project_id',$cc);
	$data['tickets']=$this->db->get('tickets')->result();
	
	
	//$total=$data['tickets']=$this->project->calculeheureticket($data["tickets"]->id);
	$cc=$data['estimate']->project_id;
	$this->db->where('project_id',$cc);

	$idd=$data['ticktes']=$this->db->get('tickets')->result()[0]->id;
	
	foreach ($data['tickets'] as $ticket) {
		$ticket->total = $this->project->calculeheureticket($ticket->id)->periodeticket;
		
	}

	$data['id']=$idd;

	//var_dump($totalg); exit;
		$html = $this->load->view($data["core_settings"]->template.'/templates/estimate/bluelineeB', $data, true); 
		//var_dump($data["core_settings"]->template.'/templates/estimate/bluelineeB', $data, true);exit;
		$html = $this->parser->parse_string($html, $parse_data);
		$filename = $data['estimate']->project_name.'_ATT('.$data['estimate']->project_ref.')';
		//var_dump($data["core_settings"]);
		//Générer le pdf
		$this->pdf->load_view($html, $filename);
	}

	function preview($id = FALSE){

		$this->load->helper(array( 'file')); 
		$this->load->library('parser');

		$data["estimate"] = Invoice::find($id); 


		$this->db->where('invoice_id',$id);
		$this->db->order_by('position','asc');
		$data['items'] = $this->db->get('invoice_has_items')->result();
		$countDiscount = $this->db->query("SELECT discount FROM invoice_has_items WHERE invoice_id = '".$id."' AND discount > 0")->result();
                $data['countDiscount'] = count($countDiscount);
		
		$this->db->where('id',$_SESSION['current_company']);
		$data['vcompanies']=$this->db->get('v_companies')->result();
		$data["core_settings"] = Setting::first();
		if($data["estimate"]->project_id != 0){
			$data['num_project']= $this->project->getProjectById($data["estimate"]->project_id)->id;	
		}
		$this->db->where('id',$_SESSION['current_company']);
		$logo=$this->db->get('v_companies')->result()[0]->picture;
		$data['logo']=$logo;
		$this->db->where('id',$data['estimate']->company_id);
		$company=$this->db->get('companies')->result()[0];
		$data['company']=$company;
		//var_dump($data);
		$this->db->where('id',$data['company']->client_id);
		$due_date = date($data["core_settings"]->date_format, human_to_unix($data["estimate"]->due_date.' 00:00:00'));  
		//chiffre of Devise
		$ref=$this->refType->getRefTypeByName($data["estimate"]->currency)->id; 
		 	
		$data['chiffre']=$this->referentiels->getReferentielsByIdType($this->refType->getRefTypeByName($data["estimate"]->currency)->id)[0]->name; 
		
		$parse_data = array(
							'due_date' => $due_date,
							'estimate_id' => $data["core_settings"]->estimate_prefix.$data["estimate"]->estimate_reference,
							'client_link' => $data["core_settings"]->domain,
							'company' => $data["core_settings"]->company,
							);
		
		$html = $this->load->view($data["core_settings"]->template. '/' .$data["core_settings"]->estimate_pdf_template.$data["core_settings"]->default_template, $data, true); 
		//var_dump($data["core_settings"]->template. '/' .$data["core_settings"]->estimate_pdf_template.$data["core_settings"]->default_template, $data, true);
		$html = $this->parser->parse_string($html, $parse_data);
		$filename = $data['estimate']->estimate_num.'_'.$data['company']->name;
		//var_dump($data["core_settings"]);
		//Générer le pdf
		$this->pdf->load_view($html, $filename);
	}
	
	function sendestimate($id = FALSE)
	{
		$this->load->helper(array('dompdf', 'file'));
		$this->load->library('parser');
		$data["estimate"] = Invoice::find($id); 
		//check if client contact has permissions for estimates and grant if not
		if(isset($data["estimate"]->company->client->id)){
			$access = explode(",", $data["estimate"]->company->client->access);
			if(!in_array("107", $access)){
				$client_estimate_permission = Client::find_by_id($data["estimate"]->company->client->id);
				if($client_estimate_permission){
					$client_estimate_permission->access = $client_estimate_permission->access.",107";
					$client_estimate_permission->save();
				}
			}
			
		}
		$data["estimate"]->estimate_sent = date("Y-m-d");
		
		$data["core_settings"] = Setting::first();
		$due_date = date($data["core_settings"]->date_format, human_to_unix($data["estimate"]->due_date.' 00:00:00')); 
		//Set parse values
		$parse_data = array(
							'client_contact' => $data["estimate"]->company->client->firstname.' '.$data["estimate"]->company->client->lastname,
							'client_company' => $data["estimate"]->company->name,
							'due_date' => $due_date,
							'estimate_id' => $data["core_settings"]->estimate_prefix.$data["estimate"]->estimate_reference,
							'client_link' => $data["core_settings"]->domain,
							'company' => $data["core_settings"]->company,
							'logo' => '<img src="'.base_url().''.$data["core_settings"]->logo.'" alt="'.$data["core_settings"]->company.'"/>',
							'invoice_logo' => '<img src="'.base_url().''.$data["core_settings"]->invoice_logo.'" alt="'.$data["core_settings"]->company.'"/>'
							);
		// Generate PDF     
		$html = $this->load->view($data["core_settings"]->template. '/' .$data["core_settings"]->estimate_pdf_template, $data, true);
		$html = $this->parser->parse_string($html, $parse_data);
		$filename = $this->lang->line('application_estimate').'_'.$data["core_settings"]->estimate_prefix.$data["estimate"]->estimate_reference;
		pdf_create($html, $filename, FALSE);
		//email
		$subject = $this->parser->parse_string($data["core_settings"]->estimate_mail_subject, $parse_data);
		$this->email->from($data["core_settings"]->email, $data["core_settings"]->company);
		if(!isset($data["estimate"]->company->client->email)){
			$this->session->set_flashdata('message', 'error:This client company has no primary contact! Just add a primary contact.');
			redirect('estimates/view/'.$id);
		}
		$this->email->to($data["estimate"]->company->client->email); 
		$this->email->subject($subject); 
		$this->email->attach("files/temp/".$filename.".pdf");
		$email_estimate = read_file('./application/views/'.$data["core_settings"]->template.'/templates/email_estimate.html');
		$message = $this->parser->parse_string($email_estimate, $parse_data);
		$this->email->message($message);			
		if($this->email->send()){$this->session->set_flashdata('message', 'success:'.$this->lang->line('messages_send_estimate_success'));
		$data["estimate"]->update_attributes(array('status' => 'Sent', 'sent_date' => date("Y-m-d")));
		log_message('error', 'Estimate #'.$data["core_settings"]->estimate_prefix.$data["estimate"]->estimate_reference.' has been send to '.$data["estimate"]->company->client->email);
		}
		else{$this->session->set_flashdata('message', 'error:'.$this->lang->line('messages_send_estimate_error'));
		log_message('error', 'ERROR: Estimate #'.$data["core_settings"]->estimate_prefix.$data["estimate"]->estimate_reference.' has not been send to '.$data["estimate"]->company->client->email.'. Please check your servers email settings.');
		}
		unlink("files/temp/".$filename.".pdf");
		redirect('estimates/view/'.$id);
	}
	
	function convert($data, $index = 0) 
	{
		$output = array_filter($data, function($item) use ($index) {
			return $item->parent == $index;
		});
		$real_output = array();
		foreach ($output as $item) {
			$real_output[] = array('id' =>$item->id,'libelle' => $item->libelle, 'children' => $this->convert($data, $item->id));
		}
		return $real_output;
	}
	
	// Ajouter un article au devis
	function item($id = FALSE)
	{	
		if($_POST)
		{
			unset($_POST['send']);
			$_POST = array_map('htmlspecialchars', $_POST);
			if($_POST['name'] != ""){
				//insert new item 
				$d = array(
						'name' =>$_POST['name'],
						'value' =>$_POST['value'],
						'tva' =>$_POST['tva'],
						'name' =>$_POST['name'],
						'id_family' => $_POST['id_family'], 
						'unit' => $_POST['unit'],
						'description' =>  $_POST['description']
				);
				$this->db->insert('items',$d);
				$_POST['item_id'] = Item::last()->id;  
				
			}else{
				if($_POST['item_id'] == "-"){
					$this->session->set_flashdata('message', 'error:'.$this->lang->line('messages_add_item_error'));
					redirect('estimates/view/'.$_POST['invoice_id']);
				}else{
					$itemvalue = Item::find_by_id($_POST['item_id']);
					$_POST['name'] = $itemvalue->name;
					$_POST['value'] = $_POST['Prixunitaire'] ;
				}
			}
			unset($_POST['id_family']);
			unset($_POST['Prixunitaire']);
			$item = InvoiceHasItem::create($_POST);
       		if(!$item){$this->session->set_flashdata('message', 'error:'.$this->lang->line('messages_add_item_error'));}
       		else{$this->session->set_flashdata('message', 'success:'.$this->lang->line('messages_add_item_success'));}
			redirect('estimates/view/'.$_POST['invoice_id']);
		}else
		{
			$item_units = $this->db->query("SELECT * FROM item_units")->result();
			$this->view_data['item_units'] = $item_units;
			$this->view_data['estimate'] = Invoice::find($id);
			$idType= $this->refType->getRefTypeByName($this->view_data['estimate']->currency)->id;   
			$this->view_data['chiffre'] = $this->referentiels->getReferentielsByIdType($idType)->name;
			$company =  Company::find_by_id($this->view_data['estimate']->company_id);
			$this->view_data['company'] = $company;
			
			$this->view_data['items'] = Item::find('all',array('conditions' => array('inactive=?','0')));
			//Créer la liste des articles
			$list_items = array();
			$list_items['0'] = '-';

			foreach ($this->view_data['items']  as $value):
				$list_items[$value->id] = $value->name." - ".$value->value." ".$core_settings->currency;
			endforeach;
			$this->view_data['list_items'] =$list_items ;
			$this->view_data['title'] = $this->lang->line('application_add_item');
			$type=$this->db->query('SELECT id,libelle FROM items_has_family
			UNION
			SELECT id,libelle FROM items_has_family_parent')->result();
			$families=$this->db->query('select * from  items_has_family where inactive=0')->result();
			$this->view_data['families'] = $this->convert($families);
			$this->view_data['type']=$type;
			$this->view_data['tva']=$this->referentiels->getReferentielsByIdType($this->config->item("type_id_tva"));
			$this->view_data['form_action'] = 'estimates/item';
			$this->theme_view = 'modal';
			$this->content_view = 'estimates/_item';
		}	
	}
    function itemEmpty($id = FALSE)
	{	
		if($_POST){
			unset($_POST['send']);
			$_POST = array_map('htmlspecialchars', $_POST);
            
			if($_POST['name'] != ""){
				$_POST['name'] = $_POST['name'];
				$_POST['value'] = $_POST['value'];
				$_POST['discount'] = $_POST['discount'];
				$_POST['tva'] = $_POST['tva'];
			}else{
				if($_POST['item_id'] == "-"){
					$this->session->set_flashdata('message', 'error:'.$this->lang->line('messages_add_item_error'));
					redirect('estimates/view/'.$_POST['invoice_id']);
				}else{
				$itemvalue = Item::find_by_id($_POST['item_id']);
				$_POST['name'] = $itemvalue->name;
				$_POST['value'] = $itemvalue->value;
				$_POST['tva'] = $_POST['tva'];
				}
			}
			$item = InvoiceHasItem::create($_POST);
       		if(!$item){$this->session->set_flashdata('message', 'error:'.$this->lang->line('messages_add_item_error'));}
       		else{$this->session->set_flashdata('message', 'success:'.$this->lang->line('messages_add_item_success'));}
			redirect('estimates/view/'.$_POST['invoice_id']);
		}else
		{
			$item_units = $this->db->query("SELECT * FROM item_units")->result();
			$this->view_data['item_units'] = $item_units;
			$this->view_data['estimate'] = Invoice::find($id);
			$this->view_data['tva'] = $this->db->query('select * from ref_type_occurences where id_type=9 and visible=1')->result();		
			$this->view_data['defaultTva'] = $settings=setting::find(array('id_vcompanies'=>$_SESSION['current_company']))->tax;  
			$this->view_data['items'] = Item::find('all',array('conditions' => array('inactive=?','0')));
			$this->theme_view = 'modal';
			$this->view_data['title'] = $this->lang->line('application_add_item');
			$type=$this->db->query('SELECT id,libelle FROM items_has_family
			UNION
			SELECT id,libelle FROM items_has_family_parent')->result();
			$this->view_data['type']=$type;
			$this->view_data['form_action'] = 'estimates/itemEmpty';
			$this->content_view = 'estimates/_itemEmpty';
		}	
	}	
	function duplicateItemEmpty($id)
	{	
		$this->db->where('id',$id);
		$invoiceItem=$this->db->get('invoice_has_items')->result()[0];
		$lastId = $this->invoiceHasItem->getLastId() + 1 ; 
		$invoiceItem->id = $lastId; 
		$item = $this->db->insert('invoice_has_items',$invoiceItem);
		if(!$item){$this->session->set_flashdata('message', 'error:'.$this->lang->line('messages_add_item_error'));}
		else{$this->session->set_flashdata('message', 'success:'.$this->lang->line('messages_add_item_success'));}
		redirect('estimates/view/'.$invoiceItem->invoice_id);

	}	
	function item_update($id = FALSE)
	{	
		if($_POST){
			unset($_POST['send']);
			$_POST = array_map('htmlspecialchars', $_POST);
			$item = InvoiceHasItem::find($_POST['id']);
			$item = $item->update_attributes($_POST);
       		if(!$item){$this->session->set_flashdata('message', 'error:'.$this->lang->line('messages_save_item_error'));}
       		else{$this->session->set_flashdata('message', 'success:'.$this->lang->line('messages_save_item_success'));}
			redirect('estimates/view/'.$_POST['invoice_id']);
		}else
		{
			$item_units = $this->db->query("SELECT * FROM item_units")->result();
			$this->view_data['item_units'] = $item_units;
			$this->view_data['estimate_has_items'] = InvoiceHasItem::find($id);
			$this->theme_view = 'modal';
			$this->view_data['title'] = $this->lang->line('application_edit_item');
			$this->view_data['form_action'] = 'estimates/item_update';
			$this->content_view = 'estimates/_item';
		}	
	}
	function item_update_empty($id = FALSE)
	{	
		if($_POST){
			unset($_POST['send']);
			$_POST = array_map('htmlspecialchars', $_POST);
			$item = InvoiceHasItem::find($_POST['id']);
			$item = $item->update_attributes($_POST);
       		if(!$item){$this->session->set_flashdata('message', 'error:'.$this->lang->line('messages_save_item_error'));}
       		else{$this->session->set_flashdata('message', 'success:'.$this->lang->line('messages_save_item_success'));}
			redirect('estimates/view/'.$_POST['invoice_id']);
			
		}else
		{
			$item_units = $this->db->query("SELECT * FROM item_units")->result();
			$this->view_data['item_units'] = $item_units;
			$this->view_data['estimate_has_items'] = InvoiceHasItem::find($id);
			$this->theme_view = 'modal';
			$this->view_data['title'] = $this->lang->line('application_edit_item');
			$this->view_data['form_action'] = 'estimates/item_update_empty';
			$this->view_data['tva']=$this->db->query('select * from ref_type_occurences where id_type=9 and visible=1')->result();
			$this->content_view = 'estimates/_itemEmpty';
		}	
	}     	
	function item_delete($id = FALSE, $estimate_id = FALSE)
	{	
		$item = InvoiceHasItem::find($id);
		$item->delete();
		$this->content_view = 'estimates/view';
		if(!$item){$this->session->set_flashdata('message', 'error:'.$this->lang->line('messages_delete_item_error'));}
       		else{$this->session->set_flashdata('message', 'success:'.$this->lang->line('messages_delete_item_success'));}
			redirect('estimates/view/'.$estimate_id);
	}	
	
	/*
	Facturer un devis
	*/
	function facture($id){
		
		$settings=setting::first();
		$this->db->where('id',$id);
		$devis=$this->db->get('invoices')->result()[0];

		unset($devis->id);
		unset($devis->id_facture);
		$devis->estimate=0;
		$devis->creation_date = date("Y-m-d");
		//revert ref estimate 
		$year = date("Y"); 
		$lastfacture = $this->facture->getLastInvoice();
		$lastRef = explode('-',$lastfacture->creation_date);
		
		if($lastRef[0] != $year){
			$devis->estimate_reference = '1';		
		}else {
			$devis->estimate_reference=$settings->invoice_reference;
		}

		$devis->reference=$settings->invoice_reference+1;
		$this->db->order_by("id","desc");
		$factureid=$this->db->get('facture')->result()[0]->id;
		$factureid=$factureid+1;
		$new_estimate_reference = $settings->invoice_reference;
		
		$estimate_reference = Setting::first();
		$estimate_reference->update_attributes(array('invoice_reference' => $devis->estimate_reference + 1));
		// estimate status 
		  
		$status= $this->config->item("occ_devis_facture"); 
		
		$set=array("id_facture"=>$factureid,
				   "status"=>$status,
				     "estimate_accepted_date"=>date('Y-m-d')
				   );
		$this->db->where('id',$id);
		$this->db->set($set);
		$this->db->update('invoices');
		$devis->id=$factureid;
		//si le num de ref est inf à 10 
		if ($devis->estimate_reference< 10)
		{
			$devis->estimate_reference= '0'.$devis->estimate_reference;
		}
		//-------------------------- Devis name 	
		$estimate_pieces = explode("-",strrev($settings->invoice_prefix));
		$var = date("y-m-d");
		$pieces = explode("-", $var);
		$piecesYear = $pieces[0]; 
		$piecesMounth = $pieces[1];
		$subpiecesYear = explode(' ', $pieces[0]);	
		// année 
		if ($estimate_pieces[0] == "YY")
		{
			$devis->estimate_num = strrev($estimate_pieces[1]).$subpiecesYear[0].
			$devis->estimate_reference;
		}
		//année + mois 
		else
		{	
			$devis->estimate_num = strrev($estimate_pieces[2]).$subpiecesYear[0].$piecesMounth.
			$devis->estimate_reference;	
		}
		unset($devis->due_date);
		//unset($devis->project_ref);
		//$devis->issue_date = date("Y-m-d");
		// facture status 
		$issue_date = date("d-m-y", strtotime($devis->issue_date));

		$refP=($this->estimate_model->getByRef(intval($devis->project_id))->refp);
		//var_dump($refP);exit;


		$idprojet=$this->db->query('select project_id from facture ORDER BY id desc')->result()[0]->project_id;

	$totalg=((float)($this->project->calculquantitelg($idprojet)->quantitelg));
	$data['totalg']=$totalg;
	//	var_dump($totalg);exit;

		$devis->subject= $devis->project_name;
		$devis->status= $this->config->item("occ_facture_ouvert");
		$devis->timbre_fiscal = $settings->timbre_fiscal; 
		$devis->notes ='&nbsp;Attachement (Réf: '. $devis->project_ref .')<div>ARIANA , LE '.$issue_date .'</div><div><br></div><div><div>La somme des heures passées sur ce dossier est '.$devis->calcul_heure .' Heures</div><div>La somme des quantités sur ce dossier est '. ($devis->project_surface == 0 ? $totalg : $devis->project_surface).' m²</div><div><br> Réf QUARTA: '. $refP.' 
		</div>'; 


		$factured=$this->db->insert('facture',$devis);
		$idfacture=$this->db->query('select id from facture ORDER BY id desc')->result()[0]->id;
		$this->db->where('invoice_id',$id);
		$items=$this->db->get('invoice_has_items')->result();
		
		$idprojet=$this->db->query('select project_id from facture ORDER BY id desc')->result()[0]->project_id;
		
		$this->db->where('project_id',	$idprojet);
		$this->db->select('subject, surface, longueur');
		$query = $this->db->get('tickets');
		$subject = $query->result_array();
		//var_dump($subject[0]);exit;
		$item ='';
		$sub = array();
		foreach ($subject as $sub ) // array(1) { ["subject"]=> string(20) "DAO Plan de niveaux " } =>> $sub["subject"]
		{
			$item->facture_id=$idfacture; 
			$item->name=$sub["subject"];
			$item->	unit='m²';
			if ($sub["surface"]==0){
			$item->	amount=$sub["longueur"];} else {
				$item->	amount=$sub["surface"];
			}

			if (stripos($sub["subject"], "niveaux") !== false || stripos($sub["subject"], "interieur") !== false || stripos($sub["subject"], "masse") !== false || stripos($sub["subject"], "héberge") !== false) {
				switch (true) {
					case ($item->amount >= 0 && $item->amount <= 500):
						$item->value = '1';
						break;
					case ($item->amount >= 501 && $item->amount <= 1000):
						$item->value = '0.9';
						break;
					case ($item->amount >= 1001 && $item->amount <= 1500):
						$item->value = '0.75';
						break;
					case ($item->amount >= 1501 && $item->amount <= 2500):
							$item->value = '0.65';
							break;
					case ($item->amount >= 2500 && $item->amount <= 5000):
								$item->value = '0.5';
								break;
                    case ($item->amount >= 5000 && $item->amount <= 10000):
									$item->value = '0.42';
									break;
					case ($item->amount >= 10000 && $item->amount <= 50000):
										$item->value = '0.35';
										break;
					default:
						// Cas par défaut si aucune condition n'est satisfaite
						break;
				}
			}

			elseif (stripos($sub["subject"], "façades") !== false || stripos($sub["subject"], "coupes") !== false) {
				switch (true) {
					case ($item->amount >= 0 && $item->amount <= 500):
						$item->value = '1';
						break;
					case ($item->amount >= 501 && $item->amount <= 1000):
						$item->value = '0.8';
						break;
					case ($item->amount >= 1001 && $item->amount <= 1500):
						$item->value = '0.7';
						break;
					case ($item->amount >= 1501 && $item->amount <= 2500):
							$item->value = '0.6';
							break;
					case ($item->amount >= 2500 && $item->amount <= 5000):
								$item->value = '0.42';
								break;
                    case ($item->amount >= 5000 ):
									$item->value = '0.35';
									break;
				
					default:
						// Cas par défaut si aucune condition n'est satisfaite
						break;
				}
			}


			elseif (stripos($sub["subject"], "toitu") !== false) {
				switch (true) {
					case ($item->amount >= 0 && $item->amount <= 500):
						$item->value = '0.3';
						break;
					case ($item->amount >= 501 && $item->amount <= 1000):
						$item->value = '0.28';
						break;
					case ($item->amount >= 1001 && $item->amount <= 1500):
						$item->value = '0.25';
						break;
					case ($item->amount >= 1501 && $item->amount <= 2500):
							$item->value = '0.2';
							break;
					case ($item->amount >= 2500 && $item->amount <= 5000):
								$item->value = '0.15';
								break;
                    case ($item->amount >= 5000 ):
									$item->value = '0.1';
									break;
				
					default:
						// Cas par défaut si aucune condition n'est satisfaite
						break;
				}
			}
			elseif (stripos($sub["subject"], "maquette") !== false) {
				switch (true) {
					case ($item->amount >= 0 && $item->amount <= 100):
						$item->value = '3';
						break;
					case ($item->amount >= 101 && $item->amount <= 200):
						$item->value = '2';
						break;
					case ($item->amount >= 201 && $item->amount <= 400):
						$item->value = '1.8';
						break;
					case ($item->amount >= 401 && $item->amount <= 1000):
							$item->value = '1.5';
							break;
					case ($item->amount >= 1001 && $item->amount <= 1500):
								$item->value = '1.2';
								break;
					case ($item->amount >= 1501 && $item->amount <= 2500):
									$item->value = '1';
									break;
					case ($item->amount >= 2501 && $item->amount <= 10000):
										$item->value = '0.75';
										break;
				
					default:
						// Cas par défaut si aucune condition n'est satisfaite
						break;
				}
			}
			else $item->value='';
			//var_dump($sub["subject"]);exit;
			$this->db->insert('facture_has_items',$item); 
		}
		
		




		foreach ($items as $item ) 
		{
			$idprojet=$this->db->query('select project_id from facture ORDER BY id desc')->result()[0]->project_id;
			unset($item->id);
			unset($item->invoice_id);
			$this->db->insert('facture_has_items',$item); }
		
		
		if(!$factured)
		{
			$this->session->set_flashdata('message', 'error:'.$this->lang->line('messages_delete_factured_error'));
		}
	    else
		{
			$this->session->set_flashdata('message', 'success:'.$this->lang->line('messages_delete_factured_success'));
		}		
		redirect('invoices/view/'.$idfacture);
	}
	
	/*
	Dupliquer un devis
	*/
	function duplicate($id)
	{
		$this->db->where('id',$id);
		$devis=$this->db->get('invoices')->result()[0];
		$option=array("id_vcompanies"=>$_SESSION['current_company']);
		$settings = Setting::find($option);
		$devis->reference=$settings->estimate_reference;
		$devis->creation_date = date("Y-m-d");
		//revert ref estimate 
		$year = date("Y"); 
		$lastestimate = Invoice::last();
		$lastRef = explode('-',(date_format($lastestimate->creation_date,'Y-m-d'))); 
		if($lastRef[0] != $year){
			$devis->estimate_reference = '1';		
		}else {
			$devis->estimate_reference = $settings->estimate_reference;
		} 
		$devis->issue_date=date('Y-m-d');
		$date=$devis->issue_date;
		$echeance=date('Y-m-d', strtotime($date . " +".$settings->echeance."days"));
		$devis->due_date=$echeance;
		//get referentiels of  devis 
		
		$devis->status= $this->config->item("occ_devis_ouvert"); 
		$this->db->select_max('invoices.id'); 
        $this->db->from('invoices');
        $deviseid = $this->db->get()->result()[0]->id;
		$devis->id=$deviseid +1;
		//si le num de ref est inf à 10 
		$new_estimate_reference = $devis->estimate_reference;
		if ($new_estimate_reference< 10)
		{
			$new_estimate_reference= '0'.$new_estimate_reference;
		}
		//-------------------------- Devis name 	
		$estimate_pieces = explode("-",strrev($settings->estimate_prefix));
		$var = date("y-m-d",strtotime($date));
		$pieces = explode("-", $var);
		$piecesYear = $pieces[0]; 
		$piecesMounth = $pieces[1];
		$subpiecesYear = explode(' ', $pieces[0]);	
		// année 
		if ($estimate_pieces[0] == "YY")
		{
			$devis->estimate_num = strrev($estimate_pieces[1]).$subpiecesYear[0].
			$new_estimate_reference;
		}
		//année + mois 
		else{	
			$devis->estimate_num = strrev($estimate_pieces[2]).$subpiecesYear[0].$piecesMounth.$new_estimate_reference;	
		} 
		$devis->creation_date = date("Y-m-d");
		$this->db->insert('invoices',$devis);
		$settings->update_attributes(array('estimate_reference' =>$new_estimate_reference +1 ));
		$this->db->where('invoice_id',$id);
		$items=$this->db->get('invoice_has_items')->result();
       	$devisid=Invoice::last();
		foreach($items as $item){
		unset($item->id);
		$item->invoice_id=$devisid->id;
		$this->db->insert('invoice_has_items',$item);
	    }
	redirect('estimates');
	}
	public function renderItem($name)
	{
        $item = $this->item->getById(urldecode($name));
        $output = array(
			"value" => $item->value,
			"tva" => $item->tva,
			"unit"   => $item->unit,
		);
		header('Content-Type: application/json');
		echo json_encode($output);
		exit();
	}
	function sendfiles($id)
	{
		if($_POST)
		{
			$id = $_POST['id'];
			$this->view_data['form_action'] = 'settings/sendfiles';
			$this->view_data['data'] = Invoice::find($id);  
			$this->view_data['type'] = "devis"; 
			//Save file  
			$this->load->database();		
			$this->load->helper('file'); 
			require_once('dompdf/dompdf_config.inc.php');
			$pdfroot  = dirname(dirname(__FILE__));
			$pdfroot .= '/third_party/pdf/devis.pdf';
			$dompdf = new Dompdf();
			$this->load->helper(array('dompdf', 'file')); 
			$data["estimate"] = Invoice::find($id); 
			//get name status of devis 
			
			$this->db->where('invoice_id',$id);
			$this->db->order_by('position','asc');
			$data['items'] = $this->db->get('invoice_has_items')->result();
			$countDiscount = $this->db->query("SELECT discount FROM invoice_has_items WHERE invoice_id = '".$id."' AND discount > 0")->result();
					$data['countDiscount'] = count($countDiscount);
			$option=array("id_vcompanies"=>$_SESSION['current_company']);	
			$this->db->where('id',$_SESSION['current_company']);
			$data['vcompanies']=$this->db->get('v_companies')->result();
			$data["core_settings"] = Setting::find($option);
			$data['num_project']= $this->project->getProjectById($data["estimate"]->project_id)->id;	
			$this->db->where('id',$_SESSION['current_company']);
			$logo=$this->db->get('v_companies')->result()[0]->picture;
			$data['logo']=$logo;
			$this->db->where('id',$data['estimate']->company_id);
			$company=$this->db->get('companies')->result()[0];
			$data['company']=$company;
			$this->db->where('id',$data['company']->client_id);
			$due_date = date($data["core_settings"]->date_format, human_to_unix($data["estimate"]->due_date.' 00:00:00'));  
			//chiffre of Devise
			$ref=$this->refType->getRefTypeByName($data["estimate"]->currency)->id; 
			
			$chiffre=$this->referentiels->getReferentielsByIdType($this->refType->getRefTypeByName($data["estimate"]->currency)->id)->name; 
			$data['chiffre']=$chiffre;
			$parse_data = array(
								'due_date' => $due_date,
								'estimate_id' => $data["core_settings"]->estimate_prefix.$data["estimate"]->estimate_reference,
								'client_link' => $data["core_settings"]->domain,
								'company' => $data["core_settings"]->company,
								);
			$html = $this->load->view($data["core_settings"]->template. '/' .$data["core_settings"]->estimate_pdf_template.$data["core_settings"]->default_template, $data, true); 
			$dompdf->load_html($html);
			$paper_orientation = 'Potrait'; 
			$dompdf->set_paper($paper_orientation);
			$dompdf->render();
			$pdf_string =   $dompdf->output();
			file_put_contents($pdfroot, $pdf_string);
			//Send file
			sendMail($id,$_POST['smtp_user'],$pdfroot,$_POST['dist'],$_POST['cc'],$_POST['notes'],'estimates/sendfiles','estimates/') ; 
		} else {
			$this->view_data['form_action'] = 'estimates/sendfiles';
			$this->view_data['data'] = Invoice::find($id);  
			$this->view_data['type'] = "devis";  
			$this->content_view = 'settings/sendFile';
		}
	}
}
