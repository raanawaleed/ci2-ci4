<?php

namespace App\Controllers;

use App\Models\SubscriptionHasItemModel;
use App\Models\SubscriptionModel;
use App\Models\CompanyModel;
use App\Models\SettingModel;
use App\Models\FactureModel;
use App\Models\ItemsModel;

use App\Controllers\BaseController;

class SubscriptionsController extends BaseController
{

	protected $subscriptionModel;
	protected $companyModel;
	protected $settingModel;
	protected $invoiceModel;
	protected $SubscriptionHasItemModel;
	protected $itemModel;

	public function __construct()
	{
		$this->subscriptionModel = new SubscriptionModel();
		$this->companyModel = new CompanyModel();
		$this->settingModel = new SettingModel();
		$this->invoiceModel = new FactureModel();
		$this->itemModel = new ItemsModel();
		$this->SubscriptionHasItemModel = new SubscriptionHasItemModel();

		if (!session()->get('user') || session()->get('user')->admin !== 1) {
			return redirect()->to('login');
		}

		if (session()->get('client')) {
			return redirect()->to('cprojects');
		}

		$this->checkAccess();
	}

	private function checkAccess()
    {
        $access = array_filter($this->view_data['menu'], fn($item) => $item->link === "items") ||
                  array_filter($this->view_data['submenuRight'], fn($item) => $item->link === "items");

        if (!$access) {
            return redirect()->to('login');
        }
    }
	public function index()
    {
        $subscriptions = $this->user->admin === 1
            ? $this->subscriptionModel->findAll(['id_vcompanies' => session()->get('current_company')])
            : $this->getUserSubscriptions();

        foreach ($subscriptions as $val) {
            if ($val->company_id !== 0) {
                $val->company_id = $this->companyModel->find($val->company_id);
            }
        }

        $this->view_data['subscriptions'] = $subscriptions;
        return view('subscriptions/all', $this->view_data);
    }

	private function getUserSubscriptions()
    {
        $companyIds = array_map(fn($company) => $company->id, (array) $this->user->companies);
        return $this->subscriptionModel->whereIn('company_id', $companyIds)
            ->where('id_vcompanies', session()->get('current_company'))
            ->findAll();
    }
	public function filter(string $condition)
    {
        $conditions = ['status' => ucfirst($condition)];
        if ($this->user->admin !== 1) {
            if ($condition === "ended") {
                $conditions = ['status' => 'Active', 'end_date <' => date('Y-m-d')];
                $conditions['company_id IN'] = array_map(fn($company) => $company->id, (array) $this->user->companies);
            }
        }

        $this->view_data['subscriptions'] = $this->subscriptionModel->where($conditions)->findAll();
        return view('subscriptions/filter', $this->view_data);
    }
	public function create()
    {
        if ($this->request->getMethod() === 'post') {
            return $this->processCreate();
        }

        $this->loadCreateView();
        return view('subscriptions/_subscription', $this->view_data);
    }

	private function processCreate()
    {
        $data = $this->request->getPost();
        unset($data['send'], $data['_wysihtml5_mode'], $data['files']);

        $settings = $this->settingModel->find(['id_vcompanies' => session()->get('current_company')]);
        $data['next_payment'] = $data['issue_date'];
        $data['id_vcompanies'] = session()->get('current_company');
        $data['second_tax'] = $settings->tax;
        $data['creation_date'] = date("Y-m-d");

        $latestSubscription = $this->subscriptionModel->orderBy('creation_date', 'desc')->first();
        $data['reference'] = date('Y', strtotime($latestSubscription->creation_date)) !== date('Y') ? '01' : $data['reference'];

        $this->generateSubscriptionNumber($data, $settings);

        $subscription = $this->subscriptionModel->save($data);
        $this->updateSubscriptionReference($settings);

        session()->setFlashdata('message', $subscription ? 'success: Subscription created successfully' : 'error: Subscription creation failed');
        return redirect()->to('subscriptions');
    }
	private function generateSubscriptionNumber(array &$data, $settings): void
    {
        $subscriptionPieces = explode("-", strrev($settings->subscription_prefix));
        $issueDateParts = explode("-", date("y-m-d", strtotime($data['issue_date'])));
        
        if ($subscriptionPieces[0] === "YY") {
            $data['subscription_num'] = strrev($subscriptionPieces[1]) . substr($issueDateParts[0], -1) . $data['reference'];
        } else {
            $data['subscription_num'] = strrev($subscriptionPieces[2]) . substr($issueDateParts[0], -1) . $issueDateParts[1] . $data['reference'];
        }
    }

