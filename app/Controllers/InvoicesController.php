<?php

namespace App\Controllers;

use App\Models\ProjectModel;
use App\Models\FactureModel;
use App\Models\RefTypeOccurencesModel;
use App\Models\RefTypeModel;
use App\Models\ItemModel;
use App\Models\ItemFamilyModel;
use App\Models\FactureHasItemModel;
use App\Models\AvoirModel;
use App\Models\SettingModel;
use App\Models\CompanyModel;
use App\Models\ExpenseModel;
use App\Models\InvoiceHasItemModel;

use App\Controllers\BaseController;

class InvoicesController extends BaseController
{

	protected $idTypeEtatFacture, $idFactureOuvert, $idTypeEtatAvoir;
	protected $idAvoirOuvert, $idMoyensPaiement;

	protected $referentiels, $viewData = [];
	protected $refType, $companyModel, $settingModel;
	protected $invoice, $projectModel, $invoiceHasItemModel, $expenseModel;

	function __construct()
	{
		$this->referentiels = new RefTypeOccurencesModel();
		$this->refType = new RefTypeModel();
		$this->invoice = new FactureModel();
		$this->companyModel = new CompanyModel();
		$this->settingModel = new SettingModel();
		$this->projectModel = new ProjectModel();
		$this->invoiceHasItemModel = new InvoiceHasItemModel();
		$this->expenseModel = new ExpenseModel();

		// Replace with actual authentication checks
		if (!session()->has('client') && !session()->has('user')) {
			return redirect()->to('login');
		}

		$this->idTypeEtatFacture = $this->config->item("type_id_etat_facture");
		$this->idTypeEtatAvoir = $this->config->item("type_id_etat_avoir");
		$this->idFactureOuvert = $this->config->item("occ_facture_ouvert");
		$this->idAvoirOuvert = $this->config->item("occ_avoir_ouvert");
		$this->idMoyensPaiement = $this->config->item("type_id_moyens_paiement");


		$submenus = $this->referentiels->getReferentielsByIdType($this->idTypeEtatFacture);
		$this->viewData['submenu'][$this->lang->line('application_all')] = 'invoices';

		foreach ($submenus as $submenu) {
			if ($submenu->name != "Sent" && $submenu->name != "Pending") {
				if ($this->lang->line('application_' . $submenu->name) == false) {
					$this->viewData['submenu'][$submenu->name] = 'invoices/filter' . $submenu->name;
				} else {
					$this->viewData['submenu'][$this->lang->line('application_' . $submenu->name)] = 'invoices/filter' . $submenu->name;
				}
			}
		}
	}

	function index()
	{
		$facture = $this->invoice->whereIn('company_id', session()->get('current_company_id'))->orderBy('id', 'desc')->findAll();

		$this->viewData['settings'] = $this->settingModel->first();

		// Currency formatting
		foreach ($facture as $fact) {
			$refTypeCurrency = $this->refType->getRefTypeByName($fact->currency)->id ?? null;
			$fact->chiffre = $this->referentiels->getReferentielsByIdType($refTypeCurrency)[0]->name ?? null;
		}

		$this->viewData['invoices'] = $facture;

		// Date calculations
		$daysInMonth = date('t');
		$lastDayInMonth = date('Y-m-' . $daysInMonth);
		$firstDayInMonth = date('Y-m-01');

		$now = time();
		$beginningOfWeek = strtotime('last Monday', $now);
		$endOfWeek = strtotime('next Sunday', $now) + 86400;

		$this->viewData['invoices_paid_this_month_graph'] = $this->invoice->select([
			"COUNT(id) AS amount",
			"DATE_FORMAT(`paid_date`, '%w') AS date_day",
			"DATE_FORMAT(`paid_date`, '%Y-%m-%d') AS date_formatted"
		])
			->where("UNIX_TIMESTAMP(`paid_date`) >=", $beginningOfWeek)
			->where("UNIX_TIMESTAMP(`paid_date`) <=", $endOfWeek)
			->groupBy('paid_date')
			->findAll();

		return view('invoices/all', $this->viewData);
	}

	function calc()
	{
		$invoices = $this->invoice->where('estimate !=', 1)->findAll();
		$settings = (new SettingModel())->find(session()->get('current_company'));

		foreach ($invoices as $invoice) {
			$items = (new FactureHasItemModel())->where('invoice_id', $invoice->id)->findAll();
			$sum = array_reduce($items, function ($carry, $item) use ($invoice) {
				return $carry + ($item->amount * $item->value);
			}, 0);

			$discount = str_ends_with($invoice->discount, '%')
				? round(($sum / 100) * rtrim($invoice->discount, '%'), 2)
				: $invoice->discount;

			$sum -= $discount;

			$taxValue = $invoice->tax ?: $settings->tax;
			$secondTaxValue = $invoice->second_tax ?: $settings->second_tax;
			$tax = round(($sum / 100) * $taxValue, 2);
			$secondTax = round(($sum / 100) * $secondTaxValue, 2);

			$sum = round($sum + $tax + $secondTax, 2);
			$invoice->sum = $sum;
			$this->invoice->update($invoice->id, ['sum' => $sum]);
		}

		return redirect()->to('invoices');
	}

