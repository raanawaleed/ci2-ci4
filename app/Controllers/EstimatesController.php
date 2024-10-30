<?php

namespace App\Controllers;

use App\Models\ItemModel;
use App\Models\ProjectsModel;
use App\Models\FactureModel;
use App\Models\RefTypeModel;
use App\Models\InvoiceHasItemModel;
use App\Models\CompanyModel;
use App\Models\EstimateModel;
use App\Models\InvoiceModel;
use App\Models\ItemsModel;
use App\Models\ProjectModel;
use App\Models\SalarieModel;
use App\Models\RefTypeOccurencesModel;
use App\Models\SettingModel;
use CodeIgniter\Controller;
use DateTime;

class EstimatesController extends Controller
{

	private $idTypeRefDevis, $itemModel, $projectModel, $factureModel, $referentielsModel, $refTypeModel,
		$invoiceHasItemModel, $companyModel, $estimateModel, $salarieModel, $db,
		$settingModel, $theme_view, $invoiceModel, $parser, $pdf;
	private $submenu = [];
	protected $view_data = [];

	public function __construct()
	{
		// Check user authentication
		if (!session()->get('client') && !session()->get('user')) {
			return redirect()->to('login');
		}

		// Load models using dependency injection
		$this->itemModel = new ItemsModel();
		$this->projectModel = new ProjectModel();
		$this->factureModel = new FactureModel();
		$this->referentielsModel = new RefTypeOccurencesModel();
		$this->refTypeModel = new RefTypeModel();
		$this->invoiceHasItemModel = new InvoiceHasItemModel();
		$this->companyModel = new CompanyModel();
		$this->estimateModel = new EstimateModel();
		$this->salarieModel = new SalarieModel();
		$this->settingModel = new SettingModel();
		$this->invoiceModel = new InvoiceModel();

		helper(['form', 'file']);
		$this->parser = \Config\Services::parser();
		$this->pdf = \Config\Services::pdf();
		$this->loadDatabase();

		// Load helper
		helper('calcul_helper');

		// Load configuration
		$this->idTypeRefDevis = config('App');

		// Set up submenus
		$this->setupSubmenus();
	}
	private function loadDatabase()
	{
		// Assuming database is already set in the configuration
		$this->db = \Config\Database::connect();
	}

	private function setupSubmenus()
	{
		$submenus = $this->referentielsModel->getReferentielsByIdType($this->idTypeRefDevis);

		$this->submenu[lang('application.application_all')] = 'estimates';

		foreach ($submenus as $submenu) {
			if ($submenu->name !== "Sent" && $submenu->name !== "Pending") {
				$langName = lang('application.' . $submenu->name);
				$this->submenu[$langName ?: $submenu->name] = 'estimates/filter' . $submenu->name;
			}
		}

		// Assign submenu to view_data
		$this->view_data['submenu'] = $this->submenu;
	}

	// Afficher la liste des devis
	public function index()
	{
		// Get request parameters with validation
		$document = $this->request->getGet('document');
		$department = $this->request->getGet('department');

		// Load estimates with conditions
		$this->view_data['estimates'] = $this->estimateModel->findAll();

		// Fetch invoices based on department
		switch ($department) {
			case 'mms':
				$invoices = $this->estimateModel->getMms();
				break;
			case 'bim2d':
				$invoices = $this->estimateModel->getBim2d();
				break;
			case 'bim3d':
				$invoices = $this->estimateModel->getBim3d();
				break;
			default:
				$invoices = [];
				break;
		}

		// Fetch invoices based on document type
		switch ($document) {
			case 'devis':
				$invoices = $this->estimateModel->getDevisDocument();
				break;
			case 'attachment':
				$invoices = $this->estimateModel->getAttDocument();
				break;
			default:
				break;
		}

		// Initialize estimates data
		$this->view_data['estimates'] = [
			'document' => $document,
			'department' => $department,
			'invoices' => $invoices
		];

		// Fetch currency types and enrich invoices
		foreach ($invoices as $invoice) {
			$currencyRefType = $this->refTypeModel->getRefTypeByName($invoice->currency);
			if ($currencyRefType) {
				$currencyName = $this->referentielsModel->getReferentielsByIdType($currencyRefType->id);
				$invoice->currency = $currencyName[0]->name ?? $invoice->currency; // Set currency name or fallback
			}
		}

		// Fetch due and paid estimates for this week
		$now = time();
		$beginningOfWeek = strtotime('last Monday', $now);
		$endOfWeek = strtotime('next Sunday', $now) + 86400;

		// Using Query Builder for safer queries
		$this->view_data['estimates_due_this_month_graph'] = $this->db->table('invoices')
			->select('count(id) AS amount, DATE_FORMAT(due_date, "%w") AS date_day, DATE_FORMAT(due_date, "%Y-%m-%d") AS date_formatted')
			->where('UNIX_TIMESTAMP(due_date) >=', $beginningOfWeek)
			->where('UNIX_TIMESTAMP(due_date) <=', $endOfWeek)
			->where('estimate !=', 0)
			->get()
			->getResult();

		$this->view_data['estimates_paid_this_month_graph'] = $this->db->table('invoices')
			->select('count(id) AS amount, DATE_FORMAT(paid_date, "%w") AS date_day, DATE_FORMAT(paid_date, "%Y-%m-%d") AS date_formatted')
			->where('UNIX_TIMESTAMP(paid_date) >=', $beginningOfWeek)
			->where('UNIX_TIMESTAMP(paid_date) <=', $endOfWeek)
			->where('estimate !=', 0)
			->get()
			->getResult();

		view('estimates/all');
	}


	//filtrer les devis
	function filter($condition = FALSE, $year = FALSE)
	{
		if ($condition == "False") {
			$estimates = $this->db->get('invoices')->result();
			foreach ($estimates as $key => $estimate) {
				$date = DateTime::createFromFormat("Y-m-d", $estimate->issue_date);
				$date = $date->format("Y");
				if ($date != $year) {
					unset($estimates[$key]);
				}
			}
		} else {
			$condition = urldecode($condition);
			//get the id of status 
			$idType = $this->refTypeModel->getRefTypeByName('Devis')->id;
			$idState = $this->referentielsModel->getReferentiels($idType, $condition)->id;
			$this->db->where("status =" . $idState);
			$estimates = $this->db->get('invoices')->result();
		}
		foreach ($estimates as $estimate) {

			$idType = $this->refTypeModel->getRefTypeByName($estimate->currency)->id;
			$chiffre = $this->referentielsModel->getReferentielsByIdType($idType);
			//$chiffre->description = $estimate->currency;
			//chiffre de devise 
			$estimate->currency = $chiffre;
			$estimate->company = $this->companyModel->find($estimate->company_id);
		}
		$this->view_data['estimates'] = $estimates;
		$opt = array("id_vcompanies" => $_SESSION['current_company']);
		$this->view_data['settings'] = $this->settingModel->find($opt);
		view('estimates/all');
	}

