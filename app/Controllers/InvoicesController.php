<?php 

namespace App\Controllers;

use App\Controllers\BaseController;

class InvoicesController extends BaseController
{

	var $idTypeEtatFacture;
	var $idFactureOuvert;
	var $idTypeEtatAvoir;
	var $idAvoirOuvert;
	var $idMoyensPaiement;

	function __construct()
	{
		parent::__construct();
		if ($this->client) {
		} elseif ($this->user) {
		} else {
			redirect('login');
		}
		$this->load->model('projects_model', 'project');
		$this->load->model('Facture_model', 'invoice');
		$this->load->model('Ref_type_occurences_model', 'referentiels');
		$this->load->model('RefType_model', 'refType');
		$this->load->model('item_model', 'item');
		$this->load->model('Itemfamily_model', 'itemFamily');
		$this->load->model('factureHasItem_model', 'factureHasItem');
		$this->load->model('avoir_model', 'avoir');

		$this->idTypeEtatFacture = $this->config->item("type_id_etat_facture");
		$this->idTypeEtatAvoir = $this->config->item("type_id_etat_avoir");
		$this->idFactureOuvert = $this->config->item("occ_facture_ouvert");
		$this->idAvoirOuvert = $this->config->item("occ_avoir_ouvert");
		$this->idMoyensPaiement = $this->config->item("type_id_moyens_paiement");


		$submenus = $this->referentiels->getReferentielsByIdType($this->idTypeEtatFacture);
		$this->view_data['submenu'][$this->lang->line('application_all')] = 'invoices';
		foreach ($submenus as $submenu) {
			if ($submenu->name != "Sent" && $submenu->name != "Pending") {
				if ($this->lang->line('application_' . $submenu->name) == false) {
					$this->view_data['submenu'][$submenu->name] = 'invoices/filter' . $submenu->name;
				} else {
					$this->view_data['submenu'][$this->lang->line('application_' . $submenu->name)] = 'invoices/filter' . $submenu->name;
				}
			}
		}
	}