	//condition of filter
	public function filter($condition = false, $year = false)
	{
		$factures = [];

		if ($condition !== 'False') {
			$condition = urldecode($condition);
			// Get the id of status
			$idState = $this->referentiels->getReferentiels($this->idTypeEtatFacture, $condition)->id;
			$factures = $this->invoice->where('status', $idState)->findAll();
		} elseif ($year) {
			$allFactures = $this->invoice->findAll();
			foreach ($allFactures as $facture) {
				$issueYear = date('Y', strtotime($facture->issue_date));
				if ($issueYear == $year) {
					$factures[] = $facture;
				}
			}
		}

		foreach ($factures as $facture) {
			$refTypeCurrency = $this->refType->getRefTypeByName($facture->currency)->id;
			$facture->chiffre = $this->referentiels->getReferentielsByIdType($refTypeCurrency)->name;
			$facture->status = $this->referentiels->getReferentielsById($facture->status)->name;
		}

		$this->viewData['invoices'] = $factures;
		$this->viewData['settings'] = $this->settingModel->first();
		return view('invoices/all', $this->viewData);
	}

	//Créer une nouvelle facture
	function create()
	{
		if ($this->request->getMethod() === 'post') {
			$invoiceReference = $this->settingModel->first();
			$data = $this->request->getPost();
			$refType = $this->refType->getRefTypeByName($data['currency'])->id;
			$chiffre = $this->referentiels->getReferentielsByIdType($refType)->name;

			// Handle new company creation
			if ($data['company'] == 1) {
				$data['timbre_fiscal'] = $data['timbre_fiscal'] ?? 0;
				$company = $this->companyModel->orderBy('id', 'desc')->first();
				$reference = $company->reference + 1;

				$newCompanyData = [
					'name' => $data['nomClient'],
					'reference' => $reference,
					'passager' => '1',
					'tva' => $data['tva'],
					'guarantee' => $data['guarantee'],
					'timbre_fiscal' => $data['timbre_fiscal']
				];
				$this->companyModel->insert($newCompanyData);
				$data['company_id'] = $this->companyModel->insertID();
				unset($data['company']);

				// Update company reference
				$invoiceReference->update(['company_reference' => $invoiceReference->company_reference + 1]);
			}

			// Handle project reference
			if ($data['project_id'] != 0) {
				$project = $this->projectModel->getProjectByRef($data['project_id']);
				$dataProject['project_id'] = $project->id;
			}

			// Create new project if name is provided
			if (!empty($data['name'])) {
				$lastProject = $this->projectModel->orderBy('id', 'desc')->first();
				$data['project_id'] = $lastProject->reference + 1;

				$numero = $this->generateProjectNumber($invoiceReference, $data['start']);
				unset($data['name'], $data['start'], $data['end']);

				$projectData = [
					'name' => $data['name'],
					'reference' => $data['project_id'],
					'datetime' => time(),
					'progress' => 0,
					'start' => $data['start'],
					'end' => $data['end'],
					'project_num' => $numero,
					'company_id' => $data['company_id'],
					'creation_date' => date('Y-m-d')
				];
				$this->projectModel->insert($projectData);
				$invoiceReference->update(['project_reference' => $invoiceReference->project_reference + 1]);
				$dataProject['project_id'] = $this->projectModel->insertID();
			}

			// Prepare invoice data
			$this->prepareInvoiceData($data, $invoiceReference);
			$invoiceId = $this->invoice->insert($data);

			if (isset($dataProject['project_id'])) {
				$dataProject['invoice_id'] = $invoiceId;
				$this->projectModel->insert($dataProject);
			}

			// Set flash message and redirect
			session()->setFlashdata('message', $invoiceId ? 'success:Invoice created successfully' : 'error:Invoice creation failed');
			return redirect()->to('invoices');
		}

		// Render create invoice form
		$this->viewData['invoices'] = $this->invoice->findAll();
		$this->viewData['next_reference'] = $this->invoice->orderBy('id', 'desc')->first();
		$this->viewData['companies'] = $this->companyModel->where('inactive', '0')->findAll();
		$this->viewData['current_date'] = date('Y-m-d');
		$this->viewData['current_echeance'] = date('Y-m-d', strtotime("+{$settings->echeance} days"));

		return view('invoices/_invoice', $this->viewData);
	}

	protected function generateProjectNumber($invoiceReference, $startDate)
	{
		$projectPrefixPieces = explode("-", strrev($invoiceReference->project_prefix));
		$date = date("Y-m-d", strtotime($startDate));
		$pieces = explode("-", $date);
		$piecesYear = $pieces[0];
		$piecesMonth = $pieces[1];
		$subpiecesYear = explode('0', $piecesYear);
		$ref = $this->projectModel->orderBy('id', 'desc')->first()->reference + 1;

		if ($ref < 10) {
			return '0' . $ref;
		}

		return $projectPrefixPieces[0] == 'YY'
			? strrev($projectPrefixPieces[1]) . $subpiecesYear[1] . $ref
			: strrev($projectPrefixPieces[2]) . $subpiecesYear[1] . $piecesMonth . $ref;
	}