	// Créer un devis
	public function create()
	{
		if ($this->request->getMethod() === 'post') {
			$estimateReference = $this->settingModel->first();
			$currency = $_POST['currency'] ?? '';
			$refTypeId = $this->refTypeModel->getRefTypeByName($currency)->id ?? null;

			// Ensure the reference type was found
			if ($refTypeId) {
				$chiffre = $this->referentielsModel->find($refTypeId)->name;

				// Add client if it is a passenger company
				if (!empty($_POST['company']) && $_POST['company'] == 1) {
					$company = $this->companyModel->last();
					$ref = $company->reference ?? 0; // Fallback if last company reference is not found

					$companyData = [
						'name' => $_POST['nomClient'],
						'reference' => $ref + 1,
						'passager' => '1',
						'tva' => $_POST['tva'],
						'guarantee' => $_POST['guarantee']
					];
					$this->db->table('companies')->insert($companyData);
					$_POST['company_id'] = $this->companyModel->last()->id;

					// Increment company reference
					$newCompanyReference = $estimateReference->company_reference + 1;
					$estimateReference->update(['company_reference' => $newCompanyReference]);
					unset($_POST['company']);
				}

				// Project reference in devis
				$_POST['calcul_heure'] = (float) str_replace(".5", ".3", $this->projectModel->calculeheure($_POST['project_id'])->periode);
				$_POST['project_surface'] = (float) $this->projectModel->calculquantite($_POST['project_id'])->quantite;
				$_POST['delivery'] = $this->projectModel->getProjectRef($_POST['project_id'])->delivery;
				$_POST['chef_projet_client'] = $this->projectModel->getProjectClientName($_POST['project_id'])->name_client;
				$_POST['chef_projet'] = $this->projectModel->getProjectName($_POST['project_id'])->name_bim;
				$_POST['project_name'] = $this->projectModel->getProjectRef($_POST['project_id'])->name;
				$_POST['project_ref'] = $this->projectModel->getProjectRef($_POST['project_id'])->project_num;

				// Handle project creation if name is provided
				if (!empty($_POST['name'])) {
					$lastProject = $this->projectModel->last();
					$_POST['project_id'] = $lastProject->reference + 1;

					// Generate project number based on the date and estimate prefix
					$projectPieces = explode("-", strrev($estimateReference->project_prefix));
					$startDate = date("Y-m-d", strtotime($_POST['start']));
					$pieces = explode("-", $startDate);
					$piecesYear = $pieces[0];
					$piecesMonth = $pieces[1];
					$subpiecesYear = explode('0', $piecesYear);
					$_POST['project_id'] = str_pad($_POST['project_id'], 2, '0', STR_PAD_LEFT); // Pad project ID with zeros

					// Generate project reference number based on the prefix configuration
					$numero = match ($projectPieces[0]) {
						'YY' => strrev($projectPieces[1]) . $subpiecesYear[1] . $_POST['project_id'],
						default => strrev($projectPieces[2]) . $subpiecesYear[1] . $piecesMonth . $_POST['project_id']
					};

					$projectData = [
						'name' => $_POST['name'],
						'reference' => $lastProject->reference + 1,
						'datetime' => time(),
						'progress' => 0,
						'start' => $_POST['start'],
						'end' => $_POST['end'],
						'project_num' => $numero,
						'company_id' => $_POST['company_id']
					];
					$this->db->table('projects')->insert($projectData);

					// Increment project reference in core
					$newProjectReference = $estimateReference->project_reference + 1;
					$estimateReference->update(['project_reference' => $newProjectReference]);
					$_POST['project_ref'] = $numero;
				}

				// Clean up POST data
				$this->cleanPostData();

				// Set common estimate data
				$_POST['estimate'] = 1;
				$_POST['creation_date'] = date("Y-m-d");
				$idType = $this->refTypeModel->getRefTypeByName('Devis')->id;

				// Handle estimate reference
				$year = date("Y");
				$lastEstimate = new InvoiceModel();
				$lastEstimate->last();
				$lastRef = explode('-', (date_format($lastEstimate->creation_date, 'Y-m-d')));
				$_POST['estimate_reference'] = ($lastRef[0] != $year) ? '01' : $_POST['reference'];

				// Generate estimate number
				$estimatePieces = explode("-", strrev($estimateReference->estimate_prefix));
				$var = date("y-m-d", strtotime($_POST['issue_date']));
				$pieces = explode("-", $var);
				$piecesYear = $pieces[0];
				$piecesMonth = $pieces[1];
				$subpiecesYear = explode(' ', $piecesYear);
				$_POST['estimate_num'] = match ($estimatePieces[0]) {
					'YY' => strrev($estimatePieces[1]) . $subpiecesYear[0] . $_POST['estimate_reference'],
					default => strrev($estimatePieces[2]) . $subpiecesYear[0] . $piecesMonth . $_POST['estimate_reference']
				};

				// Create estimate
				$estimate = $lastEstimate->create($_POST);
				$newEstimateReference = $_POST['estimate_reference'] + 1;
				$estimateReference->update(['estimate_reference' => $newEstimateReference]);

				// Set flash message based on success
				$messageKey = $estimate ? 'success' : 'error';
				session()->setFlashdata('message', "$messageKey:" . lang('messages_create_estimate_' . $messageKey));

				return redirect()->to('estimates');
			} else {
				$this->setViewData();
				return view('estimates/_estimate', $this->view_data);
			}
		}
	}
	private function cleanPostData()
	{
		unset($_POST['name'], $_POST['start'], $_POST['end'], $_POST['nomClient'], $_POST['tva'], $_POST['guarantee'], $_POST['send'], $_POST['_wysihtml5_mode'], $_POST['files']);
	}

	private function setViewData()
	{
		$this->view_data['estimates'] = $this->invoiceModel->all();
		$this->view_data['projects'] = $this->projectModel->all();
		$this->view_data['companies'] = $this->companyModel->findAll();
		$this->view_data['next_reference'] = $this->invoiceModel->last();
		$idType = $this->refTypeModel->getRefTypeByName("Devise")->id;
		$this->view_data['currencys'] = $this->referentielsModel->getReferentielsByIdType($idType);
		$option = ['id_vcompanies' => $_SESSION['current_company']];
		$settings = $this->settingModel->find($option);
		$this->view_data['current_echeance'] = date('Y-m-d', strtotime($settings->echeance . " days"));
		$this->view_data['current_date'] = date('Y-m-d');

		// Handle reference reset
		$year = date("Y");
		$lastEstimate = $this->invoiceModel->last();
		$lastRef = explode('-', (date_format($lastEstimate->creation_date, 'Y-m-d')));
		if ($lastRef[0] != $year) {
			$settings->estimate_reference = 1;
		}
		$this->view_data['core_settings'] = $settings;
		$this->theme_view = 'modal';
		$this->view_data['title'] = lang('application_create_estimate');
		$this->view_data['form_action'] = 'estimates/create';
	}