    private function updateSubscriptionReference($settings): void
    {
        $settings->subscription_reference += 1;
        $this->settingModel->save($settings);
    }

    private function loadCreateView(): void
    {
        $this->view_data['core_settings'] = $this->settingModel->find(['id_vcompanies' => session()->get('current_company')]);
        $this->view_data['companies'] = $this->companyModel->where('inactive', 0)->findAll();
        $this->view_data['currencies'] = $this->referentiels->getReferentielsByIdType($this->refType->getRefTypeByName("Devise")->id);
        $this->view_data['title'] = 'Create Subscription';
        $this->view_data['form_action'] = 'subscriptions/create';
    }
	public function update($id = null)
    {
        if ($this->request->getMethod() === 'post') {
            return $this->processUpdate($id);
        }

        return $this->loadUpdateView($id);
    }

	private function processUpdate($id): void
    {
        $data = $this->request->getPost();
        unset($data['send'], $data['files'], $data['_wysihtml5_mode']);

        $subscription = $this->subscriptionModel->find($id);
        if ($data['issue_date'] !== $subscription->issue_date) {
            $data['next_payment'] = $data['issue_date'];
        }
        if ($data['status'] === "Paid") {
            $data['paid_date'] = date('Y-m-d');
        }
        $this->subscriptionModel->update($id, $data);

        session()->setFlashdata('message', 'success: Subscription updated successfully');
        return redirect()->to('subscriptions');
    }

    private function loadUpdateView($id): string
    {
        $this->view_data['subscription'] = $this->subscriptionModel->find($id);
        $this->view_data['companies'] = $this->companyModel->where('inactive', 0)->findAll();
        $this->view_data['currencies'] = $this->referentiels->getReferentielsByIdType($this->refType->getRefTypeByName("Devise")->id);
        $this->view_data['title'] = 'Edit Subscription';
        $this->view_data['form_action'] = "subscriptions/update/$id";

        return view('subscriptions/_subscription', $this->view_data);
    }
	public function view($id)
    {
        $this->view_data['subscription'] = $this->subscriptionModel->find($id);
        $this->view_data['factures'] = $this->invoiceModel->getBySubscriptionId($id);
        $this->view_data['items'] = $this->SubscriptionHasItemModel->where('subscription_id', $id)->findAll();
        
        return view('subscriptions/view', $this->view_data);
    }
	public function createInvoice($id = null)
    {
        $subscription = $this->subscriptionModel->find($id);
        $invoiceReference = $this->settingModel->where('id_vcompanies', session()->get('current_company'))->first();

        if ($subscription) {
            $data = $this->prepareInvoiceData($subscription, $invoiceReference);

            // Save invoice and handle items
            $invoice = $this->invoiceModel->insert($data);
            $this->handleInvoiceItems($id, $invoice);

            // Update subscription next payment date
            $this->updateNextPayment($subscription);

            session()->setFlashdata('message', 'success: Invoice created successfully');
            return redirect()->to("subscriptions/view/$id");
        }

        session()->setFlashdata('message', 'error: Subscription not found');
        return redirect()->to('subscriptions');
    }

    private function prepareInvoiceData($subscription, $invoiceReference)
    {
        $data = [
            'subscription_id' => $subscription->id,
            'company_id' => $subscription->company_id,
            'currency' => $this->referentiels->getReferentielsById($subscription->currency)->name,
            'notes' => $invoiceReference->notes_facture,
            'id_vcompanies' => session()->get('current_company'),
            'issue_date' => $subscription->next_payment,
            'terms' => $subscription->terms,
            'discount' => $subscription->discount,
            'tax' => $subscription->second_tax,
            'status' => $this->getInvoiceStatus($subscription),
            'reference' => $this->getInvoiceReference($invoiceReference),
            'estimate_reference' => $this->getEstimateReference($invoiceReference),
            'estimate_num' => $this->generateEstimateNumber($invoiceReference, $subscription),
            'timbre_fiscal' => $invoiceReference->timbre_fiscal,
            'creation_date' => date("Y-m-d"),
        ];

        return $data;
    }