	protected function prepareInvoiceData(array &$data, $invoiceReference)
	{
		$year = date("Y");
		$lastInvoice = $this->invoice->orderBy('id', 'desc')->first();
		$lastRefYear = date('Y', strtotime($lastInvoice->creation_date));

		$data['creation_date'] = date("Y-m-d");
		$data['status'] = config('App\Config\Settings')->occ_facture_ouvert;

		$data['reference'] = ($lastRefYear != $year) ? 1 : $invoiceReference->invoice_reference;
		$data['estimate_reference'] = str_pad($data['reference'], 2, '0', STR_PAD_LEFT);
		$data['estimate_num'] = $invoiceReference->invoice_prefix . $data['estimate_reference'];

		// Generate estimate number
		$estimatePrefixPieces = explode("-", strrev($invoiceReference->invoice_prefix));
		$estimateDate = date("y-m-d", strtotime($data['issue_date']));
		$estimatePieces = explode("-", $estimateDate);
		$subpiecesYear = explode(' ', $estimatePieces[0]);

		if ($estimatePrefixPieces[0] == "YY") {
			$data['estimate_num'] = strrev($estimatePrefixPieces[1]) . $subpiecesYear[0] . $data['estimate_reference'];
		} else {
			$data['estimate_num'] = strrev($estimatePrefixPieces[2]) . $subpiecesYear[0] . $estimatePieces[1] . $data['estimate_reference'];
		}

		$data['timbre_fiscal'] = $invoiceReference->timbre_fiscal;

		$invoiceReference->update(['invoice_reference' => $data['reference'] + 1]);
	}


	public function update($id = null, $getView = false)
	{
		if ($this->request->getMethod() === 'post') {
			$data = $this->request->getPost();
			$invoice = $this->invoice->find($id);
			$company = $this->companyModel->find($invoice->company_id);
			$invoiceReference = $this->settingModel->first();

			// Handle company updates
			if ($data['company'] == 1) {
				$this->handleCompanyUpdate($data, $company, $invoiceReference);
				unset($data['company']);
			}

			// Handle project updates
			if ($data['project_id'] != 0) {
				$project = $this->projectModel->getProjectByRef($data['project_id']);
				$data['project_id'] = $project->id;
			}

			// Handle new project creation if name is provided
			if (!empty($data['name'])) {
				$data['project_id'] = $this->createNewProject($data, $invoiceReference);
			}

			// Update invoice
			$this->invoice->update($id, $data);
			$this->setFlashMessage('Invoice updated successfully');

			return redirect()->to($getView ? "invoices/view/{$id}" : 'invoices');
		}

		// Load invoice for editing
		$invoice = $this->invoice->find($id);
		$this->viewData['invoice'] = $invoice;
		$this->viewData['companies'] = $this->companyModel->where('inactive', '0')->findAll();
		$this->viewData['title'] = 'Edit Invoice';

		return view('invoices/edit', $this->viewData);
	}

	protected function handleCompanyUpdate(array &$data, $company, $invoiceReference)
	{
		if ($company->passager != 1) {
			// Create new company
			$newCompanyData = [
				'name' => $data['nomClient'],
				'id_vcompanies' => session()->get('current_company'),
				'reference' => $this->companyModel->getLastReference() + 1,
				'passager' => '1',
				'tva' => $data['tva'],
				'guarantee' => $data['guarantee'],
				'timbre_fiscal' => $data['timbre_fiscal'],
			];
			$this->companyModel->insert($newCompanyData);
			$data['company_id'] = $this->companyModel->insertID();

			// Update company reference
			$invoiceReference->update(['company_reference' => $invoiceReference->company_reference + 1]);
		} else {
			// Update existing company
			$companyData = [
				'name' => $data['nomClient'],
				'tva' => $data['tva'],
				'guarantee' => $data['guarantee'],
				'timbre_fiscal' => $data['timbre_fiscal'],
			];
			$this->companyModel->update($company->id, $companyData);
		}
	}

	protected function createNewProject(array &$data, $invoiceReference)
	{
		$lastProject = $this->projectModel->getLast();
		$ref = $lastProject->reference + 1;

		$projectNum = $this->generateProjectNumber($invoiceReference, $data['start'], $ref);

		$projectData = [
			'name' => $data['name'],
			'id_vcompanies' => session()->get('current_company'),
			'reference' => $ref,
			'datetime' => time(),
			'progress' => 0,
			'start' => $data['start'],
			'end' => $data['end'],
			'project_num' => $projectNum,
			'company_id' => $data['company_id'],
		];

		$this->projectModel->insert($projectData);
		$invoiceReference->update(['project_reference' => $invoiceReference->project_reference + 1]);

		return $this->projectModel->insertID();
	}

	public function view($id = null)
	{
		$invoice = $this->invoice->find($id);
		$this->viewData['invoice'] = $invoice;

		if ($invoice->project_id != 0) {
			$this->viewData['project'] = $this->projectModel->find($invoice->project_id);
		}

		// Fetch currency details
		$currencyId = $this->referentiels->getReferentielsByName($invoice->currency)->id;
		$this->viewData['chiffre'] = $this->referentiels->getReferentielsById($currencyId)[0]->name;

		// Fetch company details
		$this->viewData['company'] = $this->companyModel->find($invoice->company_id);

		// Calculate totals
		$this->calculateInvoiceTotals($invoice);

		return view('invoices/view', $this->viewData);
	}