	public function createb()
	{
		if ($this->request->getMethod() === 'post') {
			$validation = \Config\Services::validation();
			$validation->setRules([
				'nomClient' => 'required',
				'tva' => 'required|numeric',
				'guarantee' => 'required',
				'company' => 'required|in_list[0,1]',
				'name' => 'required',
				'start' => 'required|valid_date',
				'end' => 'required|valid_date',
				'issue_date' => 'required|valid_date',
			]);

			if (!$validation->withRequest($this->request)->run()) {
				return redirect()->back()->withInput()->with('errors', $validation->getErrors());
			}

			$estimateReference = $this->settingModel->first();
			$currencyId = $this->referentielsModel->getRefTypeByName($this->request->getPost('currency'))->id;
			// $chiffre = $this->referentielsModel->getReferentielsByIdType($currencyId)->name;

			// Add client if applicable
			if ($this->request->getPost('company') == 1) {
				$company = $this->companyModel->orderBy('id', 'desc')->first();
				$data = [
					'name' => $this->request->getPost('nomClient'),
					'reference' => $company->reference + 1,
					'passager' => '1',
					'tva' => $this->request->getPost('tva'),
					'guarantee' => $this->request->getPost('guarantee')
				];
				$this->companyModel->insert($data);
				$this->request->getPost('company_id', $this->companyModel->insertID());

				// Update company reference
				$estimateReference->company_reference++;
				$this->settingModel->save($estimateReference);
			}

			// Update project details
			$projectData = $this->projectModel->find($this->request->getPost('project_id'));
			if ($projectData) {
				$this->request->getPost('calcul_heure', (float)$projectData->periode);
				$this->request->getPost('project_surface', (float)$projectData->quantite);
				$this->request->getPost('delivery', $projectData->delivery);
				$this->request->getPost('chef_projet_client', $projectData->name_client);
				$this->request->getPost('chef_projet', $projectData->name_bim);
				$this->request->getPost('project_name', $projectData->name);
				$this->request->getPost('project_ref', $projectData->project_num);
			}

			// Create project if applicable
			if ($this->request->getPost('name') != '') {
				$lastProject = $this->projectModel->orderBy('id', 'desc')->first();
				$this->request->getPost('project_id', $lastProject->reference + 1);

				$projectNum = $this->generateProjectNumber($estimateReference->project_prefix, $this->request->getPost('start'), $this->request->getPost('project_id'));

				$projectData = [
					'name' => $this->request->getPost('name'),
					'reference' => $lastProject->reference + 1,
					'datetime' => time(),
					'progress' => 0,
					'start' => $this->request->getPost('start'),
					'end' => $this->request->getPost('end'),
					'project_num' => $projectNum,
					'company_id' => $this->request->getPost('company_id')
				];
				$this->projectModel->insert($projectData);

				// Increment project reference
				$estimateReference->project_reference++;
				$this->settingModel->save($estimateReference);
				$this->request->getPost('project_ref', $projectNum);
			}

			$this->prepareEstimateData($estimateReference);

			// Create estimate
			$estimate = $this->invoiceModel->create($this->request->getPost());
			if (!$estimate) {
				return redirect()->back()->with('message', 'error: Failed to create estimate');
			} else {
				return redirect()->to('estimates')->with('message', 'success: Estimate created successfully');
			}
		}

		return $this->loadCreateView();
	}

	private function generateProjectNumber(string $projectPrefix, string $startDate, int $projectId): string
	{
		// Logic to generate project number based on prefix and date
		$projectPieces = explode("-", strrev($projectPrefix));
		$datePieces = explode("-", date("Y-m-d", strtotime($startDate)));
		$year = $datePieces[0];
		$month = $datePieces[1];

		$projectIdStr = str_pad($projectId, 2, '0', STR_PAD_LEFT); // pad to 2 digits

		// Construct the project number
		return $projectPieces[0] == 'YY'
			? strrev($projectPieces[1]) . substr($year, 2) . $projectIdStr
			: strrev($projectPieces[2]) . substr($year, 2) . $month . $projectIdStr;
	}

	private function prepareEstimateData(SettingModel $estimateReference): void
	{
		$year = date("Y");
		$lastEstimate = $this->invoiceModel->orderBy('id', 'desc')->first();
		$lastRefYear = explode('-', date_format($lastEstimate->creation_date, 'Y-m-d'))[0];

		if ($lastRefYear !== $year) {
			$this->request->getPost('estimate_reference', '01');
		} else {
			$this->request->getPost('estimate_reference', $estimateReference->estimate_reference);
		}

		$estimatePrefix = explode("-", strrev($estimateReference->estimate_prefix));
		$issueDate = date("y-m-d", strtotime($this->request->getPost('issue_date')));
		$datePieces = explode("-", $issueDate);

		$estimateNum = $estimatePrefix[0] == "YY"
			? strrev($estimatePrefix[1]) . substr($datePieces[0], 0, 1) . $this->request->getPost('estimate_reference')
			: strrev($estimatePrefix[2]) . substr($datePieces[0], 0, 1) . $datePieces[1] . $this->request->getPost('estimate_reference');

		request()->getPost('estimate_num', $estimateNum);
	}

	private function loadCreateView()
	{
		$this->view_data['estimates'] = $this->invoiceModel->findAll();
		$this->view_data['projects'] = $this->projectModel->findAll();
		$this->view_data['companies'] = $this->companyModel->where('inactive', 0)->findAll();
		$this->view_data['next_reference'] = $this->invoiceModel->orderBy('id', 'desc')->first();
		$idType = $this->referentielsModel->getRefTypeByName("Devise")->id;
		$this->view_data['currencys'] = $this->referentielsModel->getReferentielsByIdType($idType);
		$settings = $this->settingModel->where('id_vcompanies', $_SESSION['current_company'])->first();
		$this->view_data['current_echeance'] = date('Y-m-d', strtotime("+{$settings->echeance} days"));
		$this->view_data['state'] = $this->referentielsModel->getReferentielsByIdType($this->idTypeRefDevis);
		$this->view_data['current_date'] = date('Y-m-d');

		// Reset estimate reference if new year
		if (date('Y-m-d') === '01-01') {
			$settings->estimate_reference = 1;
			$this->settingModel->save($settings);
		}

		return view('estimates/create', $this->view_data);
	}



	// Mettre à jour un devis
	// Mettre à jour un devis

