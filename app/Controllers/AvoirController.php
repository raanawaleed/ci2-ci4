<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Database\Migrations\CreateAvoirHasPayments;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use App\Models\AvoirMOdel;
use App\Models\AvoirHasItemModel;
use App\Models\ClientModel;
use App\Models\CompanyModel;
use App\Models\CompteBancaireModel;
use App\Models\FactureModel;
use App\Models\ItemsModel;
use App\Models\ProjectModel;
use App\Models\RefTypeModel;
use App\Models\RefTypeOccurencesModel;
use App\Models\SettingModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class AvoirController extends BaseController
{

	public $idTypeRefAvoir;
	public $idTypeRefTVA;
	public $idMoyensPaiement;
	protected $avoirHasPaymentModel;
	protected $avoirModel;
	protected $client;
	protected $item;
	protected $project;
	protected $factureHasItem;
	protected $referentiels;
	protected $refType;
	protected $companyModel;
	protected $avoirHasItemModel;
	protected $compteBancaireModel, $avoirHasItem, $settingModel;
	protected $view_data = [];

	protected $helpers = ['form', 'url'];

	public function initController(
		RequestInterface $request,
		ResponseInterface $response,
		LoggerInterface $logger
	) {
		parent::initController($request, $response, $logger);

		$this->item = new ItemsModel();
		$this->project = new ProjectModel();
		$this->factureHasItem = new FactureModel();
		$this->referentiels = new RefTypeOccurencesModel();
		$this->refType = new RefTypeModel();
		$this->avoirModel = new AvoirMOdel();
		$this->avoirHasItem = new AvoirHasItemModel();
		$this->companyModel = new CompanyModel();
		$this->client = new ClientModel();  // Assuming you have a ClientModel
		$this->avoirHasItemModel = new AvoirHasItemModel();
		$this->compteBancaireModel = new CompteBancaireModel();
		$this->avoirHasPaymentModel = new CreateAvoirHasPayments();
		$this->settingModel = new SettingModel();

		if (!$this->client && (!session()->get('user') || session()->get('user')->admin != 1)) {
			return redirect()->to('login');
		}

		// Load configuration
		$config = config('App');
		// $this->idTypeRefAvoir = $config->type_id_etat_avoir;
		// $this->idTypeRefTVA = $config->type_id_tva;
		// $this->idMoyensPaiement = $config->type_id_moyens_paiement;

		// Load submenus
		$submenus = $this->referentiels->getReferentielsByIdType($this->idTypeRefAvoir);
		$this->view_data['submenu'] = [
			lang('application_all') => 'avoir'
		];

		foreach ($submenus as $submenu) {
			if ($submenu->name != "Sent" && $submenu->name != "Pending") {
				$submenuLang = lang('application_' . $submenu->name);
				$this->view_data['submenu'][$submenuLang ?: $submenu->name] = 'avoir/filter/' . $submenu->name;
			}
		}
	}

	// Chargement initial
	public function index()
	{
		$this->view_data['settings'] = $this->settingModel->first();
		$avoirs = $this->avoirModel->findAll();

		foreach ($avoirs as $avoir) {
			$refTypeCurrency = $this->refType->getRefTypeByName($avoir->currency)->id;
			$avoir->chiffre = $this->referentiels->getReferentielsByIdType($refTypeCurrency)[0]->name;
		}

		$this->view_data['avoirs'] = $avoirs;
		return view('avoir/all', $this->view_data);
	}
	//condition of filter
	function filter($condition = FALSE, $year = FALSE)
	{
		$builder = $this->avoirModel;

		if ($condition == "False") {
			$avoirs = $builder->findAll();
			if ($year) {
				foreach ($avoirs as $key => $avoir) {
					$date = \DateTime::createFromFormat("Y-m-d", $avoir->issue_date);
					if ($date->format("Y") != $year) {
						unset($avoirs[$key]);
					}
				}
			}
		} else {
			$idType = $this->refType->getRefTypeByName('Avoir')->id;
			$idState = $this->referentiels->getReferentiels($idType, urldecode($condition))->id;
			$builder->where('status', $idState);
			$avoirs = $builder->findAll();
		}

		foreach ($avoirs as $avoir) {
			$refTypeCurrency = $this->refType->getRefTypeByName($avoir->currency)->id;
			$avoir->chiffre = $this->referentiels->getReferentielsByIdType($refTypeCurrency)->name;
		}

		$this->view_data['avoirs'] = $avoirs;
		$this->view_data['settings'] = $this->settingModel->first();
		return view('avoir/all', $this->view_data);
	}


	public function create()
	{
		if ($this->request->getPost()) {
			$avoir_reference = $this->settingModel->where('id_vcompanies', session('current_company'))->first();
			$refType = $this->refType->getRefTypeByName($this->request->getPost('currency'))->id;
			$chiffre = $this->referentiels->getReferentielsByIdType($refType)->name;

			if ($this->request->getPost('company') == 1) {
				$company = $this->companyModel->orderBy('id', 'desc')->first();
				$ref = $company->reference;

				$data = [
					'name' => $this->request->getPost('nomClient'),
					'reference' => $ref + 1,
					'passager' => '1',
					'tva' => 1,
					'guarantee' => 0,
					'timbre_fiscal' => 1,
				];

				$this->companyModel->insert($data);
				$this->request->getPost('company_id', $this->companyModel->getInsertID());
				$new_company_reference = $avoir_reference->company_reference + 1;
				$avoir_reference->update(['company_reference' => $new_company_reference]);
			}

			unset($_POST['nomClient'], $_POST['send'], $_POST['_wysihtml5_mode'], $_POST['files']);
			$_POST['creation_date'] = date("Y-m-d");

			$year = date("Y");
			$lastAvoir = $this->avoirModel->orderBy('id', 'desc')->first();
			if (explode('-', $lastAvoir->creation_date)[0] != $year) {
				$this->request->getPost('reference', '01');
			}

			$avoir_num = $avoir_reference->avoir_reference . $this->request->getPost('reference');
			$pieces = explode("-", date("y-m-d", strtotime($this->request->getPost('issue_date'))));
			$piecesYear = $pieces[0];
			$piecesMonth = $pieces[1];

			if (strrev($avoir_reference->avoir_prefix)[0] == "YY") {
				$avoir_num = strrev($avoir_reference->avoir_prefix) . $piecesYear . $this->request->getPost('reference');
			} else {
				$avoir_num = strrev($avoir_reference->avoir_prefix) . $piecesYear . $piecesMonth . $this->request->getPost('reference');
			}

			$this->request->getPost('avoir_num', $avoir_num);
			$this->avoirModel->insert($this->request->getPost());

			$new_avoir_reference = $this->request->getPost('reference') + 1;
			$avoir_reference->update(['avoir_reference' => $new_avoir_reference]);

			if (!$this->avoirModel->affectedRows()) {
				session()->setFlashdata('message', 'error: ' . lang('messages_create_avoir_error'));
			} else {
				session()->setFlashdata('message', 'success: ' . lang('messages_create_avoir_success'));
			}

			return redirect()->to('avoir');
		} else {
			$this->view_data['companies'] = $this->companyModel->where('inactive', 0)->findAll();
			$this->view_data['title'] = lang('application_create_avoir');
			$this->view_data['current_date'] = date('Y-m-d');
			$this->view_data['core_settings'] = $this->settingModel->where('id_vcompanies', session('current_company'))->first();
			$this->view_data['state'] = $this->referentiels->getReferentielsByIdType($this->refType->getRefTypeByName('Avoir')->id);
			$this->view_data['currencys'] = $this->referentiels->getReferentielsByIdType($this->refType->getRefTypeByName("Devise")->id);
			return view('avoir/_avoir', $this->view_data);
		}
	}


	public function view($id = false)
	{
		$avoir = $this->avoirModel->find($id);
		if (!$avoir) {
			throw new PageNotFoundException('Avoir not found');
		}

		$this->view_data['avoir'] = $avoir;
		$this->view_data['project'] = $this->project->where('reference', $avoir->project_id)->first();

		$refType = $this->refType->getRefTypeByName($avoir->currency)->id;
		$this->view_data['chiffre'] = $this->referentiels->getReferentielsByIdType($refType)->name;

		$this->view_data['company'] = $this->companyModel->find($avoir->company_id);
		$this->view_data['client'] = $this->companyModel->find($avoir->client_id);

		return view('avoir/view', $this->view_data);
	}

	// Mettre à jour un avoir
	function update($id = false)
	{
		if ($this->request->getPost()) {
			$id = $this->request->getPost('id');
			$avoir = $this->avoirModel->find($id);

			if ($this->request->getPost('company') == 1 && $avoir->passager != 1) {
				$ref = $this->companyModel->orderBy('id', 'desc')->first()->reference;
				$data = [
					'name' => $this->request->getPost('nomClient'),
					'id_vcompanies' => session('current_company'),
					'reference' => $ref + 1,
					'passager' => 1,
					'tva' => 1,
					'guarantee' => 0,
					'timbre_fiscal' => 1,
				];
				$this->companyModel->insert($data);
				$this->request->setGlobal('company_id', $this->companyModel->getInsertID());
			}

			$this->avoirModel->update($id, $this->request->getPost());

			session()->setFlashdata('message', 'success: ' . lang('messages_save_avoir_success'));
			return redirect()->to('avoir/view/' . $id);
		} else {
			$this->view_data['avoir'] = $this->avoirModel->find($id);
			$this->view_data['currencys'] = $this->referentiels->getReferentielsByIdType($this->refType->getRefTypeByName("Devise")->id);
			$this->view_data['companies'] = $this->companyModel->where('inactive', 0)->findAll();
			$this->view_data['state'] = $this->referentiels->getReferentielsByIdType($this->refType->getRefTypeByName('Avoir')->id);

			return view('avoir/edit', $this->view_data);
		}
	}


	public function duplicate($id)
	{
		$avoir = $this->avoirModel->find($id);
		if (!$avoir) {
			throw PageNotFoundException::forPageNotFound();
		}

		$settings = $this->settingModel->first();
		$newAvoirReference = $settings->avoir_reference;
		$avoir->reference = $newAvoirReference;
		$avoir->issue_date = date('Y-m-d');
		$echeance = date('Y-m-d', strtotime($avoir->issue_date . " +" . $settings->echeance . " days"));
		$idType = $this->refType->getRefTypeByName('Avoir')->id;

		$lastId = $this->avoirModel->selectMax('id')->first();
		$avoir->id = $lastId['id'] + 1;

		// Format the new Avoir number
		$avoirPieces = explode("-", strrev($settings->avoir_prefix));
		$pieces = explode("-", date("y-m-d", strtotime($avoir->issue_date)));
		$piecesYear = $pieces[0];
		$piecesMonth = $pieces[1];
		$subpiecesYear = explode(' ', $pieces[0]);

		if ($avoirPieces[0] == "YY") {
			$avoir->avoir_num = strrev($avoirPieces[1]) . $subpiecesYear[0] . str_pad($newAvoirReference, 2, '0', STR_PAD_LEFT);
		} else {
			$avoir->avoir_num = strrev($avoirPieces[2]) . $subpiecesYear[0] . $piecesMonth . str_pad($newAvoirReference, 2, '0', STR_PAD_LEFT);
		}

		$avoir->creation_date = date("Y-m-d");
		$this->avoirModel->insert($avoir);
		$settings->update(['avoir_reference' => $newAvoirReference + 1]);

		$items = $this->avoirHasItemModel->where('avoir_id', $id)->findAll();
		foreach ($items as $item) {
			unset($item['id']); // Remove the id to avoid primary key conflict
			$item['avoir_id'] = $avoir->id;
			$this->avoirHasItemModel->insert($item);
		}

		return redirect()->to('avoir');
	}

	public function preview($id = null, $attachment = false)
	{
		$avoir = $this->avoirModel->find($id);
		if (!$avoir) {
			throw PageNotFoundException::forPageNotFound();
		}

		$coreSettings = $this->settingModel->where('id_vcompanies', session()->get('current_company'))->first();
		$avoir->avoir_status = $this->referentiels->getReferentielsById($avoir->avoir_status)->name;

		$items = $this->avoirHasItemModel->where('avoir_id', $id)->orderBy('position', 'asc')->findAll();
		$countDiscount = $this->avoirHasItemModel->where('avoir_id', $id)->where('discount >', 0)->countAllResults();

		$company = $this->companyModel->find($avoir->company_id);
		$client = $this->client->find($company->client_id);

		$logo = $this->companyModel->select('picture')->find(session()->get('current_company'))->picture;
		$chiffre = $this->referentiels->getReferentielsByIdType($this->refType->getRefTypeByName($avoir->currency)->id)[0]->name;

		$data = [
			'avoir' => $avoir,
			'core_settings' => $coreSettings,
			'items' => $items,
			'countDiscount' => $countDiscount,
			'company' => $company,
			'client' => $client,
			'vcompanies' => $this->companyModel->findAll(),
			'logo' => $logo,
			'chiffre' => $chiffre,
		];

		$parseData = [
			'avoir_id' => $coreSettings->avoir_prefix . $data['avoir']->reference,
			'client_link' => $coreSettings->domain,
			'company' => $coreSettings->company,
			'client_id' => $data['avoir']->company->reference,
		];

		$html = view($coreSettings->template . '/' . $coreSettings->avoir_pdf_template . $coreSettings->default_template, $data);
		$html = $this-par->setData($parseData)->parseString($html);
		$filename = $data['avoir']->avoir_num . '_' . $data['company']->name;

		// Generate PDF
		$this->pdf->load_view($html, $filename);
	}


	//ajouter article 
	public function item($id = null)
	{
		if ($this->request->getMethod() === 'post') {
			$postData = $this->request->getPost();
			unset($postData['send']);
			$postData = array_map('htmlspecialchars', $postData);

			if ($postData['name'] != "") {
				// Insert new item
				$itemData = [
					'name' => $postData['name'],
					'value' => $postData['value'],
					'tva' => $postData['tva'],
					'id_vcompanies' => session()->get('current_company'),
					'id_family' => $postData['id_family'],
					'unit' => $postData['unit'],
					'description' => $postData['description']
				];
				$this->itemModel->insert($itemData);
				$postData['item_id'] = $this->itemModel->insertID();
			} else {
				if ($postData['item_id'] == "-") {
					session()->setFlashdata('message', 'error:' . lang('messages_add_item_error'));
					return redirect()->to('avoir/view/' . $postData['avoir_id']);
				} else {
					$rebill = explode("_", $postData['item_id']);
					if ($rebill[0] == "rebill") {
						$itemValue = $this->expenseModel->find($rebill[1]);
						$postData['name'] = $itemValue->description;
						$postData['value'] = $itemValue->value;
						$itemValue->rebill = 2;
						$itemValue->invoice_id = $postData['facture_id'];
						$itemValue->save();
					} else {
						$itemValue = $this->itemModel->find($postData['item_id']);
						$postData['name'] = $itemValue->name;
						$postData['value'] = $postData['Prixunitaire'];
					}
				}
			}

			unset($postData['id_family'], $postData['Prixunitaire']);

			if ($postData['item_id'] == "-") {
				session()->setFlashdata('message', 'error:' . lang('messages_add_item_error'));
			} else {
				$this->avoirHasItemModel->insert($postData);
				session()->setFlashdata('message', 'success:' . lang('messages_item_add'));
			}

			return redirect()->to('avoir/view/' . $postData['avoir_id']);
		}

		// Load data for the item
		$itemData = [];
		if ($id) {
			$itemData = $this->itemModel->find($id);
		}

		// Set up view data
		$data = [
			'item' => $itemData,
			'families' => $this->familiesModel->findAll(),
			'tva' => $this->referentiels->getReferentielsByIdType($this->idTypeRefTVA),
			'avoir_id' => $this->request->getGet('avoir_id'),
		];

		return view('avoir/item', $data);
	}
	public function renderItem($name)
	{
		$item = $this->itemModel->getByName(urldecode($name));
		if ($item) {
			return $this->response->setJSON([
				"value" => $item->value,
				"tva" => $item->tva,
				"unit" => $item->unit,
			]);
		} else {
			return $this->response->setStatusCode(404)->setJSON(['error' => 'Item not found']);
		}
	}
	public function convert(array $data, int $index = 0): array
	{
		$output = array_filter($data, fn($item) => $item->parent == $index);
		return array_map(function ($item) use ($data) {
			return [
				'id' => $item->id,
				'libelle' => $item->libelle,
				'children' => $this->convert($data, $item->id),
			];
		}, $output);
	}
	/*****Ligne Vide**********************************/
	public function itemEmpty($id = false)
	{
		if ($this->request->getMethod() === 'post') {
			$postData = $this->request->getPost();
			unset($postData['send']);
			$postData = array_map('htmlspecialchars', $postData);

			if (!empty($postData['name'])) {
				$postData['discount'] = $postData['discount'] ?? 0;
				$this->avoirHasItemModel->insert($postData);
				session()->setFlashdata('message', 'success:' . lang('messages_add_item_success'));
			} else {
				if ($postData['item_id'] == "-") {
					session()->setFlashdata('message', 'error:' . lang('messages_add_item_error'));
					return redirect()->to('avoir/view/' . $postData['avoir_id']);
				}

				$rebill = explode("_", $postData['item_id']);
				if ($rebill[0] == "rebill") {
					$itemValue = Expense::find($rebill[1]);
					$postData['name'] = $itemValue->description;
					$postData['value'] = $itemValue->value;
					$itemValue->rebill = 2;
					$itemValue->invoice_id = $postData['avoir_id'];
					$itemValue->save();
				} else {
					$itemValue = $this->itemModel->find($postData['item_id']);
					$postData['name'] = $itemValue->name;
					$postData['value'] = $itemValue->value;
				}
			}

			// Insert item into the avoir_has_items
			if ($this->avoirHasItemModel->insert($postData) === false) {
				session()->setFlashdata('message', 'error:' . lang('messages_add_item_error'));
			} else {
				session()->setFlashdata('message', 'success:' . lang('messages_add_item_success'));
			}
			return redirect()->to('avoir/view/' . $postData['avoir_id']);
		}

		// Load data for the item
		$avoir = $this->avoirModel->find($id);
		$itemUnits = $this->itemModel->findAll(); // Assuming you have this in your ItemModel

		$type = $this->db->query('SELECT libelle, id FROM items_has_family WHERE inactive=0 UNION SELECT libelle, id FROM items_has_family_parent WHERE inactive=0')->getResult();

		$data = [
			'type' => $type,
			'tva' => $this->referentiels->getReferentielsByIdType($this->idTypeRefTVA),
			'defaultTva' => setting::first()->tax,
			'avoir' => $avoir,
			'items' => $itemUnits,
		];

		return view('avoir/_itemEmpty', $data);
	}

	public function duplicateItemEmpty($id)
	{
		$avoirItem = $this->avoirHasItemModel->find($id);
		if ($avoirItem) {
			$newAvoirItem = $avoirItem;
			unset($newAvoirItem->id); // Remove the id to create a new entry
			$this->avoirHasItemModel->insert($newAvoirItem);
			session()->setFlashdata('message', 'success:' . lang('messages_duplicate_item_success'));
		} else {
			session()->setFlashdata('message', 'error:' . lang('messages_duplicate_item_error'));
		}

		return redirect()->to('avoir/view/' . $avoirItem->avoir_id);
	}

	public function itemDelete($id, $avoir_id)
	{
		$condition = ["id" => $id, "avoir_id" => $avoir_id];
		if ($this->avoirHasItemModel->where($condition)->delete() === false) {
			session()->setFlashdata('message', 'error:' . lang('messages_delete_item_error'));
		} else {
			session()->setFlashdata('message', 'success:' . lang('messages_delete_item_success'));
		}
		return redirect()->to('avoir/view/' . $avoir_id);
	}

	public function itemUpdate($id = false)
	{
		if ($this->request->getMethod() === 'post') {
			$postData = $this->request->getPost();
			unset($postData['send']);
			$postData = array_map('htmlspecialchars', $postData);

			if ($this->avoirHasItemModel->update($postData['id'], $postData) === false) {
				session()->setFlashdata('message', 'error:' . lang('messages_save_item_error'));
			} else {
				session()->setFlashdata('message', 'success:' . lang('messages_save_item_success'));
			}
			return redirect()->to('avoir/view/' . $postData['avoir_id']);
		} else {
			$avoirHasItem = $this->avoirHasItemModel->find($id);
			if ($avoirHasItem) {
				$type = $this->db->query('SELECT libelle, id FROM items_has_family WHERE inactive=0 UNION SELECT libelle, id FROM items_has_family_parent WHERE inactive=0')->getResult();
				$itemUnits = $this->db->query("SELECT * FROM item_units")->getResult();

				$data = [
					'avoir_has_items' => $avoirHasItem,
					'type' => $type,
					'item_units' => $itemUnits,
					'title' => lang('application_edit_item'),
					'form_action' => 'avoir/item_update'
				];

				return view('avoir/_itemEmpty', $data);
			} else {
				return redirect()->to('avoir/view');
			}
		}
	}

	public function payment($id = false)
	{
		$compteBancaire = $this->compteBancaireModel->findAll();
		if ($this->request->getMethod() === 'post') {
			$postData = $this->request->getPost();

			if ($postData["type"] == "virement") {
				$idCompteBancaire = $this->db->query("SELECT id FROM comptes_bancaires WHERE nom= '" . $postData['nomCompteBancaire'] . "'")->getRow()->id;
				$postData['id_compteBancaire'] = $idCompteBancaire;
			} else {
				unset($postData['id_compteBancaire']);
			}
			unset($postData['nomCompteBancaire'], $postData['send'], $postData['_wysihtml5_mode'], $postData['files']);
			$postData['user_id'] = $this->user->id;

			$invoice = $this->avoirModel->find($postData['avoir_id']);
			$settings = Setting::first();
			$postData['amount'] = number_format($postData['amount'], 2, '.', '');
			$idType = $this->refType->getRefTypeByName('Mode de paiement')->id;
			$postData['type'] = $this->referentiels->getReferentiels($idType, $postData['type'])->id;

			$this->db->table('avoir_has_payments')->insert($postData);

			// Check payment status
			$avoirOutstanding = $invoice->outstanding;
			$newStatus = ($avoirOutstanding == $postData['amount']) ? $this->config->item("occ_avoir_paye") : $this->config->item("occ_facture_p_paye");

			$this->avoirModel->update($postData['avoir_id'], ['status' => $newStatus]);
			if (isset($postData['date'])) {
				$this->avoirModel->update($id, ['paid_date' => $postData['date']]);
			}
			return redirect()->to('avoir/view/' . $postData['avoir_id']);
		} else {
			$avoir = $this->avoirModel->find($id);
			if ($avoir) {
				$paymentCount = $this->db->query('SELECT COUNT(*) as payment FROM facture_has_payments WHERE facture_id=' . $id)->getRow()->payment;
				$company = Company::find($avoir->company_id);

				$amount = $company->tva == 1 ? $avoir->sumht : $avoir->sum;
				if ($company->guarantee == 1) {
					$amount -= $amount * 10 / 100;
				}

				$data = [
					'core_settings' => $settings,
					'avoir' => $avoir,
					'payment_reference' => $paymentCount + 1,
					'typepaiement' => $this->referentiels->getReferentielsByIdType($this->idMoyensPaiement),
					'sumRest' => sprintf("%01.2f", round($amount - $avoir->paid, 2)),
					'title' => lang('application_add_payment'),
					'form_action' => 'avoir/payment',
					'compteBancaire' => $compteBancaire
				];

				return view('avoir/_payment', $data);
			} else {
				return redirect()->to('avoir/view');
			}
		}
	}

	public function paymentUpdate($id = false)
	{
		if ($this->request->getMethod() === 'post') {
			$postData = $this->request->getPost();
			unset($postData['send'], $postData['_wysihtml5_mode'], $postData['files']);

			$payment = $this->avoirHasPaymentModel->find($postData['id']);
			$idType = $this->refType->getRefTypeByName('Mode de paiement')->id;
			$postData['type'] = $this->referentiels->getReferentiels($idType, $postData['type'])->id;

			$avoir_id = $payment->avoir_id;
			$this->avoirHasPaymentModel->update($postData['id'], $postData);

			$invoice = $this->avoirModel->find($avoir_id);
			$payments = $this->avoirHasPaymentModel->where('id', $postData['id'])->findAll();
			$paymentSum = 0;

			foreach ($payments as $value) {
				$paymentSum += $value->amount;
			}
			$paymentsum = sprintf("%01.2f", round($paymentSum + $postData['amount'], 2));

			if ($invoice->paid <= $paymentsum + $invoice->paid) {
				$new_status = $this->config->item("occ_avoir_paye");
				$payment_date = $postData['date'];
			} else {
				$new_status = $this->config->item("occ_avoir_p_paye");
			}

			$this->avoirModel->update($invoice->id, ['status' => $new_status]);
			if (isset($payment_date)) {
				$this->avoirModel->update($invoice->id, ['paid_date' => $payment_date]);
			}

			session()->setFlashdata('message', $payment ? 'success:' . lang('messages_edit_payment_success') : 'error:' . lang('messages_edit_payment_error'));
			return redirect()->to('avoir/view/' . $postData['avoir_id']);
		} else {
			$payment = $this->avoirHasPaymentModel->find($id);
			$avoir = $this->avoirModel->find($payment->avoir_id);
			$data = [
				'payment' => $payment,
				'avoir' => $avoir,
				'typepaiement' => $this->referentiels->getReferentielsByIdType($this->idMoyensPaiement),
				'form_action' => 'avoir/payment_update',
				'title' => lang('application_add_payment')
			];
			return view('avoir/_payment', $data);
		}
	}

	public function paymentDelete($id = false, $avoir_id = false)
	{
		$payment = $this->avoirHasPaymentModel->find($id);
		$avoir = $this->avoirModel->find($avoir_id);
		$paid = $avoir->paid - $payment->amount;
		$outstanding = $avoir->outstanding + $payment->amount;

		$status = ($paid == 0) ? $this->config->item("occ_avoir_ouvert") : $this->config->item("occ_avoir_p_paye");

		$this->avoirModel->update($avoir_id, [
			'paid' => $paid,
			'outstanding' => $outstanding,
			'status' => $status
		]);

		if ($payment) {
			$this->avoirHasPaymentModel->delete($id);
			session()->setFlashdata('message', 'success:' . lang('messages_delete_payment_success'));
		} else {
			session()->setFlashdata('message', 'error:' . lang('messages_delete_payment_error'));
		}

		return redirect()->to('avoir/view/' . $avoir_id);
	}

	public function sendFiles($id)
	{
		$data = [
			'data' => $this->avoirModel->find($id),
			'type' => "avoir"
		];
		return view('settings/sendFile', $data);
	}
}