	protected function calculateInvoiceTotals($invoice)
	{
		$items = $this->invoice->getInvoiceItems($invoice->id);
		$total = 0;
		$subtotalTVA = 0;

		foreach ($items as $item) {
			$subtotal = ($item->amount * $item->value) - ($item->amount * $item->value * $item->discount) / 100;
			$subtotalTVA += $subtotal + ($subtotal * $item->tva) / 100;
			$total += $subtotal;
		}

		$company = $this->companyModel->find($invoice->company_id);
		$finalTotal = $company->tva ? $total : $subtotalTVA;

		$data = [
			'sum' => $finalTotal,
			'sumht' => $total,
			// Additional calculations can be added here
		];

		$this->invoice->update($invoice->id, $data);
	}

	public function bankTransfer($id = null)
	{
		$invoice = $this->invoice->find($id);
		$this->viewData['invoice'] = $invoice;
		$this->viewData['title'] = 'Bank Transfer';

		return view('invoices/bank_transfer', $this->viewData);
	}

	//mettre à jour l'avancement du projet par rapport à l'état des paiements des factures
	function update_progress_projet($id_projet)
	{
		$project_id = $id_projet;
		$payee = 0;

		if ($project_id != 0) {
			$factures = $this->invoice->getByIdProject($project_id);
			foreach ($factures as $facture) {
				if ($facture->status == $this->config->item("occ_facture_paye")) {
					$payee++;
				}
			}

			// Mettre à jour la progression du projet
			$progress = ($payee / count($factures)) * 100;
			$this->project->UpdateProgress($project_id, $progress);
		}
	}

	function payment($id = FALSE)
	{
		$this->load->database();
		$compteBancaire = $this->db->get('comptes_bancaires')->result();

		if ($_POST) {
			$_POST['amount'] = $this->formatAmount($_POST['amount']);
			unset($_POST['send']);

			$_POST['user_id'] = $this->user->id;
			$this->db->where('id', $_POST['facture_id']);
			$invoice = $this->db->get('facture')->result()[0];

			if ($_POST['type'] != "26") {
				unset($_POST['id_compteBancaire']);
			}

			$this->db->insert('facture_has_payments', $_POST);
			$this->processPayment($invoice, $_POST['amount'], $_POST['date']);
			redirect('invoices/view/' . $_POST['facture_id']);
		} else {
			$this->preparePaymentView($id);
		}
	}

	private function formatAmount($amount)
	{
		if (strpos($amount, ',') !== false) {
			$parts = explode(",", $amount);
			return $parts[0] . '.' . $parts[1];
		}
		return $amount;
	}

	private function processPayment($invoice, $amount, $date)
	{
		$factureoutstanding = $invoice->outstanding;
		$new_status = $this->calculateInvoiceStatus($factureoutstanding, $amount);

		$this->db->where('id', $invoice->id);
		$this->db->set('status', $new_status);
		if ($new_status == $this->config->item("occ_facture_paye")) {
			$this->db->set('paid_date', $date);
		}
		$this->db->update('facture');

		// Update project progress
		$this->update_progress_projet($invoice->project_id);
	}

	private function calculateInvoiceStatus($outstanding, $amount)
	{
		$outstanding -= $amount;
		return ($outstanding == 0) ? $this->config->item("occ_facture_paye") : $this->config->item("occ_facture_p_paye");
	}

	private function preparePaymentView($id)
	{
		$this->db->where('id', $id);
		$facture = $this->db->get('facture')->result()[0];

		$this->viewData['compteBancaires'] = $this->db->get('comptes_bancaires')->result();
		$this->viewData['invoice'] = $facture;

		$company = Company::find(['id' => $facture->company_id]);
		$this->viewData['payment_reference'] = $this->getPaymentReferenceCount($id) + 1;

		// Moyens de paiement
		$this->viewData['typepaiement'] = $this->referentiels->getReferentielsByIdType($this->idMoyensPaiement);
		$this->viewData['sumRest'] = $this->calculateRemainingAmount($company, $facture);

		$this->theme_view = 'modal';
		$this->viewData['title'] = $this->lang->line('application_add_payment');
		$this->viewData['form_action'] = 'invoices/payment';
		$this->viewData['chiffre'] = $this->referentiels->getReferentielsByIdType($this->refType->getRefTypeByName($facture->currency)->id)[0]->name;

		$this->content_view = 'invoices/_payment';
	}

	private function getPaymentReferenceCount($invoiceId)
	{
		return (int) $this->db->query('SELECT COUNT(*) as payment FROM facture_has_payments WHERE facture_id=' . $invoiceId)->result()[0]->payment;
	}

	private function calculateRemainingAmount($company, $facture)
	{
		$amount = ($company->tva == 1) ? $facture->sumht : $facture->sum;

		// Add timbre fiscal if applicable
		if ($company->timbre_fiscal == 0) {
			$amount += $facture->timbre_fiscal;
		}

		// Retenue de garantie
		if ($company->guarantee == 1) {
			$amount -= $amount * 0.10;
		}

		return $amount - $facture->paid;
	}