	public function update($id = null)
	{
		if ($this->request->getMethod() === 'post') {
			$postData = $this->request->getPost();
			$estimateReference = $this->settingModel->first();
			$currencyType = $this->referentielsModel->getReferentielsByName($postData['currency']);
			$chiffre = $currencyType->name;

			$estimate = $this->invoiceModel->find($id);

			// Check if the company exists
			if ($estimate->company_id != 0) {
				$company = $this->companyModel->find($estimate->company_id);
			}

			// Handle passenger client
			if ($postData['company'] == 1) {
				if ($company->passager != 1) {
					$newCompany = [
						'name' => $postData['nomClient'],
						'reference' => $this->companyModel->getLastReference() + 1,
						'passager' => 1,
						'tva' => $postData['tva'],
						'guarantee' => $postData['guarantee']
					];
					$this->companyModel->save($newCompany);
					$postData['company_id'] = $this->companyModel->getLastInsertID();

					// Update company reference
					$this->settingModel->update($estimateReference->id, ['company_reference' => $estimateReference->company_reference + 1]);
				} else {
					$companyData = [
						'name' => $postData['nomClient'],
						'tva' => $postData['tva'],
						'guarantee' => $postData['guarantee']
					];
					$this->companyModel->update($company->id, $companyData);
				}
				unset($postData['company']);
			}

			// Handle project reference
			if ($postData['project_id'] != 0) {
				$projectRef = $this->projectModel->find($postData['project_id']);
				$postData['project_surface'] = $projectRef->surface;
				$postData['project_name'] = $projectRef->name;
				$postData['project_ref'] = $projectRef->project_num;
			}

			// Handle new project creation
			if (!empty($postData['name'])) {
				$lastProjectRef = $this->projectModel->getLastReference();
				$newProjectData = [
					'name' => $postData['name'],
					'id_vcompanies' => session()->get('current_company'),
					'reference' => $lastProjectRef + 1,
					'datetime' => time(),
					'progress' => 0,
					'start' => $postData['start'],
					'end' => $postData['end'],
					'company_id' => $postData['company_id'],
				];

				// Handle project number
				$newProjectData['project_num'] = $this->generateProjectNum($postData['start'], $lastProjectRef);

				$this->projectModel->save($newProjectData);
				$postData['project_id'] = $this->projectModel->getLastInsertID();
			}

			// Clean up the POST data for the invoice
			unset($postData['name'], $postData['reference'], $postData['start'], $postData['end'], $postData['nomClient'], $postData['tva'], $postData['guarantee']);

			// Update invoice
			$this->invoiceModel->update($id, $postData);
			session()->setFlashdata('message', 'Invoice updated successfully.');

			return redirect()->to('/estimates');
		}

		// Load the estimate data to edit
		$data['estimate'] = $this->invoiceModel->find($id);
		$data['companies'] = $this->companyModel->findAll();
		$data['projects'] = $this->projectModel->findAll();
		$data['currencies'] = $this->referentielsModel->getAllCurrencies();
		$data['title'] = 'Edit Estimate';

		return view('estimates/edit', $data);
	}

	private function generateProjectNum($startDate, $lastProjectRef)
	{
		// Generate project number logic here
		$dateTime = new \DateTime($startDate);
		$year = $dateTime->format('Y');
		// Modify the logic below according to your needs
		return 'PROJ-' . $year . '-' . ($lastProjectRef + 1);
	}



	// Afficher le détail d'un devis
	public function view($id = null)
	{
		$data = [
			'submenu' => [
				lang('application_back') => 'estimates',
			],
			'estimate' => $this->invoiceModel->find($id),
		];

		// Check if the estimate exists and get project details
		if ($data['estimate'] && $data['estimate']->project_id != 0) {
			$data['project'] = $this->projectModel->find($data['estimate']->project_id);
		}

		// Get currency and other details
		$refType = $this->settingModel->getRefTypeByName($data['estimate']->currency)->id;
		$data['chiffre'] = $this->settingModel->getReferentielsByIdType($refType)[0]->name;
		$data['company'] = $this->companyModel->find($data['estimate']->company_id);

		// Retrieve core settings
		$option = ["id_vcompanies" => session()->get('current_company')];
		$data['core_settings'] = $this->settingModel->find($option);

		// Retrieve estimate items
		$data['items'] = $this->invoiceModel->getInvoiceItems($id);

		// Calculate sums and totals
		$totals = $this->calculateTotals($data['items'], $data['company']->tva, $data['estimate']->discount);
		$data['sumht'] = $totals['sumht'];
		$data['sum'] = $totals['sum'];

		// Update the estimate
		$this->invoiceModel->update($id, [
			'sumht' => $data['sumht'],
			'sum' => $data['sum']
		]);

		// Get contact details
		$contact_id = $data['company']->client_id;
		$data['contact_principale'] = $this->companyModel->getClientById($contact_id);

		return view('estimates/view', $data);
	}

	public function estimateToInvoice($id = null)
	{
		$settings = $this->settingModel->find(["id_vcompanies" => session()->get('current_company')]);
		$estimate = $this->invoiceModel->find($id);
		if (!$estimate) {
			return redirect()->to('/invoices')->with('message', 'error:' . lang('messages_invoice_not_found'));
		}

		$devis = $estimate;
		unset($devis->id, $devis->id_facture);
		$devis->estimate = 0;
		$devis->estimate_reference = $settings->invoice_reference + 1;
		$devis->reference = $settings->invoice_reference + 1;

		// Increment the invoice reference
		$newInvoiceReference = $settings->invoice_reference + 2;
		$this->settingModel->update(["id_vcompanies" => session()->get('current_company')], ['invoice_reference' => $newInvoiceReference]);

		// Create new invoice
		$devis->id_facture = $this->invoiceModel->getNextInvoiceId();
		$devis->status = config('App\Config');
		$devis->estimate_accepted_date = date('Y-m-d');
		$devis->timbre_fiscal = $settings->timbre_fiscal;

		// Insert into facture
		$factured = $this->invoiceModel->insertFacture($devis);

		// Copy items to facture_has_items
		if ($factured) {
			$items = $this->invoiceModel->getInvoiceItems($id);
			foreach ($items as $item) {
				$itemData = (array)$item;
				unset($itemData['id'], $itemData['invoice_id']);
				$itemData['facture_id'] = $devis->id_facture;
				$itemData['name'] = $devis->id_facture; // or any relevant name logic
				$this->invoiceModel->insertInvoiceItem($itemData);
			}
			return redirect()->to('invoices/view/' . $devis->id_facture)->with('message', 'success:' . lang('messages_invoice_created_success'));
		}

		return redirect()->to('invoices')->with('message', 'error:' . lang('messages_invoice_creation_failed'));
	}

	public function delete($id = null)
	{
		$estimate = $this->invoiceModel->find($id);
		if ($estimate) {
			$this->invoiceModel->delete($id);
			return redirect()->to('estimates')->with('message', 'success:' . lang('messages_delete_estimate_success'));
		}

		return redirect()->to('estimates')->with('message', 'error:' . lang('messages_delete_estimate_error'));
	}

	private function calculateTotals($items, $isTvaExempt, $discount)
	{
		$total = 0;
		$totalTVA = 0;

		foreach ($items as $value) {
			$SousTotal = ($value->amount * $value->value) - ($value->amount * $value->value * $value->discount) / 100;
			$SousTotalTVA = $SousTotal + ($SousTotal * $value->tva) / 100;
			$totalTVA += $SousTotalTVA;
			$total += $SousTotal;
		}

		$sum = $isTvaExempt ? $total : $totalTVA;
		$sumht = $total;

		// Apply discount
		$sum -= ($sum / 100) * $discount;
		$sumht -= ($sumht / 100) * $discount;

		// Calculate retention guarantee if applicable
		if ($isTvaExempt) {
			$sum -= ($sum * 10) / 100;
		}

		return ['sum' => $sum, 'sumht' => $sumht];
	}