    private function getInvoiceStatus($subscription)
    {
        return $subscription->subscribed != 0 ? 'Paid' : 'Open';
    }

    private function getInvoiceReference($invoiceReference)
    {
        $lastInvoice = $this->invoiceModel->getLastInvoice();
        $year = date("Y");

        return ($lastInvoice->creation_date[0] != $year) ? 1 : $invoiceReference->invoice_reference;
    }

    private function getEstimateReference($invoiceReference)
    {
        $reference = $this->getInvoiceReference($invoiceReference);
        return str_pad($reference, 2, '0', STR_PAD_LEFT);
    }

    private function generateEstimateNumber($invoiceReference, $subscription)
    {
        $estimatePieces = explode("-", strrev($invoiceReference->invoice_prefix));
        $issueDateParts = explode("-", date("Y-m-d", strtotime($subscription->next_payment)));

        return $estimatePieces[0] == "YY"
            ? strrev($estimatePieces[1]) . substr($issueDateParts[0], -1) . $this->getEstimateReference($invoiceReference)
            : strrev($estimatePieces[2]) . substr($issueDateParts[0], -1) . $issueDateParts[1] . $this->getEstimateReference($invoiceReference);
    }

    private function handleInvoiceItems($subscriptionId, $invoiceId)
    {
        $items = $this->SubscriptionHasItemModel->where('subscription_id', $subscriptionId)->findAll();

        foreach ($items as $item) {
            $itemValues = [
                'facture_id' => $invoiceId,
                'item_id' => $item->item_id,
                'amount' => $item->amount,
                'description' => $item->description,
                'value' => $item->value,
                'name' => $item->name,
                'type' => $item->type,
                'tva' => $item->tva,
            ];
            $this->factureHasItemModel->insert($itemValues);
        }
    }

    private function updateNextPayment($subscription)
    {
        $subscription->next_payment = date('Y-m-d', strtotime($subscription->frequency, strtotime($subscription->next_payment)));
        $this->subscriptionModel->save($subscription);
    }
	public function delete($id = null)
    {
        $subscription = $this->subscriptionModel->find($id);

        if ($subscription) {
            $this->subscriptionModel->delete($id);
            session()->setFlashdata('message', 'success: Subscription deleted successfully');
        } else {
            session()->setFlashdata('message', 'error: Subscription not found');
        }

        return redirect()->to('subscriptions');
    }
	public function sendSubscription($id = null)
    {
        $data["subscription"] = $this->subscriptionModel->find($id);
        $data['items'] = $this->SubscriptionHasItemModel->where('subscription_id', $id)->findAll();
        $data["core_settings"] = $this->settingModel->where('id_vcompanies', session()->get('current_company'))->first();

        if (empty($data["subscription"]->company->client->email)) {
            session()->setFlashdata('message', 'error: No client email!');
            return redirect()->to("subscriptions/view/$id");
        }

        $this->sendEmail($data);
        redirect()->to("subscriptions/view/$id");
    }

	private function sendEmail($data)
    {
        $this->load->library('parser');
        
        // Set email parameters
        $subject = $this->parser->parse_string($data["core_settings"]->subscription_mail_subject, $this->getParseData($data));
        $this->email->from($data["core_settings"]->email, $data["core_settings"]->company);
        $this->email->to($data["subscription"]->company->client->email);
        $this->email->subject($subject);

        // Prepare email message
        $emailTemplate = read_file('./application/views/' . $data["core_settings"]->template . '/templates/email_subscription.html');
        $message = $this->parser->parse_string($emailTemplate, $this->getParseData($data));
        $this->email->message($message);

        if ($this->email->send()) {
            session()->setFlashdata('message', 'success: Subscription sent successfully');
        } else {
            session()->setFlashdata('message', 'error: Failed to send subscription');
        }
    }