	function payment_delete($id = FALSE, $invoice_id = FALSE)
	{
		$payment = $this->getPayment($id);
		$facture = $this->getInvoice($invoice_id);

		// Calculer le montant payé et le montant restant
		$paid = $facture->paid - $payment->amount;
		$outstanding = $facture->outstanding + $payment->amount;

		// Déterminer le nouveau statut de la facture
		$status = ($paid == 0) ? $this->config->item("occ_facture_ouvert") : $this->config->item("occ_facture_p_paye");

		// Mettre à jour la facture
		$this->updateInvoiceStatus($invoice_id, $paid, $outstanding, $status);

		// Supprimer le paiement
		$this->deletePayment($id);

		// Mettre à jour l'avancement du projet
		$this->update_progress_projet($facture->project_id);

		// Gestion des messages de session
		$this->setFlashMessage($payment);

		redirect('invoices/view/' . $invoice_id);
	}

	// Obtenir un paiement par ID
	private function getPayment($id)
	{
		$this->db->where('id', $id);
		return $this->db->get('facture_has_payments')->row();
	}

	// Obtenir une facture par ID
	private function getInvoice($invoice_id)
	{
		$this->db->where('id', $invoice_id);
		return $this->db->get('facture')->row();
	}

	// Mettre à jour l'état de la facture
	private function updateInvoiceStatus($invoice_id, $paid, $outstanding, $status)
	{
		$data = array(
			"paid" => $paid,
			"outstanding" => $outstanding,
			"status" => $status
		);

		$this->db->where('id', $invoice_id);
		$this->db->set($data);
		$this->db->update('facture');
	}

	// Supprimer un paiement par ID
	private function deletePayment($id)
	{
		$this->db->where('id', $id);
		$this->db->delete('facture_has_payments');
	}

	// Définir les messages de session
	private function setFlashMessage($payment)
	{
		if (!$payment) {
			$this->session->set_flashdata('message', 'error:' . $this->lang->line('messages_delete_payment_error'));
		} else {
			$this->session->set_flashdata('message', 'success:' . $this->lang->line('messages_delete_payment_success'));
		}
	}

	function _twocheckout($id = FALSE, $sum = FALSE)
	{
		$option = array("id_vcompanies" => $_SESSION['current_company']);
		$data["core_settings"] = $this->settingModel->find($option);

		if ($_POST) {
			$this->processTwoCheckoutPayment($data, $_POST);
		} else {
			$this->prepareTwoCheckoutView($id, $sum, $data);
		}
	}
	private function processTwoCheckoutPayment($data, $postData)
	{
		$invoice = Invoice::find_by_id($postData['id']);
		$this->load->file(APPPATH . 'helpers/2checkout/Twocheckout.php', true);

		// Configuration 2Checkout
		$this->configureTwoCheckout($data);

		$currency = $this->getCurrency($invoice, $data);

		try {
			$charge = $this->makeTwoCheckoutCharge($invoice, $currency, $postData);
			if ($charge['response']['responseCode'] == 'APPROVED') {
				$this->handleSuccessfulPayment($charge, $invoice, $postData['sum']);
			}
		} catch (Twocheckout_Error $e) {
			$this->handlePaymentError($e, $invoice);
		}

		redirect('invoices/view/' . $postData['id']);
	}

	// Configurer 2Checkout
	private function configureTwoCheckout($data)
	{
		Twocheckout::privateKey($data["core_settings"]->twocheckout_private_key);
		Twocheckout::sellerId($data["core_settings"]->twocheckout_seller_id);
	}

	// Obtenir la devise
	private function getCurrency($invoice, $data)
	{
		$currency = $invoice->currency;
		$currency_codes = getCurrencyCodesForTwocheckout();
		return array_key_exists($currency, $currency_codes) ? $currency : $data["core_settings"]->twocheckout_currency;
	}