	public function previewe($id = null)
	{
		$data["estimate"] = $this->invoiceModel->find($id);

		$data['items'] = $this->invoiceModel->getItems($id);
		$data['countDiscount'] = $this->invoiceModel->getDiscountCount($id);
		$data['vcompanies'] = $this->companyModel->find(session()->get('current_company'));
		$data["core_settings"] = $this->settingModel->first();

		if ($data["estimate"]->project_id != 0) {
			$data['num_project'] = $this->projectModel->find($data["estimate"]->project_id)->id;
		}

		$data['logo'] = $this->companyModel->getLogo(session()->get('current_company'));
		$data['company'] = $this->companyModel->find($data['estimate']->company_id);
		$due_date = date($data["core_settings"]->date_format, human_to_unix($data["estimate"]->due_date . ' 00:00:00'));

		$ref = $this->refTypeModel->getByName($data["estimate"]->currency)->id;
		$data['chiffre'] = $this->referentielsModel->getByIdType($ref)[0]->name;

		$parse_data = [
			'due_date' => $due_date,
			'estimate_id' => $data["core_settings"]->estimate_prefix . $data["estimate"]->estimate_reference,
			'client_link' => $data["core_settings"]->domain,
			'company' => $data["core_settings"]->company,
		];

		$cc = $data['estimate']->project_id;
		// $refP = $this->estimateModel->getByRef($cc)->refp;
		// $data['refP'] = $refP;

		$totalg = (float)$this->projectModel->calculQuantiteLg($cc)->quantitelg;
		$data['totalg'] = $totalg;

		$html = view($data["core_settings"]->template . '/templates/estimate/bluelinee', $data);
		$html = $this->parser->parse($html, $parse_data);
		$filename = $data['estimate']->project_name . '_ATT(' . $data['estimate']->project_ref . ')';

		// Generate PDF
		$this->pdf->load_view($html, $filename);
	}

	public function previewb($id = null)
	{
		$data["estimate"] = $this->invoiceModel->find($id);

		$data['items'] = $this->invoiceModel->getItems($id);
		$data['countDiscount'] = $this->invoiceModel->getDiscountCount($id);
		$data['vcompanies'] = $this->companyModel->find(session()->get('current_company'));
		$data["core_settings"] = $this->settingModel->first();

		if ($data["estimate"]->project_id != 0) {
			$data['num_project'] = $this->projectModel->find($data["estimate"]->project_id)->id;
		}

		$data['logo'] = $this->companyModel->getLogo(session()->get('current_company'));
		$data['company'] = $this->companyModel->find($data['estimate']->company_id);
		$due_date = date($data["core_settings"]->date_format, human_to_unix($data["estimate"]->due_date . ' 00:00:00'));

		$ref = $this->refTypeModel->getByName($data["estimate"]->currency)->id;
		$data['chiffre'] = $this->referentielModel->getByIdType($ref)[0]->name;

		$parse_data = [
			'due_date' => $due_date,
			'estimate_id' => $data["core_settings"]->estimate_prefix . $data["estimate"]->estimate_reference,
			'client_link' => $data["core_settings"]->domain,
			'company' => $data["core_settings"]->company,
		];

		$cc = $data['estimate']->project_id;
		$refP = $this->estimateModel->getByRef($cc)->refp;
		$data['refP'] = $refP;

		$totalg = (float)$this->projectModel->calculQuantiteLg($cc)->quantitelg;
		$data['totalg'] = $totalg;

		$data['tickets'] = $this->invoiceModel->getTickets($cc);
		foreach ($data['tickets'] as $ticket) {
			$ticket->total = $this->projectModel->calculeHeureTicket($ticket->id)->periodeticket;
		}

		$html = $this->load->view($data["core_settings"]->template . '/templates/estimate/bluelineeB', $data, true);
		$html = $this->parser->parse($html, $parse_data);
		$filename = $data['estimate']->project_name . '_ATT(' . $data['estimate']->project_ref . ')';

		// Generate PDF
		$this->pdf->load_view($html, $filename);
	}

	public function preview($id = null)
	{
		$data["estimate"] = $this->invoiceModel->find($id);

		$data['items'] = $this->invoiceModel->getItems($id);
		$data['countDiscount'] = $this->invoiceModel->getDiscountCount($id);
		$data['vcompanies'] = $this->companyModel->find(session()->get('current_company'));
		$data["core_settings"] = $this->settingModel->first();

		if ($data["estimate"]->project_id != 0) {
			$data['num_project'] = $this->projectModel->find($data["estimate"]->project_id)->id;
		}

		$data['logo'] = $this->companyModel->getLogo(session()->get('current_company'));
		$data['company'] = $this->companyModel->find($data['estimate']->company_id);
		$due_date = date($data["core_settings"]->date_format, human_to_unix($data["estimate"]->due_date . ' 00:00:00'));

		$ref = $this->refTypeModel->getByName($data["estimate"]->currency)->id;
		$data['chiffre'] = $this->referentielModel->getByIdType($ref)[0]->name;

		$parse_data = [
			'due_date' => $due_date,
			'estimate_id' => $data["core_settings"]->estimate_prefix . $data["estimate"]->estimate_reference,
			'client_link' => $data["core_settings"]->domain,
			'company' => $data["core_settings"]->company,
		];

		$html = $this->load->view($data["core_settings"]->template . '/' . $data["core_settings"]->estimate_pdf_template . $data["core_settings"]->default_template, $data, true);
		$html = $this->parser->parse($html, $parse_data);
		$filename = $data['estimate']->estimate_num . '_' . $data['company']->name;

		// Generate PDF
		$this->pdf->load_view($html, $filename);
	}