	private function getParseData($data)
    {
        $issue_date = date($data["core_settings"]->date_format, human_to_unix($data["subscription"]->issue_date . ' 00:00:00'));

        return [
            'client_contact' => $data["subscription"]->company->client->firstname . ' ' . $data["subscription"]->company->client->lastname,
            'issue_date' => $issue_date,
            'subscription_id' => $data["core_settings"]->subscription_prefix . $data["subscription"]->reference,
            'client_link' => $data["core_settings"]->domain,
            'company' => $data["core_settings"]->company,
            'logo' => '<img src="' . base_url() . $data["core_settings"]->logo . '" alt="' . $data["core_settings"]->company . '"/>',
            'invoice_logo' => '<img src="' . base_url() . $data["core_settings"]->invoice_logo . '" alt="' . $data["core_settings"]->company . '"/>',
        ];
    }
	public function item($id = null)
    {
        if ($this->request->getMethod() === 'post') {
            return $this->handleItemPost($this->request->getPost());
        }

        return $this->loadItemView($id);
    }

    private function handleItemPost(array $postData)
    {
        unset($postData['send']);
        $postData = array_map('htmlspecialchars', $postData);

        if (empty($postData['name'])) {
            if ($postData['item_id'] === "-") {
                session()->setFlashdata('message', 'error: Item not found');
                return redirect()->to("subscriptions/view/{$postData['subscription_id']}");
            }

            $itemValue = $this->itemModel->find($postData['item_id']);
            $postData['name'] = $itemValue->name;
            $postData['type'] = $itemValue->type;
            $postData['value'] = $itemValue->value;
        }

        $this->SubscriptionHasItemModel->insert($postData);
        session()->setFlashdata('message', 'success: Item added successfully');
        return redirect()->to("subscriptions/view/{$postData['subscription_id']}");
    }

    private function loadItemView($id)
    {
        $subscription = $this->subscriptionModel->find($id);
        $subscription->currency = $this->referentiels->getReferentielsById($subscription->currency)->name;

        $data = [
            'subscription' => $subscription,
            'items' => $this->itemModel->where('inactive', '0')->findAll(),
            'tva' => $this->db->query('SELECT * FROM referentiels WHERE id_type=9 AND visible=1')->getResult(),
        ];

        return view('subscriptions/_item', $data);
    }
	public function itemUpdate($id = null)
    {
        if ($this->request->getMethod() === 'post') {
            return $this->handleItemUpdate($this->request->getPost());
        }

        return $this->loadItemUpdateView($id);
    }

    private function handleItemUpdate(array $postData)
    {
        unset($postData['send']);
        $postData = array_map('htmlspecialchars', $postData);
        $item = $this->SubscriptionHasItemModel->find($postData['id']);

        if ($item) {
            $item->update($postData);
            session()->setFlashdata('message', 'success: Item updated successfully');
        } else {
            session()->setFlashdata('message', 'error: Item not found');
        }

        return redirect()->to("subscriptions/view/{$postData['subscription_id']}");
    }

    private function loadItemUpdateView($id)
    {
        $subscriptionItem = $this->SubscriptionHasItemModel->find($id);
        $data = [
            'subscription_has_items' => $subscriptionItem,
            'title' => 'Edit Item',
            'form_action' => 'subscriptions/itemUpdate',
            'tva' => $this->db->query('SELECT * FROM referentiels WHERE id_type=9 AND visible=1')->getResult(),
        ];

        return view('subscriptions/_item', $data);
    }

    public function itemDelete($id = null, $subscriptionId = null)
    {
        $item = $this->SubscriptionHasItemModel->find($id);

        if ($item) {
            $this->SubscriptionHasItemModel->delete($id);
            session()->setFlashdata('message', 'success: Item deleted successfully');
        } else {
            session()->setFlashdata('message', 'error: Item not found');
        }

        return redirect()->to("subscriptions/view/$subscriptionId");
    }

}