	// Effectuer la charge 2Checkout
	private function makeTwoCheckoutCharge($invoice, $currency, $postData)
	{
		return Twocheckout_Charge::auth(array(
			"merchantOrderId" => $invoice->reference,
			"token" => $postData['token'],
			"currency" => $currency,
			"total" => $postData['sum'],
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
	}

	// Gérer le succès du paiement
	private function handleSuccessfulPayment($charge, $invoice, $sum)
	{
		echo "Thanks for your Order!";
		echo "<h3>Return Parameters:</h3>";
		echo "<pre>";
		print_r($charge);
		echo "</pre>";

		$paid_date = date('Y-m-d');
		$payment_reference = $invoice->reference . '00' . (InvoiceHasPayment::count(array('conditions' => 'invoice_id = ' . $invoice->id)) + 1);

		// Créer un enregistrement de paiement
		InvoiceHasPayment::create(array(
			'invoice_id' => $invoice->id,
			'reference' => $payment_reference,
			'amount' => $sum,
			'date' => $paid_date,
			'type' => 'credit_card',
			'notes' => ''
		));

		// Mettre à jour la facture selon le montant
		if ($sum >= $invoice->outstanding) {
			$invoice->update_attributes(array('paid_date' => $paid_date, 'status' => 'Paid'));
		} else {
			$invoice->update_attributes(array('status' => 'PartiallyPaid'));
		}

		$this->session->set_flashdata('message', 'success:' . $this->lang->line('messages_payment_complete'));
		log_message('error', '2Checkout: Payment of ' . $sum . ' for invoice ' . $invoice->reference . ' received!');
	}

	// Gérer l'erreur de paiement
	private function handlePaymentError($e, $invoice)
	{
		$this->session->set_flashdata('message', 'error: Your payment could NOT be processed (i.e., you have not been charged) because the payment system rejected the transaction.');
		log_message('error', '2Checkout: Payment of invoice ' . $invoice->reference . ' failed - ' . $e->getMessage());
	}
	private function prepareTwoCheckoutView($id, $sum, $data)
	{
		$this->viewData['invoices'] = $this->invoice->find_by_id($id);
		$this->viewData['publishable_key'] = $data["core_settings"]->twocheckout_publishable_key;
		$this->viewData['seller_id'] = $data["core_settings"]->twocheckout_seller_id;
		$this->viewData['sum'] = $sum;
		$this->theme_view = 'modal';
		$this->viewData['title'] = $this->lang->line('application_pay_with_credit_card');
		$this->viewData['form_action'] = 'invoices/twocheckout';
		$this->content_view = 'invoices/_2checkout';
	}

	function preview($id = FALSE, $attachment = FALSE)
	{
		$this->load->helper(array('dompdf', 'file'));
		$this->load->library('parser');

		$invoice = $this->getInvoice($id);
		$core_settings = $this->settingModel->find(array("id_vcompanies" => $_SESSION['current_company']));
		$this->loadInvoiceData($invoice, $core_settings);

		// Générer le PDF
		$filename = $this->generatePDF($invoice, $core_settings);
	}
	private function loadInvoiceData($invoice, $core_settings)
	{
		$invoice->status = $this->referentiels->getReferentielsById($invoice->status)->name;

		// Obtenir les éléments de la facture
		$this->db->where('facture_id', $invoice->id);
		$this->db->order_by('position', 'asc');
		$items = $this->db->get('facture_has_items')->result();

		// Mettre à jour les données de la facture
		$company = $this->db->where('id', $invoice->company_id)->get('companies')->row();
		$client = $this->db->where('id', $company->client_id)->get('clients')->row();

		// Récupérer le logo de la société
		$logo = $this->db->where('id', $_SESSION['current_company'])->get('v_companies')->row()->picture;

		// Passer les données à la vue
		return [
			'invoice' => $invoice,
			'items' => $items,
			'company' => $company,
			'client' => $client,
			'logo' => $logo,
			// Autres données nécessaires...
		];
	}

	// Générer le PDF
	private function generatePDF($invoice, $core_settings)
	{
		// Construire les données à passer à la vue
		$parse_data = [
			'invoice_id' => $core_settings->invoice_prefix . $invoice->reference,
			'client_link' => $core_settings->domain,
			'company' => $core_settings->company,
			'client_id' => $invoice->company->reference,
		];

		// Charger la vue et parser les données
		$html = $this->load->view($core_settings->template . '/' . $core_settings->invoice_pdf_template, $data, true);
		$html = $this->parser->parse_string($html, $parse_data);

		$filename = $invoice->estimate_num . '_' . Unaccent($data['company']->name);
		$this->pdf->load_view($html, $filename);

		return $filename;
	}

	// Préparer les données de l'email
	private function prepareEmailData($data, $core_settings)
	{
		return [
			'client_contact' => $data["invoice"]->company->client->firstname . ' ' . $data["invoice"]->company->client->lastname,
			'client_company' => $data["invoice"]->company->name,
			'invoice_id' => $core_settings->invoice_prefix . $data["invoice"]->reference,
			'client_link' => $core_settings->domain,
			'company' => $core_settings->company,
			'logo' => '<img src="' . base_url() . $core_settings->logo . '" alt="' . $core_settings->company . '"/>',
			'invoice_logo' => '<img src="' . base_url() . $core_settings->invoice_logo . '" alt="' . $core_settings->company . '"/>',
		];
	}

	// Générer et envoyer le PDF par email
	private function generateAndEmailPDF($data, $parse_data, $core_settings)
	{
		// Générer le PDF
		$html = $this->load->view($core_settings->template . '/' . $core_settings->invoice_pdf_template, $data, true);
		$html = $this->parser->parse_string($html, $parse_data);

		$filename = $this->lang->line('application_invoice') . '_' . $core_settings->invoice_prefix . $data["invoice"]->reference;
		pdf_create($html, $filename, FALSE);

		// Envoyer l'email
		$this->sendEmailWithAttachment($data, $parse_data, $filename);
	}

	// Envoyer l'email avec pièce jointe
	private function sendEmailWithAttachment($data, $parse_data, $filename)
	{
		$subject = $this->parser->parse_string($data["core_settings"]->invoice_mail_subject, $parse_data);
		$this->email->from($data["core_settings"]->email, $data["core_settings"]->company);

		if (!isset($data["invoice"]->company->client->email)) {
			$this->session->set_flashdata('message', 'error:This client company has no primary contact! Just add a primary contact.');
			redirect('invoices/view/' . $data["invoice"]->id);
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
			log_message('error', 'Invoice #' . $data["core_settings"]->invoice_prefix . $data["invoice"]->reference . ' has been sent to ' . $data["invoice"]->company->client->email);
		} else {
			$this->session->set_flashdata('message', 'error:' . $this->lang->line('messages_send_invoice_error'));
			log_message('error', 'ERROR: Invoice #' . $data["core_settings"]->invoice_prefix . $data["invoice"]->reference . ' has not been sent to ' . $data["invoice"]->company->client->email . '. Please check your server\'s email settings.');
		}

		unlink("files/temp/" . $filename . ".pdf");
		redirect('invoices/view/' . $data["invoice"]->id);
	}

	function sendinvoice($id = FALSE)
	{
		$this->load->helper(['dompdf', 'file']);
		$this->load->library('parser');

		$data["invoice"] = $this->invoice->find($id);
		$data['items'] = $this->invoiceHasItemModel->find('all', ['conditions' => ['invoice_id=?', $id]]);
		$data["core_settings"] = $this->settingModel->find(['id_vcompanies' => $_SESSION['current_company']]);

		// Prepare parse values
		$parse_data = $this->prepareParseData($data);

		// Generate PDF
		$html = $this->load->view($data["core_settings"]->template . '/' . $data["core_settings"]->invoice_pdf_template, $data, true);
		$html = $this->parser->parse_string($html, $parse_data);
		$filename = $this->generateInvoiceFilename($data);
		pdf_create($html, $filename, FALSE);

		// Send email with the invoice
		$this->sendInvoiceEmail($data, $parse_data, $filename);

		// Clean up
		unlink("files/temp/" . $filename . ".pdf");
		redirect('invoices/view/' . $id);
	}

	private function prepareParseData($data)
	{
		return [
			'client_contact' => $data["invoice"]->company->client->firstname . ' ' . $data["invoice"]->company->client->lastname,
			'client_company' => $data["invoice"]->company->name,
			'invoice_id' => $data["core_settings"]->invoice_prefix . $data["invoice"]->reference,
			'client_link' => $data["core_settings"]->domain,
			'company' => $data["core_settings"]->company,
			'logo' => '<img src="' . base_url() . $data["core_settings"]->logo . '" alt="' . $data["core_settings"]->company . '"/>',
			'invoice_logo' => '<img src="' . base_url() . $data["core_settings"]->invoice_logo . '" alt="' . $data["core_settings"]->company . '"/>'
		];
	}

	// Generate the filename for the invoice PDF
	private function generateInvoiceFilename($data)
	{
		return $this->lang->line('application_invoice') . '_' . $data["core_settings"]->invoice_prefix . $data["invoice"]->reference;
	}

	private function sendInvoiceEmail($data, $parse_data, $filename)
	{
		$subject = $this->parser->parse_string($data["core_settings"]->invoice_mail_subject, $parse_data);
		$this->email->from($data["core_settings"]->email, $data["core_settings"]->company);

		if (!isset($data["invoice"]->company->client->email)) {
			$this->session->set_flashdata('message', 'error:This client company has no primary contact! Just add a primary contact.');
			redirect('invoices/view/' . $data["invoice"]->id);
		}

		$this->email->to($data["invoice"]->company->client->email);
		$this->email->subject($subject);
		$this->email->attach("files/temp/" . $filename . ".pdf");

		// Prepare and send the email message
		$email_invoice_template = read_file('./application/views/' . $data["core_settings"]->template . '/templates/email_invoice.html');
		$message = $this->parser->parse_string($email_invoice_template, $parse_data);
		$this->email->message($message);

		// Send the email and handle the response
		if ($this->email->send()) {
			$this->session->set_flashdata('message', 'success:' . $this->lang->line('messages_send_invoice_success'));
			$data["invoice"]->update_attributes(['status' => 'Sent', 'sent_date' => date("Y-m-d")]);
			log_message('error', 'Invoice #' . $data["core_settings"]->invoice_prefix . $data["invoice"]->reference . ' has been sent to ' . $data["invoice"]->company->client->email);
		} else {
			$this->session->set_flashdata('message', 'error:' . $this->lang->line('messages_send_invoice_error'));
			log_message('error', 'ERROR: Invoice #' . $data["core_settings"]->invoice_prefix . $data["invoice"]->reference . ' has not been sent to ' . $data["invoice"]->company->client->email . '. Please check your server\'s email settings.');
		}
	}
	function itemEmpty($id = FALSE)
	{
		$this->load->database();

		if ($_POST) {
			$this->handleItemSubmission($id);
		} else {
			$this->prepareItemForm($id);
		}
	}
	private function handleItemSubmission($id)
	{
		unset($_POST['send']);
		$_POST = array_map('htmlspecialchars', $_POST);
		$item_id = $_POST['item_id'];

		if (!empty($_POST['name'])) {
			$_POST['discount'] = $_POST['discount'] ?? 0;
			$_POST['tva'] = $_POST['tva'];
		} else {
			$this->handleEmptyItemName($item_id);
		}

		$item = $this->db->insert('facture_has_items', $_POST);
		if (!$item) {
			$this->session->set_flashdata('message', 'error:' . $this->lang->line('messages_add_item_error'));
		} else {
			$this->session->set_flashdata('message', 'success:' . $this->lang->line('messages_add_item_success'));
		}
		redirect('invoices/view/' . $_POST['facture_id']);
	}

	// Handle the case where the item name is empty
	private function handleEmptyItemName($item_id)
	{
		if ($item_id == "-") {
			$this->session->set_flashdata('message', 'error:' . $this->lang->line('messages_add_item_error'));
			redirect('invoices/view/' . $_POST['invoice_id']);
		}

		$rebill = explode("_", $item_id);
		if ($rebill[0] == "rebill") {
			$itemvalue = $this->expenseModel->find_by_id($rebill[1]);
			$_POST['name'] = $itemvalue->description;
			$_POST['value'] = $itemvalue->value;
			$itemvalue->rebill = 2;
			$itemvalue->invoice_id = $_POST['facture_id'];
			$itemvalue->save();
		} else {
			$itemvalue = Item::find_by_id($item_id);
			$_POST['name'] = $itemvalue->name;
			$_POST['value'] = $itemvalue->value;
		}
	}

	// Prepare the form for adding a new item
	private function prepareItemForm($id)
	{
		$this->db->where('id', $id);
		$facture = $this->db->get('facture')->result()[0];
		$this->viewData['item_units'] = $this->db->query("SELECT * FROM item_units")->result();
		$this->viewData['type'] = $this->db->query('SELECT libelle, id FROM items_has_family WHERE inactive=0 UNION SELECT libelle, id FROM items_has_family_parent WHERE inactive=0')->result();
		$this->viewData['tva'] = $this->db->query('SELECT * FROM ref_type_occurences WHERE id_type=9 AND visible=1')->result();
		$this->viewData['defaultTva'] = $this->settingModel->find(['id_vcompanies' => $_SESSION['current_company']])->tax;
		$this->viewData['invoice'] = $facture;
		$this->viewData['items'] = Item::find('all', ['conditions' => ['inactive=?', '0']]);
		$this->viewData['rebill'] = $this->expenseModel->find('all', ['conditions' => ['project_id=? AND (rebill=? OR invoice_id=?)', $this->viewData['invoice']->project_id, 1, $id]]);
		$this->theme_view = 'modal';
		$this->viewData['title'] = $this->lang->line('application_add_item');
		$this->viewData['form_action'] = 'invoices/itemEmpty';
		$this->content_view = 'invoices/_itemEmpty';
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
						$itemvalue = $this->expenseModel->find_by_id($rebill[1]);
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
			$this->viewData['families'] = $this->convert($families);
			$this->db->where('id', $id);
			$facture = $this->db->get('facture')->result()[0];
			$company = Company::find_by_id($facture->company_id);
			$this->viewData['company'] = $company;
			$this->viewData['item_units'] = $this->db->query("SELECT * FROM item_units")->result();
			$type = $this->db->query('select libelle,id from items_has_family where inactive=0 UNION select libelle,id from items_has_family_parent where inactive=0');
			$this->viewData['type'] = $type->result();

			$this->viewData['invoice'] = $facture;
			$idType = $this->refType->getRefTypeByName($facture->currency)->id;
			$this->viewData['chiffre'] = $this->referentiels->getReferentielsByIdType($idType)->name;
			$this->viewData['items'] = Item::find('all', array('conditions' => array('inactive=?', '0')));
			//Créer la liste des articles
			$list_items = array();
			$list_items['0'] = '-';
			foreach ($this->viewData['items'] as $value):
				$list_items[$value->id] = $value->name . " - " . $value->value . " " . $core_settings->currency;
			endforeach;
			$this->viewData['list_items'] = $list_items;
			$this->viewData['title'] = $this->lang->line('application_add_item');
			$this->viewData['tva'] = $this->referentiels->getReferentielsByIdType($this->config->item("type_id_tva"));
			$this->theme_view = 'modal';
			$this->viewData['form_action'] = 'invoices/item';
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
			$this->viewData['invoice_has_items'] = $item;
			$type = $this->db->query('select libelle,id from items_has_family where inactive=0 UNION select libelle,id from items_has_family_parent where inactive=0');
			$this->viewData['type'] = $type->result();
			$this->theme_view = 'modal';
			$this->viewData['title'] = $this->lang->line('application_edit_item');
			$item_units = $this->db->query("SELECT * FROM item_units")->result();
			$this->viewData['item_units'] = $item_units;
			$this->viewData['form_action'] = 'invoices/item_update';
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
			$this->viewData['invoice_has_items'] = $item;
			$type = $this->db->query('select libelle,id from items_has_family where inactive=0 UNION select libelle,id from items_has_family_parent where inactive=0');
			$this->viewData['type'] = $type->result();
			$this->theme_view = 'modal';
			$this->viewData['title'] = $this->lang->line('application_edit_item');
			$item_units = $this->db->query("SELECT * FROM item_units")->result();
			$this->viewData['item_units'] = $item_units;
			$this->viewData['form_action'] = 'invoices/item_update_empty';
			$this->viewData['tva'] = $this->db->query('select * from ref_type_occurences where id_type=9 and visible=1')->result();
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
		$settings = $this->settingModel->find($option);
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
		$settings = $this->settingModel->first();
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
		$this->viewData['lastUrl'] = 'facture';
		$this->viewData['settings'] = $settings;
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
			$data["core_settings"] = $this->settingModel->find($option);
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
			$this->viewData['form_action'] = 'invoices/sendfiles';
			$this->viewData['data'] = $this->invoice->getById($id)[0];
			$this->viewData['type'] = "facture";
			$this->content_view = 'settings/sendFile';
		}
	}
}