	public function sendEstimate($id = false)
	{
		helper(['dompdf', 'file']);

		$data['estimate'] = $this->invoiceModel->find($id);

		// Check if client contact has permissions for estimates and grant if not
		if (isset($data['estimate']->company->client->id)) {
			$access = explode(',', $data['estimate']->company->client->access);
			if (!in_array('107', $access)) {
				$client_estimate_permission = $this->clientModel->find($data['estimate']->company->client->id);
				if ($client_estimate_permission) {
					$client_estimate_permission->access .= ',107';
					$this->clientModel->save($client_estimate_permission);
				}
			}
		}

		$data['estimate']->estimate_sent = date('Y-m-d');
		$this->settingModel = new SettingModel();
		$data['core_settings'] = $this->settingModel->first();
		$due_date = date($data['core_settings']->date_format, strtotime($data['estimate']->due_date . ' 00:00:00'));

		// Set parse values
		$parse_data = [
			'client_contact' => $data['estimate']->company->client->firstname . ' ' . $data['estimate']->company->client->lastname,
			'client_company' => $data['estimate']->company->name,
			'due_date' => $due_date,
			'estimate_id' => $data['core_settings']->estimate_prefix . $data['estimate']->estimate_reference,
			'client_link' => $data['core_settings']->domain,
			'company' => $data['core_settings']->company,
			'logo' => '<img src="' . base_url($data['core_settings']->logo) . '" alt="' . $data['core_settings']->company . '"/>',
			'invoice_logo' => '<img src="' . base_url($data['core_settings']->invoice_logo) . '" alt="' . $data['core_settings']->company . '"/>',
		];

		// Generate PDF
		$html = view($data['core_settings']->template . '/' . $data['core_settings']->estimate_pdf_template, $data);
		$html = $this->parser->parse_string($html, $parse_data);
		$filename = lang('application_estimate') . '_' . $data['core_settings']->estimate_prefix . $data['estimate']->estimate_reference;
		pdf_create($html, $filename, false);

		// Email
		$subject = $this->parser->parse_string($data['core_settings']->estimate_mail_subject, $parse_data);
		$email = \Config\Services::email();
		$email->setFrom($data['core_settings']->email, $data['core_settings']->company);

		if (!isset($data['estimate']->company->client->email)) {
			session()->setFlashdata('message', 'error: This client company has no primary contact! Just add a primary contact.');
			return redirect()->to('estimates/view/' . $id);
		}

		$email->setTo($data['estimate']->company->client->email);
		$email->setSubject($subject);
		$email->attach("files/temp/" . $filename . ".pdf");

		$email_estimate = read_file('./app/Views/' . $data['core_settings']->template . '/templates/email_estimate.html');
		$message = $this->parser->parse_string($email_estimate, $parse_data);
		$email->setMessage($message);

		if ($email->send()) {
			session()->setFlashdata('message', 'success:' . lang('messages_send_estimate_success'));
			$data['estimate']->status = 'Sent';
			$data['estimate']->sent_date = date('Y-m-d');
			$this->invoiceModel->save($data['estimate']);
			log_message('info', 'Estimate #' . $data['core_settings']->estimate_prefix . $data['estimate']->estimate_reference . ' has been sent to ' . $data['estimate']->company->client->email);
		} else {
			session()->setFlashdata('message', 'error:' . lang('messages_send_estimate_error'));
			log_message('error', 'ERROR: Estimate #' . $data['core_settings']->estimate_prefix . $data['estimate']->estimate_reference . ' has not been sent to ' . $data['estimate']->company->client->email . '. Please check your server\'s email settings.');
		}

		unlink("files/temp/" . $filename . ".pdf");
		return redirect()->to('estimates/view/' . $id);
	}

	public function convert($data, $index = 0)
	{
		$output = array_filter($data, function ($item) use ($index) {
			return $item->parent == $index;
		});

		$real_output = [];
		foreach ($output as $item) {
			$real_output[] = [
				'id' => $item->id,
				'libelle' => $item->libelle,
				'children' => $this->convert($data, $item->id),
			];
		}
		return $real_output;
	}

	// Add item to the estimate
	public function item($id = false)
	{
		if ($this->request->getMethod() === 'post') {
			$postData = $this->request->getPost();
			unset($postData['send']);
			$postData = array_map('htmlspecialchars', $postData);

			if (!empty($postData['name'])) {
				// Insert new item
				$d = [
					'name' => $postData['name'],
					'value' => $postData['value'],
					'tva' => $postData['tva'],
					'id_family' => $postData['id_family'],
					'unit' => $postData['unit'],
					'description' => $postData['description']
				];
				$this->itemModel->save($d);
				$postData['item_id'] = $this->itemModel->insertID();
			} else {
				if ($postData['item_id'] == "-") {
					session()->setFlashdata('message', 'error:' . lang('messages_add_item_error'));
					return redirect()->to('estimates/view/' . $postData['invoice_id']);
				} else {
					$itemvalue = $this->itemModel->find($postData['item_id']);
					$postData['name'] = $itemvalue->name;
					$postData['value'] = $postData['Prixunitaire'];
				}
			}

			unset($postData['id_family']);
			unset($postData['Prixunitaire']);
			$item = $this->invoiceHasItemModel->insert($postData);

			if (!$item) {
				session()->setFlashdata('message', 'error:' . lang('messages_add_item_error'));
			} else {
				session()->setFlashdata('message', 'success:' . lang('messages_add_item_success'));
			}
			return redirect()->to('estimates/view/' . $postData['invoice_id']);
		} else {
			$item_units = $this->itemModel->findAll();
			$this->view_data['item_units'] = $item_units;
			$this->view_data['estimate'] = $this->invoiceModel->find($id);
			$company = $this->companyModel->find($this->view_data['estimate']->company_id);
			$this->view_data['company'] = $company;

			$this->view_data['items'] = $this->itemModel->findAll();
			// Create the list of items
			$list_items = ['0' => '-'];

			foreach ($this->viewData['items'] as $value) {
				$list_items[$value->id] = $value->name . " - " . $value->value . " " . $this->settingModel->first()->currency;
			}

			$this->viewData['list_items'] = $list_items;
			$this->viewData['title'] = lang('application_add_item');
			$families = $this->itemModel->findAll(['inactive' => 0]);
			$this->viewData['families'] = $this->convert($families);
			$this->viewData['form_action'] = 'estimates/item';
			echo view('modal', $this->viewData);
		}
	}

	public function itemEmpty($id = false)
	{
		if ($this->request->getMethod() === 'post') {
			$postData = $this->request->getPost();
			unset($postData['send']);
			$postData = array_map('htmlspecialchars', $postData);

			$itemData = [
				'name' => '',
				'value' => 0,
				'description' => '',
				'id_family' => null,
				'unit' => '',
			];
			$this->itemModel->insert($itemData);
			$postData['item_id'] = $this->itemModel->insertID();
			$item = $this->invoiceHasItemModel->insert($postData);

			if (!$item) {
				session()->setFlashdata('message', 'error:' . lang('messages_add_item_error'));
			} else {
				session()->setFlashdata('message', 'success:' . lang('messages_add_item_success'));
			}
			return redirect()->to('estimates/view/' . $postData['invoice_id']);
		}
	}

	public function duplicateItemEmpty($id)
	{
		$item = $this->itemModel->find($id);
		unset($item['id']);
		$this->itemModel->insert($item);
		return redirect()->to('estimates/view/' . $item->invoice_id);
	}

	public function itemUpdate($id = false)
	{
		// Placeholder for the item update logic, you would need to implement this
		// based on your application's requirements.
	}
	function item_delete($id = FALSE, $estimate_id = FALSE)
	{
		$item = new  InvoiceHasItemModel();
		$item->find($id);
		$item->delete();
		view('estimates/view');
		if (!$item) {
			session()->set_flashdata('message', 'error:' . lang('messages_delete_item_error'));
		} else {
			session()->set_flashdata('message', 'success:' . lang('messages_delete_item_success'));
		}
		redirect('estimates/view/' . $estimate_id);
	}