	function index()
	{
		$this->load->helper('url');
		$this->load->database();

		$this->db->where_in('company_id', $comp_array);
		$this->db->order_by("id", "desc");
		$facture = $this->db->get('facture')->result();

		$this->view_data['settings'] = Setting::first();

		// chiffre devise
		foreach ($facture as $fact) {
			$refTypeCurrency = $this->refType->getRefTypeByName($fact->currency)->id;
			//if(! is_null($refTypeCurrency)) { var_dump($refTypeCurrency);}
			$fact->chiffre = $this->referentiels->getReferentielsByIdType($refTypeCurrency)[0]->name;
			//var_dump($fact->chiffre);exit;
		}


		$this->view_data['invoices'] = $facture;

		$days_in_this_month = days_in_month(date('m'), date('Y'));
		$lastday_in_month = date('Y-m-' . $days_in_this_month);
		$firstday_in_month = date('Y-m-1');

		$now = time();
		$beginning_of_week = strtotime('last Monday', $now); // BEGINNING of the week
		$end_of_week = strtotime('next Sunday', $now) + 86400; // END of the last day of the week

		$this->view_data['invoices_paid_this_month_graph'] = Invoice::find_by_sql("SELECT
		COUNT(id) AS 'amount',
		DATE_FORMAT(`paid_date`, '%w') AS 'date_day',
		DATE_FORMAT(`paid_date`, '%Y-%m-%d') AS 'date_formatted'
		FROM
			facture
		WHERE
			UNIX_TIMESTAMP(`paid_date`) >= '$beginning_of_week'
				AND UNIX_TIMESTAMP(`paid_date`) <= '$end_of_week'
		GROUP BY paid_date");

		$this->content_view = 'invoices/all';
	}

	function calc()
	{
		$invoices = Invoice::find('all', array('conditions' => array('estimate != ?', 1)));
		foreach ($invoices as $invoice) {
			$option = array("id_vcompanies" => $_SESSION['current_company']);
			$settings = Setting::find($option);
			$items = InvoiceHasItem::find('all', array('conditions' => array('invoice_id=?', $invoice->id)));
			//calculate sum
			$i = 0;
			$sum = 0;
			foreach ($items as $value) {
				$sum = ($sum + $invoice->invoice_has_items[$i]->amount) * ($invoice->invoice_has_items[$i]->value);
				$i++;
			}
			if (substr($invoice->discount, -1) == "%") {
				$discount = sprintf("%01.2f", round(($sum / 100) * substr($invoice->discount, 0, -1), 2));
			} else {
				$discount = $invoice->discount;
			}
			$sum = $sum - $discount;

			if ($invoice->tax != "") {
				$tax_value = $invoice->tax;
			} else {
				$tax_value = $settings->tax;
			}
			if ($invoice->second_tax != "") {
				$second_tax_value = $invoice->second_tax;
			} else {
				$second_tax_value = $core_settings->second_tax;
			}
			$tax = sprintf("%01.2f", round(($sum / 100) * $tax_value, 2));
			$second_tax = sprintf("%01.2f", round(($sum / 100) * $second_tax_value, 2));
			$sum = sprintf("%01.2f", round($sum + $tax + $second_tax, 2));
			$invoice->sum = $sum;
			$invoice->save();
		}
		redirect('invoices');
	}

	//condition of filter
	function filter($condition = FALSE, $year = FALSE)
	{
		if ($condition != "False") {
			$condition = urldecode($condition);
			//get the id of status
			$idState = $this->referentiels->getReferentiels($this->idTypeEtatFacture, $condition)->id;
			$this->db->where("status =" . $idState);
			$Factures = $this->db->get('facture')->result();
		} else if (isset($year)) {
			$Factures = $this->invoice->getAll();
			foreach ($Factures as $key => $Facture) {
				$date = DateTime::createFromFormat("Y-m-d", $Facture->issue_date);
				$date = $date->format("Y");
				if ($date != $year) {
					unset($Factures[$key]);
				}
			}
		}
		foreach ($Factures as $Facture) {
			$refTypeCurrency = $this->refType->getRefTypeByName($Facture->currency)->id;
			$Facture->chiffre = $this->referentiels->getReferentielsByIdType($refTypeCurrency)->name;
			$Facture->status = $this->referentiels->getReferentielsById($Facture->status)->name;
		}
		$this->view_data['invoices'] = $Factures;
		$opt = array("id_vcompanies" => $_SESSION['current_company']);
		$this->view_data['settings'] = Setting::find($opt);
		$this->content_view = 'invoices/all';
	}

	//Créer une nouvelle facture
	function create()
	{
		$this->load->database();

		if ($_POST) {
			$invoice_reference = Setting::first();
			$refType = $this->refType->getRefTypeByName($_POST['currency'])->id;
			$chiffre = $this->referentiels->getReferentielsByIdType($refType)->name;
			//ajouter client passager
			if ($_POST['company'] == 1) {
				if (!isset($_POST['timbre_fiscal'])) {
					$_POST['timbre_fiscal'] = 0;
				}
				$company = Company::last();
				$ref = $company->reference;
				$data = array(
					'name' => $_POST['nomClient'],
					'reference' => $ref + 1,
					'passager' => '1',
					'tva' => $_POST['tva'],
					'guarantee' => $_POST['guarantee'],
					'timbre_fiscal' => $_POST['timbre_fiscal']
				);
				$this->db->insert('companies', $data);
				$_POST['company_id'] = Company::last()->id;
				unset($_POST['company']);
				//company ref
				$new_company_reference = $invoice_reference->company_reference + 1;
				$invoice_reference->update_attributes(array(
					'company_reference' => $new_company_reference
				));
			}

			//Project ref in facture
			if ($_POST['project_id'] != 0) {
				$proj = $this->project->getProjectByRef($_POST['project_id']);
				$dataProject['project_id'] = $proj->id;
			}
			if ($_POST['name'] != '') {
				$lastProject = Project::last();
				$_POST['project_id'] = $lastProject->reference + 1;
				//-------------------------- Project name
				$project_pieces = explode("-", strrev($invoice_reference->project_prefix));
				$var = date("Y-m-d", strtotime($_POST['start']));
				$pieces = explode("-", $var);
				$piecesYear = $pieces[0];
				$piecesMounth = $pieces[1];
				$subpiecesYear = explode('0', $pieces[0]);
				$ref = $lastProject->reference + 1;
				if ($ref != "") {
					if ($ref < 10) {
						$_POST['reference'] = '0' . $ref;
					}
					if ($project_pieces[0] == 'YY') {
						$numero = strrev($project_pieces[1]) . $subpiecesYear[1] . $_POST['reference'];
					} else {
						$numero = strrev($project_pieces[2]) . $subpiecesYear[1] . $piecesMounth . $_POST['reference'];
					}
					unset($_POST['reference']);

				}
				$d = array(
					'name' => $_POST['name'],
					'reference' => $ref,
					'datetime' => time(),
					'progress' => 0,
					'start' => $_POST['start'],
					'end' => $_POST['end'],
					'project_num' => $numero,
					'company_id' => $_POST['company_id'],
					'creation_date' => date("Y-m-d")
				);
				$this->db->insert('projects', $d);
				$invoice_reference->update_attributes(array('project_reference' => $invoice_reference->project_reference + 1));
				$dataProject['project_id'] = Project::last()->id;
			}
			unset($_POST['name']);
			unset($_POST['start']);
			unset($_POST['end']);
			unset($_POST['nomClient']);
			unset($_POST['tva']);
			unset($_POST['guarantee']);
			unset($_POST['timbre_fiscal']);
			unset($_POST['send']);
			unset($_POST['_wysihtml5_mode']);
			unset($_POST['files']);

			$_POST['creation_date'] = date("Y-m-d");
			$_POST['status'] = $this->config->item("occ_facture_ouvert");
			//var_dump($_POST);exit;
			//revert ref invoice
			$year = date("Y");
			$lastfacture = $this->invoice->getLastInvoice();
			$lastRef = explode('-', $lastfacture->creation_date);
			if ($lastRef[0] != $year) {
				$_POST['reference'] = 1;
			} else {
				$_POST['reference'] = $invoice_reference->invoice_reference;
			}
			$_POST['estimate_reference'] = $_POST['reference'];
			if ($_POST['reference'] < 10) {
				$_POST['estimate_reference'] = '0' . $_POST['estimate_reference'];
			}
			$_POST['estimate_num'] = $invoice_reference->invoice_prefix . $_POST['reference'];
			//-------------------------- Facture name
			$estimate_pieces = explode("-", strrev($invoice_reference->invoice_prefix));
			$var = date("y-m-d", strtotime($_POST['issue_date']));
			$pieces = explode("-", $var);
			$piecesYear = $pieces[0];
			$piecesMounth = $pieces[1];
			$subpiecesYear = explode(' ', $pieces[0]);
			// année
			if ($estimate_pieces[0] == "YY") {
				$_POST['estimate_num'] = strrev($estimate_pieces[1]) . $subpiecesYear[0] .
					$_POST['estimate_reference'];
			}
			//année + mois
			else {
				$_POST['estimate_num'] = strrev($estimate_pieces[2]) . $subpiecesYear[0] . $piecesMounth .
					$_POST['estimate_reference'];
			}
			$_POST['timbre_fiscal'] = $invoice_reference->timbre_fiscal;

			$new_invoice_reference = $_POST['reference'] + 1;
			$invoice_reference->update_attributes(array('invoice_reference' => $new_invoice_reference));

			$invoice = $this->db->insert('facture', $_POST);
			if ($dataProject['project_id'] != 0) {
				$dataProject['invoice_id'] = $this->invoice->getLastId();
				$this->db->insert('project_has_invoices', $dataProject);
			}
			if (!$invoice) {
				$this->session->set_flashdata('message', 'error:' . $this->lang->line('messages_create_invoice_error'));
			} else {
				$this->session->set_flashdata('message', 'success:' . $this->lang->line('messages_create_invoice_success'));
			}
			redirect('invoices');
		} else {

			$facture = $this->db->get('facture')->result();
			$this->view_data['invoices'] = Invoice::all();
			$this->view_data['next_reference'] = Invoice::last();
			$this->view_data['companies'] = Company::find('all', array('conditions' => array('inactive=?', '0')));

			$this->theme_view = 'modal';
			$this->view_data['title'] = $this->lang->line('application_create_invoice');
			$option = array("id_vcompanies" => $_SESSION['current_company']);
			$settings = setting::find($option);

			$this->view_data['current_date'] = date('Y-m-d');
			$current_date = date('Y-m-d');
			$current_date = explode('-', $current_date);
			if ($current_date[1] == "01" && $current_date[2] == "01") {
				$settings->update_attributes(array('invoice_reference' => "1"));
			}
			$this->view_data['current_date'] = date('Y-m-d');
			$date = $this->view_data['current_date'];
			$echeance = date('Y-m-d', strtotime($date . " +" . $settings->echeance . "days"));
			$_POST['creation_date'] = date("Y-m-d");
			$this->view_data['current_echeance'] = $echeance;
			//revert ref invoice
			$year = date("Y");
			$lastfacture = $this->invoice->getLastInvoice();
			$lastRef = explode('-', $lastfacture->creation_date);
			if ($lastRef[0] != $year) {
				$settings->invoice_reference = 1;
			}
			$this->view_data['core_settings'] = $settings;
			$idType = $this->refType->getRefTypeByName("Devise")->id;
			$this->view_data['currencys'] = $this->referentiels->getReferentielsByIdType($idType);
			$this->view_data['form_action'] = 'invoices/create';
			$this->content_view = 'invoices/_invoice';
		}
	}

	// Mettre à jour une facture
	function update($id = FALSE, $getview = FALSE)
	{
		$this->load->database();
		if ($_POST) {

			$id = $_POST['id'];
			$this->db->where('id', $id);
			$facture = $this->db->get('facture')->result()[0];
			$company = Company::find($facture->company_id);
			$invoice_reference = Setting::first();
			//référentiel devise
			$refType = $this->refType->getRefTypeByName($_POST['currency'])->id;
			$chiffre = $this->referentiels->getReferentielsByIdType($refType)->name;

			//ajouter client passager
			if ($_POST['company'] == 1) {
				if ($company->passager != 1) {
					$company = Company::last();
					$ref = $company->reference;
					$data = array(
						'name' => $_POST['nomClient'],
						'id_vcompanies' => $_SESSION['current_company'],
						'reference' => $ref + 1,
						'passager' => '1',
						'tva' => $_POST['tva'],
						'guarantee' => $_POST['guarantee'],
						'timbre_fiscal' => $_POST['timbre_fiscal']
					);
					$this->db->insert('companies', $data);
					$_POST['company_id'] = Company::last()->id;
					//company ref
					$new_company_reference = $invoice_reference->company_reference + 1;
					$invoice_reference->update_attributes(array(
						'company_reference' => $new_company_reference
					));
				} else {
					$data = array(
						'name' => $_POST['nomClient'],
						'tva' => $_POST['tva'],
						'guarantee' => $_POST['guarantee'],
						'timbre_fiscal' => $_POST['timbre_fiscal']

					);
					$company->update_attributes($data);
				}
				unset($_POST['company']);
			}
			//Project ref in facture by ref
			if ($_POST['project_id'] != 0) {
				//$_POST['project_ref'] = $this->project->getProjectRef($_POST['project_id'])->project_num;
				$proj = $this->project->getProjectByRef($_POST['project_id']);
				$data2['project_id'] = $proj->id;

			}
			if ($_POST['name'] != '') {
				$lastProject = Project::last();
				$_POST['project_id'] = $lastProject->reference + 1;

				//-------------------------- Project name
				$project_pieces = explode("-", strrev($invoice_reference->project_prefix));
				$var = date("Y-m-d", strtotime($_POST['start']));
				$pieces = explode("-", $var);
				$piecesYear = $pieces[0];
				$piecesMounth = $pieces[1];
				$subpiecesYear = explode('0', $pieces[0]);
				$ref = $lastProject->reference + 1;
				if ($ref != "") {
					if ($ref < 10) {
						$ref = '0' . $ref;
					}
					if ($project_pieces[0] == 'YY') {
						$numero = strrev($project_pieces[1]) . $subpiecesYear[1] . $ref;
					} else {
						$numero = strrev($project_pieces[2]) . $subpiecesYear[1] . $piecesMounth . $ref;
					}
					unset($_POST['reference']);

				}
				$d = array(
					'name' => $_POST['name'],
					'id_vcompanies' => $_SESSION['current_company'],
					'reference' => $ref,
					'datetime' => time(),
					'progress' => 0,
					'start' => $_POST['start'],
					'end' => $_POST['end'],
					'project_num' => $numero,
					'company_id' => $_POST['company_id']
				);
				$this->db->insert('projects', $d);
				$invoice_reference->update_attributes(array('project_reference' => $invoice_reference->project_reference + 1));
				$data2['project_id'] = Project::last()->id;
			}
			//update
			$data2['invoice_id'] = $id;
			$idProjectI = ProjectHasInvoice::find('all', array('conditions' => array('invoice_id=?', $id)))[0];
			if (isset($idProjectI)) {
				$this->db->where('id', $idProjectI->id);
				$this->db->set($data2);
				$this->db->update('project_has_invoices');
			} else {
				$this->db->insert('project_has_invoices', $data2);
			}
			unset($_POST['name']);
			unset($_POST['start']);
			unset($_POST['end']);
			unset($_POST['nomClient']);
			unset($_POST['tva']);
			unset($_POST['guarantee']);
			unset($_POST['timbre_fiscal']);
			unset($_POST['send']);
			unset($_POST['_wysihtml5_mode']);
			unset($_POST['files']);
			unset($_POST['reference']);
			$view = FALSE;
			if (isset($_POST['view'])) {
				$view = $_POST['view'];
			}
			unset($_POST['view']);
			//get status by id

			$invoice = $facture;

			//get referentiels of  facture
			$typeId = $this->refType->getRefTypeByName('Facture')->id;

			$this->db->where('id', $id);
			$this->db->set($_POST);
			$invoice = $this->db->update('facture');
			if (!$invoice) {
				$this->session->set_flashdata('message', 'error:' . $this->lang->line('messages_save_invoice_error'));
			} else {
				$this->session->set_flashdata('message', 'success:' . $this->lang->line('messages_save_invoice_success'));
			}
			if ($view == 'true') {
				redirect('invoices/view/' . $id);
			} else {
				redirect('invoices');
			}

		} else {
			$this->db->where('id', $id);
			$facture = $this->db->get('facture')->result()[0];
			$project = $this->project->getProjectById($facture->project_id);

			$this->view_data['invoice'] = $facture;
			$this->view_data['proj'] = $project;
			$idType = $this->refType->getRefTypeByName("Devise")->id;
			$this->view_data['currencys'] = $this->referentiels->getReferentielsByIdType($idType);
			$idType = $this->refType->getRefTypeByName("Facture")->id;

			$this->view_data['state'] = $this->referentiels->getReferentielsByIdType($idType);

			$this->view_data['companies'] = Company::find('all', array('conditions' => array('inactive=?', '0')));
			if ($getview == "view") {
				$this->view_data['view'] = "true";
			}
			$this->theme_view = 'modal';
			$this->view_data['title'] = $this->lang->line('application_edit_invoice');
			$this->view_data['form_action'] = 'invoices/update';
			$this->view_data['company'] = Company::find($this->view_data['invoice']->company_id);
			$this->view_data['projectId'] = $this->project->getProjectByid($this->view_data['invoice']->project_id)->id;
			$this->content_view = 'invoices/_invoice';
		}
	}

	// Afficher le détail d'une facture
	function view($id = FALSE)
	{
		$this->load->database();

		$this->db->where('id', $id);
		$this->view_data['invoice'] = $this->db->get('facture')->result()[0];
		if ($this->view_data['invoice']->project_id != 0) {
			$option = array("reference" => $this->view_data['invoice']->project_id);
			$this->view_data['project'] = Project::find($option);
		}
		//chiffre devise
		$refType = $this->refType->getRefTypeByName($this->view_data['invoice']->currency)->id;
		$this->view_data['chiffre'] = $this->referentiels->getReferentielsByIdType($refType)[0]->name;
		//get referentiels of  facture
		/*$this->view_data['invoice']->sent_status = $this->referentiels->getReferentielsById($this->view_data['invoice']->sent_status)->name;
			  $this->view_data['invoice']->status = $this->referentiels->getReferentielsById($this->view_data['invoice']->status)->name;*/
		$this->db->where('id', $this->view_data['invoice']->company_id);
		$this->view_data['company'] = $this->db->get('companies')->result()[0];
		$this->db->where('id', $this->view_data['company']->client_id);
		$this->view_data['client'] = $this->db->get('clients')->result()[0];

		$data["core_settings"] = Setting::first();
		$invoice = $this->view_data['invoice'];
		$this->view_data['items'] = $this->db->query("SELECT * FROM facture_has_items WHERE facture_id = $id ORDER BY position")->result();
		//calculate sum
		$i = 0;
		$sum = 0;
		foreach ($this->view_data['items'] as $value) {
			$this->db->where('id', $value->id);
			$this->db->select('amount,value');
			$amount = $this->db->get('facture_has_items')->result()[0];
			$SousTotal = ($value->amount * $value->value) - ($value->amount * $value->value * $value->discount) / 100;
			$SousTotalTVA += $SousTotal + ($SousTotal * $value->tva) / 100;
			$total += $SousTotal;
		}
		$idInvoice = $this->view_data['invoice']->company_id;
		$companiesTimbre = Company::find_by_id($idInvoice);
		//exonéré du TVA
		if ($companiesTimbre->tva == 1) {
			$sum = $total;
		} else {
			$sum = $SousTotalTVA;
		}
		//total hors tax
		$sumht = $total;
		// discount in facture
		$sum = $sum - ($sum / 100) * $this->view_data['invoice']->discount;
		$sumht = $sumht - ($sumht / 100) * $this->view_data['invoice']->discount;
		//Calcul retenue guarantee
		if ($companiesTimbre->guarantee == 1) {
			$sum = $sum - ($sum * 10) / 100;
		}
		//Retenue
		if ($this->view_data['invoice']->deduction > 0) {
			$this->view_data['deductionht'] = ($sumht / 100) * $this->view_data['invoice']->deduction;
			$this->view_data['deduction'] = ($sum / 100) * $this->view_data['invoice']->deduction;
		}
		//add timbre fisacle to facture
		if ($companiesTimbre->timbre_fiscal < 1) {
			$sum = $sum + $invoice->timbre_fiscal;
		}
		//payment of facture
		$this->db->where('facture_id', $id);
		$payments = $this->db->get('facture_has_payments')->result();
		if (isset($payments)) {
			foreach ($payments as $value) {
				$payment = $payment + $value->amount;
			}
			// reste du
			$outstanding = $sum - $payment;
		}
		$data = array(
			"sum" => $sum,
			"paid" => $payment,
			"outstanding" => $outstanding,
			"timbre_fiscal" => $invoice->timbre_fiscal,
			"sumht" => $sumht
		);
		$this->db->where('id', $id);
		$this->db->set($data);
		$this->db->update('facture');
		foreach ($payments as $payment) {
			$payment->type = $this->referentiels->getReferentielsById($payment->type)->name;
		}
		$this->view_data['payments'] = $payments;
		$this->view_data['project'] = $this->project->getProjectById($this->view_data['invoice']->project_id);

		$this->content_view = 'invoices/view';
	}


	function banktransfer($id = FALSE, $sum = FALSE)
	{
		$this->db->where('id', $id);
		$facture = $this->db->get('facture')->result()[0];
		$this->theme_view = 'modal';
		$this->view_data['title'] = $this->lang->line('application_bank_transfer');
		$option = array("id_vcompanies" => $_SESSION['current_company']);
		$data["core_settings"] = Setting::find($option);
		$this->view_data['invoice'] = $facture;
		$this->content_view = 'invoices/_banktransfer';
	}

	//mettre à jour l'avancement du projet par rapport à l'état des paiements des factures
	function update_progress_projet($id_projet)
	{
		$project_id = $id_projet;
		$payee = 0;
		if ($project_id != 0) {
			$factures = $this->invoice->getByIdProject($project_id);
			foreach ($factures as $key => $value) {
				if ($value->status == $this->config->item("occ_facture_paye")) {
					$payee++;
				}
			}
			//mettre à jour la progression du projet
			$progress = ($payee / count($factures)) * 100;
			$this->project->UpdateProgress($project_id, $progress);
		}
	}


	// Ajouter un paiement à une facture
	function payment($id = FALSE)
	{
		$this->load->database();
		$compteBancaire = $this->db->get('comptes_bancaires')->result();

		if ($_POST) {
			$virg = strrpos($_POST['amount'], ',');
			if ($virg != null) {
				$amount = explode(",", $_POST['amount']);
				$_POST['amount'] = $amount[0] . '.' . $amount[1];
			}

			unset($_POST['send']);

			$_POST['user_id'] = $this->user->id;
			$this->db->where('id', $_POST['facture_id']);
			$invoice = $this->db->get('facture')->result()[0];

			if ($_POST['type'] != "26") {
				unset($_POST['id_compteBancaire']);
			}

			$invoiceHasPayment = $this->db->insert('facture_has_payments', $_POST);
			$factureoutstanding = $invoice->outstanding;

			if (($factureoutstanding - $_POST['amount']) == 0) {
				$new_status = $this->config->item("occ_facture_paye");
				$this->db->where('id', $_POST['facture_id']);
				$this->db->set('paid_date', $_POST['date']);
				$this->db->update('facture');
			} else {
				$new_status = $this->config->item("occ_facture_p_paye");
			}
			//get the id of status

			$this->db->where('id', $_POST['facture_id']);
			$this->db->set('status', $new_status);
			$this->db->update('facture');
			$this->update_progress_projet($invoice->project_id);
			redirect('invoices/view/' . $_POST['facture_id']);

		} else {
			$this->db->where('id', $id);
			$facture = $this->db->get('facture')->result()[0];

			$this->view_data['compteBancaires'] = $this->db->get('comptes_bancaires')->result();
			$this->view_data['invoice'] = $facture;
			$opt = array("id" => $this->view_data['invoice']->company_id);
			$company = Company::find($opt);
			$payment = (int) $this->db->query('Select Count(*) as payment from facture_has_payments where facture_id=' . $id)->result()[0]->payment;
			$this->view_data['payment_reference'] = $payment + 1;

			// Moyens de paiement
			$this->view_data['typepaiement'] = $this->referentiels->getReferentielsByIdType($this->idMoyensPaiement);

			// TVA applicable
			if ($company->tva == 1) {
				$amount = $this->view_data['invoice']->sumht;
				if ($company->timbre_fiscal == 0) {
					$amount = $amount + $this->view_data['invoice']->timbre_fiscal;
				}
			} else {
				$amount = $this->view_data['invoice']->sum;
			}

			// Retenue de garantie
			if ($company->guarantee == 1) {
				$amount = $amount - $amount * 10 / 100;
			}
			$this->view_data['sumRest'] = $amount - $this->view_data['invoice']->paid;
			$this->theme_view = 'modal';
			$this->view_data['title'] = $this->lang->line('application_add_payment');
			$this->view_data['form_action'] = 'invoices/payment';
			$idType = $this->refType->getRefTypeByName($facture->currency)->id;
			$this->view_data['chiffre'] = $this->referentiels->getReferentielsByIdType($idType)[0]->name;

			$this->content_view = 'invoices/_payment';
		}
	}

	// supprimer un paiement
	function payment_delete($id = FALSE, $invoice_id = FALSE)
	{
		$this->db->where('id', $id);
		$payment = $this->db->get('facture_has_payments')->result()[0];
		$this->db->where('id', $invoice_id);
		$facture = $this->db->get('facture')->result()[0];
		$paid = $facture->paid - $payment->amount;
		$outstanding = $outstanding + $payment->amount;

		if ($paid == 0) {
			$status = $this->config->item("occ_facture_ouvert");
		} else {
			$status = $this->config->item("occ_facture_p_paye");
		}
		$data = array(
			"paid" => $paid,
			"outstanding" => $outstanding,
			"status" => $status
		);
		//get referentiels of  facture
		$this->db->where('id', $invoice_id);
		$this->db->set($data);
		$this->db->update('facture');
		$this->db->where('id', $id);
		$this->db->delete('facture_has_payments');
		$this->update_progress_projet($facture->project_id);
		$this->content_view = 'invoices/view';
		if (!$payment) {
			$this->session->set_flashdata('message', 'error:' . $this->lang->line('messages_delete_payment_error'));
		} else {
			$this->session->set_flashdata('message', 'success:' . $this->lang->line('messages_delete_payment_success'));
		}
		redirect('invoices/view/' . $invoice_id);
	}

	function _twocheckout($id = FALSE, $sum = FALSE)
	{
		$option = array("id_vcompanies" => $_SESSION['current_company']);
		$data["core_settings"] = Setting::find($option);
		if ($_POST) {
			$invoice = Invoice::find_by_id($_POST['id']);
			$invoice_reference = $data["core_settings"]->invoice_prefix . $invoice->reference;
			$this->load->file(APPPATH . 'helpers/2checkout/Twocheckout.php', true);
			$token = $_POST["token"];
			Twocheckout::privateKey($data["core_settings"]->twocheckout_private_key);
			Twocheckout::sellerId($data["core_settings"]->twocheckout_seller_id);
			//Get currency
			$currency = $invoice->currency;
			$currency_codes = getCurrencyCodesForTwocheckout();
			if (!array_key_exists($currency, $currency_codes)) {
				$currency = $data["core_settings"]->twocheckout_currency;
			}

			try {
				$charge = Twocheckout_Charge::auth(array(
					"merchantOrderId" => $invoice->reference,
					"token" => $_POST['token'],
					"currency" => $currency,
					"total" => $_POST['sum'],
					"billingAddr" => array(
						"name" => $invoice->company->name,
						"addrLine1" => $invoice->company->address,
						"city" => $invoice->company->city,
						"zipCode" => $invoice->company->zipcode,
						"country" => $invoice->company->country,
						"email" => $invoice->company->client->email,
						"phoneNumber" => $invoice->company->phone
					)
				));

				if ($charge['response']['responseCode'] == 'APPROVED') {
					echo "Thanks for your Order!";
					echo "<h3>Return Parameters:</h3>";
					echo "<pre>";
					print_r($charge);
					echo "</pre>";

					$attr = array();
					$paid_date = date('Y-m-d', time());
					$payment_reference = $invoice->reference . '00' . InvoiceHasPayment::count(array('conditions' => 'invoice_id = ' . $invoice->id)) + 1;
					$attributes = array('invoice_id' => $invoice->id, 'reference' => $payment_reference, 'amount' => $_POST['sum'], 'date' => $paid_date, 'type' => 'credit_card', 'notes' => '');
					$invoiceHasPayment = InvoiceHasPayment::create($attributes);

					if ($_POST['sum'] >= $invoice->outstanding) {
						$invoice->update_attributes(array('paid_date' => $paid_date, 'status' => 'Paid'));
					} else {
						$invoice->update_attributes(array('status' => 'PartiallyPaid'));
					}

					$this->session->set_flashdata('message', 'success:' . $this->lang->line('messages_payment_complete'));
					log_message('error', '2Checkout: Payment of ' . $_POST['sum'] . ' for invoice ' . $invoice_reference . ' received!');

				}
			} catch (Twocheckout_Error $e) {
				$this->session->set_flashdata('message', 'error: Your payment could NOT be processed (i.e., you have not been charged) because the payment system rejected the transaction.');
				log_message('error', '2Checkout: Payment of invoice ' . $invoice_reference . ' failed - ' . $e->getMessage());
			}
			redirect('invoices/view/' . $_POST['id']);
		} else {
			$this->view_data['invoices'] = Invoice::find_by_id($id);

			$this->view_data['publishable_key'] = $data["core_settings"]->twocheckout_publishable_key;
			$this->view_data['seller_id'] = $data["core_settings"]->twocheckout_seller_id;

			$this->view_data['sum'] = $sum;
			$this->theme_view = 'modal';
			$this->view_data['title'] = $this->lang->line('application_pay_with_credit_card');
			$this->view_data['form_action'] = 'invoices/twocheckout';
			$this->content_view = 'invoices/_2checkout';
		}
	}


	// créér la facture en pdf
	function preview($id = FALSE, $attachment = FALSE)
	{
		$this->load->helper(array('dompdf', 'file'));
		$this->load->library('parser');
		$this->load->database();
		$this->db->where('id', $id);
		$data["invoice"] = $this->db->get('facture')->result()[0];
		$option = array("id_vcompanies" => $_SESSION['current_company']);
		$data["core_settings"] = Setting::find($option);
		//get referentiels of  facture
		$data["invoice"]->status = $this->referentiels->getReferentielsById($data["invoice"]->status)->name;
		$this->db->where('facture_id', $id);
		$this->db->order_by('position', 'asc');
		$data['items'] = $this->db->get('facture_has_items')->result();
		$countDiscount = $this->db->query("SELECT discount FROM facture_has_items WHERE facture_id = '" . $id . "' AND discount > 0")->result();
		$data['countDiscount'] = count($countDiscount);
		if ($data["invoice"]->project_id != 0) {
			$data['num_project'] = $this->project->getProjectById($data["invoice"]->project_id)->id;
			$data['project'] = Project::find($data["invoice"]->project_id);
		}
		$company = $this->db->where('id', $data["invoice"]->company_id)->get('companies')->result()[0];
		$data['company'] = $company;

		$this->db->where('id', $data['company']->client_id);
		$data['client'] = $this->db->get('clients')->result()[0];
		$this->db->where('id', $_SESSION['current_company']);
		$data['vcompanies'] = $this->db->get('v_companies')->result();
		$this->db->where('id', $_SESSION['current_company']);
		$data['logo'] = $this->db->get('v_companies')->result()[0]->picture;

		//chiffre of Devise
		$refTypeCurrency = $this->refType->getRefTypeByName($data["invoice"]->currency)->id;
		$data['chiffre'] = $this->referentiels->getReferentielsByIdType($refTypeCurrency)[0]->name;
		$parse_data = array(
			'invoice_id' => $data["core_settings"]->invoice_prefix . $data["invoice"]->reference,
			'client_link' => $data["core_settings"]->domain,
			'company' => $data["core_settings"]->company,
			'client_id' => $data["invoice"]->company->reference,
		);

		//$v = $data["core_settings"]->template. '/' .$data["core_settings"]->invoice_pdf_template.$data["core_settings"]->default_template;
		//var_dump($html);exit;
		$html = $this->load->view($data["core_settings"]->template . '/' . $data["core_settings"]->invoice_pdf_template . $data["core_settings"]->default_template, $data, true);

		$html = $this->parser->parse_string($html, $parse_data);
		$filename = $data['invoice']->estimate_num . '_' . Unaccent($data['company']->name);
		//var_dump(	$filename);exit;

		//Générer le pdf
		$this->pdf->load_view($html, $filename);

	}

	/*function previewHTML($id = FALSE){
			$this->load->helper(array('file'));
			$this->load->library('parser');
			$data["htmlPreview"] = true;
			$this->db->where('id',$id);
			$data["invoice"] = $this->db->get('facture')->result()[0];
			$this->db->where('facture_id',$id);
			$data['items'] = $this->db->get('facture_has_items')->result()[0];
			$option=array("id_vcompanies"=>$_SESSION['current_company']);
			$data["core_settings"] = Setting::find($option);

		   $parse_data = array(
							   'invoice_id' => $data["core_settings"]->invoice_prefix.$data["invoice"]->reference,
							   'client_link' => $data["core_settings"]->domain,
							   'company' => $data["core_settings"]->company,
							   'client_id' => $data["invoice"]->company->reference,
							   );
		   $html = $this->load->view($data["core_settings"]->template. '/' .$data["core_settings"]->invoice_pdf_template, $data, true);
		   $html = $this->parser->parse_string($html, $parse_data);
			$this->theme_view = 'blank';
		   $this->content_view = 'invoices/_preview';
	   }*/

	function sendinvoice($id = FALSE)
	{
		$this->load->helper(array('dompdf', 'file'));
		$this->load->library('parser');

		$data["invoice"] = Invoice::find($id);
		$data['items'] = InvoiceHasItem::find('all', array('conditions' => array('invoice_id=?', $id)));
		$option = array("id_vcompanies" => $_SESSION['current_company']);
		$data["core_settings"] = Setting::find($option);
		//$due_date = date($data["core_settings"]->date_format, human_to_unix($data["invoice"]->due_date.' 00:00:00'));
		//Set parse values
		$parse_data = array(
			'client_contact' => $data["invoice"]->company->client->firstname . ' ' . $data["invoice"]->company->client->lastname,
			'client_company' => $data["invoice"]->company->name,
			'invoice_id' => $data["core_settings"]->invoice_prefix . $data["invoice"]->reference,
			'client_link' => $data["core_settings"]->domain,
			'company' => $data["core_settings"]->company,
			'logo' => '<img src="' . base_url() . '' . $data["core_settings"]->logo . '" alt="' . $data["core_settings"]->company . '"/>',
			'invoice_logo' => '<img src="' . base_url() . '' . $data["core_settings"]->invoice_logo . '" alt="' . $data["core_settings"]->company . '"/>'
		);
		// Generate PDF
		$html = $this->load->view($data["core_settings"]->template . '/' . $data["core_settings"]->invoice_pdf_template, $data, true);
		$html = $this->parser->parse_string($html, $parse_data);
		$filename = $this->lang->line('application_invoice') . '_' . $data["core_settings"]->invoice_prefix . $data["invoice"]->reference;
		pdf_create($html, $filename, FALSE);
		//email
		$subject = $this->parser->parse_string($data["core_settings"]->invoice_mail_subject, $parse_data);
		$this->email->from($data["core_settings"]->email, $data["core_settings"]->company);
		if (!isset($data["invoice"]->company->client->email)) {
			$this->session->set_flashdata('message', 'error:This client company has no primary contact! Just add a primary contact.');
			redirect('invoices/view/' . $id);
		}
		$this->email->to($data["invoice"]->company->client->email);
		$this->email->subject($subject);
		$this->email->attach("files/temp/" . $filename . ".pdf");
		$email_invoice = read_file('./application/views/' . $data["core_settings"]->template . '/templates/email_invoice.html');
		$message = $this->parser->parse_string($email_invoice, $parse_data);
		$this->email->message($message);
		if ($this->email->send()) {
			$this->session->set_flashdata('message', 'success:' . $this->lang->line('messages_send_invoice_success'));
			$data["invoice"]->update_attributes(array('status' => 'Sent', 'sent_date' => date("Y-m-d")));
			log_message('error', 'Invoice #' . $data["core_settings"]->invoice_prefix . $data["invoice"]->reference . ' has been send to ' . $data["invoice"]->company->client->email);
		} else {
			$this->session->set_flashdata('message', 'error:' . $this->lang->line('messages_send_invoice_error'));
			log_message('error', 'ERROR: Invoice #' . $data["core_settings"]->invoice_prefix . $data["invoice"]->reference . ' has not been send to ' . $data["invoice"]->company->client->email . '. Please check your servers email settings.');
		}
		unlink("files/temp/" . $filename . ".pdf");
		redirect('invoices/view/' . $id);
	}

	/*****Ligne Vide**********************************/
	function itemEmpty($id = FALSE)
	{
		$this->load->database();
		if ($_POST) {
			unset($_POST['send']);
			$_POST = array_map('htmlspecialchars', $_POST);
			if ($_POST['name'] != "") {
				$_POST['name'] = $_POST['name'];
				$_POST['value'] = $_POST['value'];
				if ($_POST['discount'] != NULL) {
					$_POST['discount'] = $_POST['discount'];
				} else {
					$_POST['discount'] = 0;
				}
				$_POST['tva'] = $_POST['tva'];

			} else {
				if ($_POST['item_id'] == "-") {
					$this->session->set_flashdata('message', 'error:' . $this->lang->line('messages_add_item_error'));
					redirect('invoices/view/' . $_POST['invoice_id']);

				} else {
					$rebill = explode("_", $_POST['item_id']);
					if ($rebill[0] == "rebill") {
						$itemvalue = Expense::find_by_id($rebill[1]);
						$_POST['name'] = $itemvalue->description;
						$_POST['value'] = $itemvalue->value;
						$itemvalue->rebill = 2;
						$itemvalue->invoice_id = $_POST['facture_id'];
						$itemvalue->save();
					} else {
						$itemvalue = Item::find_by_id($_POST['item_id']);
						$_POST['name'] = $itemvalue->name;
						$_POST['value'] = $itemvalue->value;
					}
				}
			}
			$item = $this->db->insert('facture_has_items', $_POST);
			if (!$item) {
				$this->session->set_flashdata('message', 'error:' . $this->lang->line('messages_add_item_error'));
			} else {
				$this->session->set_flashdata('message', 'success:' . $this->lang->line('messages_add_item_success'));
			}
			redirect('invoices/view/' . $_POST['facture_id']);

		} else {
			$this->db->where('id', $id);
			$facture = $this->db->get('facture')->result()[0];
			$this->view_data['item_units'] = $this->db->query("SELECT * FROM item_units")->result();
			$type = $this->db->query('select libelle,id from items_has_family where inactive=0 UNION select libelle,id from items_has_family_parent where inactive=0');
			$this->view_data['type'] = $type->result();
			$this->view_data['tva'] = $this->db->query('select * from ref_type_occurences where id_type=9 and visible=1')->result();
			$this->view_data['defaultTva'] = $settings = setting::find(array('id_vcompanies' => $_SESSION['current_company']))->tax;
			$this->view_data['invoice'] = $facture;
			$this->view_data['items'] = Item::find('all', array('conditions' => array('inactive=?', '0')));
			$this->view_data['rebill'] = Expense::find('all', array('conditions' => array('project_id=? and (rebill=? or invoice_id=?)', $this->view_data['invoice']->project_id, 1, $id)));
			$this->theme_view = 'modal';
			$this->view_data['title'] = $this->lang->line('application_add_item');
			$this->view_data['form_action'] = 'invoices/itemEmpty';
			$this->content_view = 'invoices/_itemEmpty';
		}
	}

	function convert($data, $index = 0)
	{
		$output = array_filter($data, function ($item) use ($index) {
			return $item->parent == $index;
		});
		$real_output = array();
		foreach ($output as $item) {
			$real_output[] = array('id' => $item->id, 'libelle' => $item->libelle, 'children' => $this->convert($data, $item->id));
		}
		return $real_output;
	}

	//	Ajouter un article à une facture
	function item($id = FALSE)
	{
		$this->load->database();
		if ($_POST) {

			unset($_POST['send']);
			$_POST = array_map('htmlspecialchars', $_POST);
			if ($_POST['name'] != "") {
				//insert new item
				$d = array(
					'name' => $_POST['name'],
					'value' => $_POST['value'],
					'tva' => $_POST['tva'],
					'name' => $_POST['name'],
					'id_family' => $_POST['id_family'],
					'unit' => $_POST['unit'],
					'description' => $_POST['description']
				);
				$this->db->insert('items', $d);
				$_POST['item_id'] = Item::last()->id;
			} else {
				if ($_POST['item_id'] == "-") {
					$this->session->set_flashdata('message', 'error:' . $this->lang->line('messages_add_item_error'));
					redirect('invoices/view/' . $_POST['invoice_id']);

				} else {
					$rebill = explode("_", $_POST['item_id']);
					if ($rebill[0] == "rebill") {
						$itemvalue = Expense::find_by_id($rebill[1]);
						$_POST['name'] = $itemvalue->description;
						$_POST['value'] = $itemvalue->value;
						$itemvalue->rebill = 2;
						$itemvalue->invoice_id = $_POST['facture_id'];
						$itemvalue->save();
					} else {
						$itemvalue = Item::find_by_id($_POST['item_id']);
						$_POST['name'] = $itemvalue->name;
						$_POST['value'] = $_POST['Prixunitaire'];
					}
				}
			}
			unset($_POST['id_family']);
			unset($_POST['Prixunitaire']);
			$item = $this->db->insert('facture_has_items', $_POST);
			if (!$item) {
				$this->session->set_flashdata('message', 'error:' . $this->lang->line('messages_add_item_error'));
			} else {
				$this->session->set_flashdata('message', 'success:' . $this->lang->line('messages_add_item_success'));
			}
			redirect('invoices/view/' . $_POST['facture_id']);
		} else {
			$families = $this->db->query('select * from  items_has_family where inactive=0 ')->result();
			$this->view_data['families'] = $this->convert($families);
			$this->db->where('id', $id);
			$facture = $this->db->get('facture')->result()[0];
			$company = Company::find_by_id($facture->company_id);
			$this->view_data['company'] = $company;
			$this->view_data['item_units'] = $this->db->query("SELECT * FROM item_units")->result();
			$type = $this->db->query('select libelle,id from items_has_family where inactive=0 UNION select libelle,id from items_has_family_parent where inactive=0');
			$this->view_data['type'] = $type->result();

			$this->view_data['invoice'] = $facture;
			$idType = $this->refType->getRefTypeByName($facture->currency)->id;
			$this->view_data['chiffre'] = $this->referentiels->getReferentielsByIdType($idType)->name;
			$this->view_data['items'] = Item::find('all', array('conditions' => array('inactive=?', '0')));
			//Créer la liste des articles
			$list_items = array();
			$list_items['0'] = '-';
			foreach ($this->view_data['items'] as $value):
				$list_items[$value->id] = $value->name . " - " . $value->value . " " . $core_settings->currency;
			endforeach;
			$this->view_data['list_items'] = $list_items;
			$this->view_data['title'] = $this->lang->line('application_add_item');
			$this->view_data['tva'] = $this->referentiels->getReferentielsByIdType($this->config->item("type_id_tva"));
			$this->theme_view = 'modal';
			$this->view_data['form_action'] = 'invoices/item';
			$this->content_view = 'vente/_item';
		}
	}

	function duplicateItemEmpty($id)
	{
		$this->db->where('id', $id);
		$factureItem = $this->db->get('facture_has_items')->result()[0];
		$lastId = $this->factureHasItem->getLastId() + 1;
		$factureItem->id = $lastId;
		$item = $this->db->insert('facture_has_items', $factureItem);
		if (!$item) {
			$this->session->set_flashdata('message', 'error:' . $this->lang->line('messages_duplicate_item_error'));
		} else {
			$this->session->set_flashdata('message', 'success:' . $this->lang->line('messages_duplicate_item_success'));
		}
		redirect('invoices/view/' . $factureItem->facture_id);
	}

	function item_update($id = FALSE)
	{
		$this->load->database();
		if ($_POST) {
			unset($_POST['send']);
			$_POST = array_map('htmlspecialchars', $_POST);
			$this->db->where('id', $_POST['id']);
			$item = $this->db->set($_POST);
			$item = $this->db->update('facture_has_items');
			if (!$item) {
				$this->session->set_flashdata('message', 'error:' . $this->lang->line('messages_save_item_error'));
			} else {
				$this->session->set_flashdata('message', 'success:' . $this->lang->line('messages_save_item_success'));
			}
			redirect('invoices/view/' . $_POST['facture_id']);

		} else {
			$this->db->where('id', $id);
			$item = $this->db->get('facture_has_items')->result()[0];
			$this->view_data['invoice_has_items'] = $item;
			$type = $this->db->query('select libelle,id from items_has_family where inactive=0 UNION select libelle,id from items_has_family_parent where inactive=0');
			$this->view_data['type'] = $type->result();
			$this->theme_view = 'modal';
			$this->view_data['title'] = $this->lang->line('application_edit_item');
			$item_units = $this->db->query("SELECT * FROM item_units")->result();
			$this->view_data['item_units'] = $item_units;
			$this->view_data['form_action'] = 'invoices/item_update';
			$this->content_view = 'invoices/_item';
		}
	}

	function item_update_empty($id = FALSE)
	{
		$this->load->database();
		if ($_POST) {
			unset($_POST['send']);
			$_POST = array_map('htmlspecialchars', $_POST);
			$this->db->where('id', $_POST['id']);
			$item = $this->db->set($_POST);
			$item = $this->db->update('facture_has_items');
			if (!$item) {
				$this->session->set_flashdata('message', 'error:' . $this->lang->line('messages_save_item_error'));
			} else {
				$this->session->set_flashdata('message', 'success:' . $this->lang->line('messages_save_item_success'));
			}
			redirect('invoices/view/' . $_POST['facture_id']);

		} else {
			$this->db->where('id', $id);
			$item = $this->db->get('facture_has_items')->result()[0];
			$this->view_data['invoice_has_items'] = $item;
			$type = $this->db->query('select libelle,id from items_has_family where inactive=0 UNION select libelle,id from items_has_family_parent where inactive=0');
			$this->view_data['type'] = $type->result();
			$this->theme_view = 'modal';
			$this->view_data['title'] = $this->lang->line('application_edit_item');
			$item_units = $this->db->query("SELECT * FROM item_units")->result();
			$this->view_data['item_units'] = $item_units;
			$this->view_data['form_action'] = 'invoices/item_update_empty';
			$this->view_data['tva'] = $this->db->query('select * from ref_type_occurences where id_type=9 and visible=1')->result();
			$this->content_view = 'invoices/_itemEmpty';
		}
	}

	function item_delete($id, $invoice_id)
	{
		$this->load->database();
		$condition = array("id" => $id, "facture_id" => $invoice_id);
		$this->db->where($condition);
		$item = $this->db->delete('facture_has_items');
		$this->content_view = 'invoices/view';
		if (!$item) {
			$this->session->set_flashdata('message', 'error:' . $this->lang->line('messages_delete_item_error'));
		} else {
			$this->session->set_flashdata('message', 'success:' . $this->lang->line('messages_delete_item_success'));
		}
		redirect('invoices/view/' . $invoice_id);
	}

	function duplicate($id)
	{
		$this->db->where('id', $id);
		$facture = $this->db->get('facture')->result()[0];
		$option = array("id_vcompanies" => $_SESSION['current_company']);
		$settings = Setting::find($option);
		$new_invoice_reference = $settings->invoice_reference;
		$facture->reference = $settings->invoice_reference;
		$facture->estimate_reference = $settings->estimate_reference;
		$facture->issue_date = date('Y-m-d');
		$date = $facture->issue_date;
		$echeance = date('Y-m-d', strtotime($date . " +" . $settings->echeance . "days"));
		$idType = $this->refType->getRefTypeByName('Facture')->id;
		$facture->status = $this->referentiels->getReferentiels($idType, 'Open')->id;
		$this->db->select_max('facture.id');
		$this->db->from('facture');
		$factureid = $this->db->get()->result()[0]->id;
		$factureLastId = $facture->id;
		$facture->id = $factureid + 1;
		//$inc = $settings->estimate_reference + 1;
		//si le num de ref est inf à 10

		if ($new_invoice_reference < 10) {
			$new_invoice_reference = '0' . $new_invoice_reference;
		}

		//-------------------------- Devis name
		$estimate_pieces = explode("-", strrev($settings->invoice_prefix));
		$var = date("y-m-d", strtotime($date));
		$pieces = explode("-", $var);
		$piecesYear = $pieces[0];
		$piecesMounth = $pieces[1];
		$subpiecesYear = explode(' ', $pieces[0]);
		// année
		if ($estimate_pieces[0] == "YY") {
			$facture->estimate_num = strrev($estimate_pieces[1]) . $subpiecesYear[0] . $new_invoice_reference;
		}
		//année + mois
		else {
			$facture->estimate_num = strrev($estimate_pieces[2]) . $subpiecesYear[0] . $piecesMounth . $new_invoice_reference;
		}
		$facture->timbre_fiscal = $settings->timbre_fiscal;
		$facture->creation_date = date("Y-m-d");
		$this->db->insert('facture', $facture);
		$settings->update_attributes(array('invoice_reference' => $new_invoice_reference + 1));
		$this->db->where('facture_id', $factureLastId);
		$items = $this->db->get('facture_has_items')->result();

		foreach ($items as $item) {
			unset($item->id);
			$item->facture_id = $facture->id;
			$this->db->insert('facture_has_items', $item);
		}
		redirect('invoices');
	}

	public function renderItem($name)
	{
		$item = $this->item->getById(urldecode($name));
		$output = array(
			"value" => $item->value,
			"tva" => $item->tva,
			"unit" => $item->unit,

		);
		header('Content-Type: application/json');
		echo json_encode($output);
		exit();
	}

	//Pass to avoir
	function PassToAvoir($id)
	{
		$this->db->where('id', $id);
		$facture = $this->db->get('facture')->result()[0];
		$this->db->where('facture_id', $facture->id);
		$items = $this->db->get('facture_has_items')->result();
		unset($facture->id);
		$settings = Setting::first();
		//insert new avoir
		$facture->creation_date = date('Y-m-d');
		//revert ref estimate
		$year = date("Y");
		$lastavoir = $this->avoir->getLastAvoir();
		$lastRef = explode('-', $lastavoir->creation_date);
		if ($lastRef[0] != $year) {
			$settings->avoir_reference = 1;
		}
		$new_avoir_reference = $settings->avoir_reference;
		if ($new_avoir_reference < 10) {
			$new_avoir_reference = '0' . $new_avoir_reference;
		}
		$settings->update_attributes(array('avoir_reference' => $new_avoir_reference + 1));
		//update status facture

		$facture->status = $this->config->item("occ_facture_avoir");
		//var_dump($facture->status);exit;

		$this->db->where('id', $id);
		$this->db->set($facture);
		$this->db->update('facture');
		$facture->issue_date = date('Y-m-d', time());
		//-------------------------- avoir name
		$avoir_pieces = explode("-", strrev($settings->avoir_prefix));
		$var = date("y-m-d");
		$pieces = explode("-", $var);
		$piecesYear = $pieces[0];
		$piecesMounth = $pieces[1];
		$subpiecesYear = explode(' ', $pieces[0]);
		// année
		if ($avoir_pieces[0] == "YY") {
			$facture->avoir_num = strrev($avoir_pieces[1]) . $subpiecesYear[0] . $new_avoir_reference;
		}
		//année + mois
		else {
			$facture->avoir_num = strrev($avoir_pieces[2]) . $subpiecesYear[0] . $piecesMounth . $new_avoir_reference;
		}
		//referenece avoir
		$facture->reference = Avoir::last()->reference + 1;

		$facture->status = $this->config->item("occ_avoir_ouvert");
		$compnay = Company::find($facture->company_id);
		if ($compnay->timbre_fiscal == 0) {
			$facture->sum = $facture->sum - $settings->timbre_fiscal;
			$facture->outstanding = $facture->outstanding - $settings->timbre_fiscal;
		}
		//var_dump($facture);exit;
		unset($facture->timbre_fiscal);
		unset($facture->avoir_date);
		$this->db->insert('avoirs', $facture);
		$avoir_id = Avoir::last()->id;
		//add item of avoir
		foreach ($items as $item) {
			unset($item->id);
			$item->avoir_id = $avoir_id;
			unset($item->facture_id);
			$this->db->insert('avoir_has_items', $item);
		}
		//update avoir ref in core
		$settings->update_attributes(array('avoir_reference' => $new_avoir_reference + 1));
		$this->view_data['lastUrl'] = 'facture';
		$this->view_data['settings'] = $settings;
		redirect('avoir');
	}

	function sendfiles($id)
	{
		if ($_POST) {
			$id = $_POST['id'];
			$data["invoice"] = $this->invoice->getById($id)[0];
			//Save file
			$this->load->database();
			$this->load->helper('file');
			//require_once('dompdf/dompdf_config.inc.php');
			$pdfroot = dirname(dirname(__FILE__));
			$pdfroot .= '/third_party/pdf/facture.pdf';
			//$dompdf = new Dompdf();
			$option = array("id_vcompanies" => $_SESSION['current_company']);
			$data["core_settings"] = Setting::find($option);
			//get referentiels of  facture
			$data["invoice"]->status = $this->referentiels->getReferentielsById($data["invoice"]->status)->name;
			$this->db->where('facture_id', $id);
			$this->db->order_by('position', 'asc');
			$data['items'] = $this->db->get('facture_has_items')->result();
			$countDiscount = $this->db->query("SELECT discount FROM facture_has_items WHERE facture_id = '" . $id . "' AND discount > 0")->result();
			$data['countDiscount'] = count($countDiscount);
			$data['num_project'] = $this->project->getProjectById($data["invoice"]->project_id)->id;
			$this->db->where('id', $data['invoice']->company_id);
			$company = $this->db->get('companies')->result()[0];
			$data['company'] = $company;
			$this->db->where('id', $data['company']->client_id);
			$client = $this->db->get('clients')->result()[0];
			$data['client'] = $client;
			$this->db->where('id', $_SESSION['current_company']);
			$data['vcompanies'] = $this->db->get('v_companies')->result();
			$this->db->where('id', $_SESSION['current_company']);
			$logo = $this->db->get('v_companies')->result()[0]->picture;
			//chiffre of Devise
			$refTypeCurrency = $this->refType->getRefTypeByName($data["invoice"]->currency)->id;
			$chiffre = $this->referentiels->getReferentielsByIdType($refTypeCurrency)->name;
			$data['chiffre'] = $chiffre;
			$data['logo'] = $logo;
			$parse_data = array(
				'invoice_id' => $data["core_settings"]->invoice_prefix . $data["invoice"]->reference,
				'client_link' => $data["core_settings"]->domain,
				'company' => $data["core_settings"]->company,
				'client_id' => $data["invoice"]->company->reference,
			);
			$html = $this->load->view($data["core_settings"]->template . '/' . $data["core_settings"]->invoice_pdf_template . $data["core_settings"]->default_template, $data, true);
			//$dompdf->load_html($html);
			//$paper_orientation = 'Potrait';
			//$dompdf->set_paper($paper_orientation);
			////$dompdf->render();
			//$pdf_string =   $dompdf->output();
			file_put_contents($pdfroot, $pdf_string);
			//Send file
			sendMail($id, $_POST['smtp_user'], $pdfroot, $_POST['dist'], $_POST['cc'], $_POST['notes'], 'invoices/sendfiles', 'invoices/');
			//var_dump($dompdf); exit;

		} else {
			$this->view_data['form_action'] = 'invoices/sendfiles';
			$this->view_data['data'] = $this->invoice->getById($id)[0];
			$this->view_data['type'] = "facture";
			$this->content_view = 'settings/sendFile';
		}
	}
}