	/*
	Facturer un devis
	*/
	public function facture(int $id)
	{
		$settings = $this->settingModel->first();
		$devis = $this->db->table('invoices')->getWhere(['id' => $id])->getRow();

		if (!$devis) {
			// Handle case when invoice is not found
			return redirect()->to('/invoices')->with('message', 'Invoice not found.');
		}

		// Unset unnecessary fields
		unset($devis->id, $devis->id_facture);
		$devis->estimate = 0;
		$devis->creation_date = date("Y-m-d");

		// Revert reference estimate
		$year = date("Y");
		$lastfacture = $this->facture->getLastInvoice();
		$lastRef = explode('-', $lastfacture->creation_date);

		if ($lastRef[0] != $year) {
			$devis->estimate_reference = '1';
		} else {
			$devis->estimate_reference = $settings->invoice_reference;
		}

		$devis->reference = $settings->invoice_reference + 1;
		$factureid = $this->db->table('facture')->orderBy('id', 'desc')->get()->getRow()->id + 1;

		// Update the invoice reference in settings
		$settings->invoice_reference = $devis->estimate_reference + 1;
		$settings->save();

		// Estimate status
		$status = config('App');

		$set = [
			"id_facture" => $factureid,
			"status" => $status,
			"estimate_accepted_date" => date('Y-m-d')
		];

		$this->db->table('invoices')->update($set, ['id' => $id]);
		$devis->id = $factureid;

		// Formatting estimate reference
		$devis->estimate_reference = str_pad($devis->estimate_reference, 2, '0', STR_PAD_LEFT);

		// Devis name
		$estimate_pieces = explode("-", strrev($settings->invoice_prefix));
		$var = date("y-m-d");
		$pieces = explode("-", $var);
		$subpiecesYear = explode(' ', $pieces[0]);

		if ($estimate_pieces[0] == "YY") {
			$devis->estimate_num = strrev($estimate_pieces[1]) . $subpiecesYear[0] . $devis->estimate_reference;
		} else {
			$devis->estimate_num = strrev($estimate_pieces[2]) . $subpiecesYear[0] . $pieces[1] . $devis->estimate_reference;
		}

		unset($devis->due_date);

		$issue_date = date("d-m-y", strtotime($devis->issue_date));
		//$refP = $this->estimateModel->getByRef((int)$devis->project_id)->refp;

		$idprojet = $this->db->table('facture')->orderBy('id', 'desc')->get()->getRow()->project_id;
		$totalg = (float) $this->projectModel->calculquantitelg($idprojet)->quantitelg;

		$devis->subject = $devis->project_name;
		$devis->status = config('App');
		$devis->timbre_fiscal = $settings->timbre_fiscal;
		$devis->notes = '&nbsp;Attachement (Réf: ' . $devis->project_ref . ')<div>ARIANA, LE ' . $issue_date . '</div><div><br></div><div>La somme des heures passées sur ce dossier est ' . $devis->calcul_heure . ' Heures</div><div>La somme des quantités sur ce dossier est ' . ($devis->project_surface == 0 ? $totalg : $devis->project_surface) . ' m²</div><div><br> Réf QUARTA: ' . $refP . '</div>';

		$factured = $this->db->table('facture')->insert((array)$devis);
		$idfacture = $this->db->table('facture')->orderBy('id', 'desc')->get()->getRow()->id;

		$items = $this->db->table('invoice_has_items')->getWhere(['invoice_id' => $id])->getResult();

		// Getting project ID again, could be optimized
		$idprojet = $this->db->table('facture')->orderBy('id', 'desc')->get()->getRow()->project_id;

		$subjects = $this->db->table('tickets')->select('subject, surface, longueur')->getWhere(['project_id' => $idprojet])->getResultArray();

		foreach ($subjects as $sub) {
			$item = new \stdClass();
			$item->facture_id = $idfacture;
			$item->name = $sub["subject"];
			$item->unit = 'm²';
			$item->amount = $sub["surface"] ?: $sub["longueur"]; // Use surface or length

			// Determine the value based on subject
			if (stripos($sub["subject"], "niveaux") !== false || stripos($sub["subject"], "interieur") !== false || stripos($sub["subject"], "masse") !== false || stripos($sub["subject"], "héberge") !== false) {
				$item->value = $this->calculateValue($item->amount, 'niveaux');
			} elseif (stripos($sub["subject"], "façades") !== false || stripos($sub["subject"], "coupes") !== false) {
				$item->value = $this->calculateValue($item->amount, 'facades');
			} elseif (stripos($sub["subject"], "toitu") !== false) {
				$item->value = $this->calculateValue($item->amount, 'toiture');
			} elseif (stripos($sub["subject"], "maquette") !== false) {
				$item->value = $this->calculateValue($item->amount, 'maquette');
			} else {
				$item->value = '';
			}

			$this->db->table('facture_has_items')->insert((array)$item);
		}

		foreach ($items as $item) {
			unset($item->id, $item->invoice_id);
			$this->db->table('facture_has_items')->insert((array)$item);
		}

		$messageType = $factured ? 'success' : 'error';
		$message = $factured ? lang('messages_delete_factured_success') : lang('messages_delete_factured_error');
		return redirect()->to('invoices/view/' . $idfacture)->with($messageType, $message);
	}

	private function calculateValue(float $amount, string $type): string
	{
		switch ($type) {
			case 'niveaux':
				if ($amount >= 0 && $amount <= 500) return '1';
				if ($amount >= 501 && $amount <= 1000) return '0.9';
				if ($amount >= 1001 && $amount <= 1500) return '0.75';
				if ($amount >= 1501 && $amount <= 2500) return '0.65';
				if ($amount >= 2500 && $amount <= 5000) return '0.5';
				if ($amount >= 5000 && $amount <= 10000) return '0.42';
				if ($amount >= 10000 && $amount <= 50000) return '0.35';
				break;
			case 'facades':
				if ($amount >= 0 && $amount <= 500) return '1';
				if ($amount >= 501 && $amount <= 1000) return '0.8';
				if ($amount >= 1001 && $amount <= 1500) return '0.7';
				if ($amount >= 1501 && $amount <= 2500) return '0.6';
				if ($amount >= 2500 && $amount <= 5000) return '0.42';
				if ($amount >= 5000) return '0.35';
				break;
			case 'toiture':
				if ($amount >= 0 && $amount <= 500) return '0.3';
				if ($amount >= 501 && $amount <= 1000) return '0.28';
				if ($amount >= 1001 && $amount <= 1500) return '0.25';
				if ($amount >= 1501 && $amount <= 2500) return '0.2';
				if ($amount >= 2500 && $amount <= 5000) return '0.15';
				if ($amount >= 5000) return '0.1';
				break;
			case 'maquette':
				if ($amount >= 0 && $amount <= 100) return '3';
				if ($amount >= 101 && $amount <= 200) return '2';
				if ($amount >= 201 && $amount <= 400) return '1.8';
				if ($amount >= 401 && $amount <= 1000) return '1.6';
				if ($amount >= 1001 && $amount <= 3000) return '1.5';
				if ($amount >= 3001) return '1.4';
				break;
		}
		return '0';
	}

	/*
	Dupliquer un devis
	*/
	public function duplicate($id)
	{
		// Load the invoice model
		$invoiceModel = new \App\Models\InvoiceModel();
		$settingsModel = new \App\Models\SettingModel();
		$invoiceItemsModel = new \App\Models\InvoiceHasItemModel();

		// Fetch the invoice by id
		$devis = $invoiceModel->find($id);

		if (!$devis) {
			// Handle the case where the invoice is not found
			return redirect()->to('/invoices')->with('error', 'Invoice not found');
		}

		// Fetch the company settings
		$company_id = session()->get('current_company');
		$settings = $settingsModel->where('id_vcompanies', $company_id)->first();

		// Generate new reference and update dates
		$devis->reference = $settings->estimate_reference;
		$devis->creation_date = date("Y-m-d");

		$year = date("Y");

		// Fetch the last created invoice
		$lastestimate = $invoiceModel->orderBy('id', 'DESC')->first();
		$lastRef = explode('-', date('Y-m-d', strtotime($lastestimate->creation_date)));

		if ($lastRef[0] != $year) {
			$devis->estimate_reference = '1';
		} else {
			$devis->estimate_reference = $settings->estimate_reference;
		}

		// Update issue and due dates
		$devis->issue_date = date('Y-m-d');
		$echeance = date('Y-m-d', strtotime($devis->issue_date . " +" . $settings->echeance . " days"));
		$devis->due_date = $echeance;

		// Update the status for the new estimate
		$devis->status = config('App');

		// Fetch the maximum ID from the invoices table
		$maxId = $invoiceModel->selectMax('id')->first();
		$devis->id = $maxId->id + 1;

		// Update the estimate reference if less than 10
		$new_estimate_reference = $devis->estimate_reference;
		if ($new_estimate_reference < 10) {
			$new_estimate_reference = '0' . $new_estimate_reference;
		}

		// Construct the estimate number
		$estimate_pieces = explode("-", strrev($settings->estimate_prefix));
		$var = date("y-m-d", strtotime($devis->issue_date));
		$pieces = explode("-", $var);
		$piecesYear = $pieces[0];
		$piecesMonth = $pieces[1];

		if ($estimate_pieces[0] == "YY") {
			$devis->estimate_num = strrev($estimate_pieces[1]) . $piecesYear . $new_estimate_reference;
		} else {
			$devis->estimate_num = strrev($estimate_pieces[2]) . $piecesYear . $piecesMonth . $new_estimate_reference;
		}

		// Insert the new invoice (devis)
		$invoiceModel->insert($devis);

		// Update the estimate reference in settings
		$settings->update_attributes(['estimate_reference' => $new_estimate_reference + 1]);

		// Copy the items from the original invoice
		$items = $invoiceItemsModel->where('invoice_id', $id)->findAll();
		$devisid = $invoiceModel->orderBy('id', 'DESC')->first();

		foreach ($items as $item) {
			unset($item->id); // Remove the ID to avoid conflicts
			$item->invoice_id = $devisid->id; // Assign to new invoice
			$invoiceItemsModel->insert($item);
		}

		// Redirect to the estimates page
		return redirect()->to('/estimates');
	}
	public function renderItem($name)
	{
		// Assuming $this->item is your model and using it to get the item by its ID
		$itemModel = new \App\Models\ItemsModel(); // Use the appropriate model class
		$item = $itemModel->getById(urldecode($name));

		if (!$item) {
			// Return a 404 response if the item is not found
			return $this->response->setStatusCode(404)->setJSON(['error' => 'Item not found']);
		}

		// Prepare the output array
		$output = [
			"value" => $item->value,
			"tva" => $item->tva,
			"unit" => $item->unit,
		];

		// Return the output as JSON
		return $this->response->setJSON($output);
	}
	public function sendfiles($id)
	{
		// Check if it's a POST request
		if ($this->request->getMethod() == 'post') {
			$id = $this->request->getPost('id');
			$data = [];

			// Fetch the invoice data
			$data['estimate'] = $this->invoiceModel->find($id);
			$data['type'] = "devis";

			// Load helpers for file and PDF
			helper(['filesystem', 'text']);
			$pdfroot  = WRITEPATH . 'third_party/pdf/devis.pdf';  // WRITEPATH is for writable directory

			// Initialize Dompdf
			$dompdf = new Dompdf();

			// Fetch related data
			$db = \Config\Database::connect();
			$itemsQuery = $db->table('invoice_has_items')->where('invoice_id', $id)->orderBy('position', 'asc')->get();
			$data['items'] = $itemsQuery->getResult();

			// Fetch discount count
			$countDiscountQuery = $db->query("SELECT discount FROM invoice_has_items WHERE invoice_id = ? AND discount > 0", [$id]);
			$data['countDiscount'] = $countDiscountQuery->getNumRows();

			// Fetch company and settings data
			$option = array("id_vcompanies" => session('current_company'));
			$data['core_settings'] = $this->settingModel->find($option);
			$data['vcompanies'] = $db->table('v_companies')->where('id', session('current_company'))->get()->getResult();

			// Project and company details
			$projectModel = new \App\Models\ProjectModel(); // Assuming you have this model
			$data['num_project'] = $projectModel->getProjectById($data['estimate']->project_id)->id;
			$logo = $db->table('v_companies')->where('id', session('current_company'))->get()->getRow()->picture;
			$data['logo'] = $logo;

			$company = $db->table('companies')->where('id', $data['estimate']->company_id)->get()->getRow();
			$data['company'] = $company;
			$client = $db->table('clients')->where('id', $company->client_id)->get()->getRow();
			$data['client'] = $client;

			// Handle due date and currency info
			$data['due_date'] = date($data["core_settings"]->date_format, strtotime($data["estimate"]->due_date));
			$refTypeModel = new \App\Models\RefTypeModel(); // Assuming you have a RefTypeModel
			// $chiffre = $this->referentielsModel->getReferentielsByIdType($refTypeModel->getRefTypeByName($data['estimate']->currency)->id)->name;
			//   $data['chiffre'] = $chiffre;

			// Parse data
			$parse_data = [
				'due_date' => $data['due_date'],
				'estimate_id' => $data['core_settings']->estimate_prefix . $data['estimate']->estimate_reference,
				'client_link' => $data['core_settings']->domain,
				'company' => $data['core_settings']->company,
			];

			// Generate HTML from view and render PDF
			$html = view($data['core_settings']->template . '/' . $data['core_settings']->estimate_pdf_template . $data['core_settings']->default_template, $data);
			$dompdf->loadHtml($html);
			$dompdf->setPaper('A4', 'portrait');
			$dompdf->render();

			// Save PDF to the specified path
			$pdf_string = $dompdf->output();
			write_file($pdfroot, $pdf_string);

			// Send the email
			$email = \Config\Services::email();
			$email->setFrom($_POST['smtp_user'], 'Your Company Name');
			$email->setTo($_POST['dist']);
			if ($_POST['cc']) {
				$email->setCC($_POST['cc']);
			}
			$email->setSubject('Estimate File');
			$email->setMessage($_POST['notes']);
			$email->attach($pdfroot);

			if ($email->send()) {
				// Redirect after success
				return redirect()->to('/estimates');
			} else {
				// Handle failure
				return redirect()->back()->withInput()->with('error', 'Failed to send the email.');
			}
		} else {
			// If it's not a POST request, load the form
			$data['form_action'] = 'estimates/sendfiles';
			$data['data'] = $this->invoiceModel->find($id);
			$data['type'] = "devis";
			return view('settings/sendFile', $data);
		}
	}
}
