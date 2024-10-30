<?php

namespace App\Controllers;

use App\Models\InvoiceModel;
use App\Models\InvoiceHasItemModel;
use App\Models\InvoiceHasPaymentModel;
use App\Models\SettingModel;
use App\Controllers\BaseController;

class CInvoicesController extends BaseController
{
	protected $client, $user, $view_data = [];
	private $invoiceModel;
	private $settingModel;
	private $invoiceHasItemModel;
	private $invoiceHasPaymentModel;
	function __construct()
	{
		$this->invoiceModel = new InvoiceModel();
		$this->settingModel = new SettingModel();
		$this->invoiceHasItemModel = new InvoiceHasItemModel();
		$this->invoiceHasPaymentModel = new InvoiceHasPaymentModel();

		$this->client = session()->get('client');
		$this->user = session()->get('user');

		if ($this->client) {
			$access = array_filter($this->client->menu, fn($value) => $value->link === "cinvoices");
			if (!$access) {
				return redirect()->to('login');
			}
		} elseif ($this->user) {
			return redirect()->to('invoices');
		} else {
			return redirect()->to('login');
		}

		$this->view_data['submenu'] = [
			lang('application_all_invoices') => 'cinvoices',
		];

	}
	public function index()
    {
        $this->view_data['invoices'] = $this->invoiceModel->where('company_id', $this->client->company->id)
            ->where('estimate', '!=', 1)
            ->where('issue_date', '<=', date('Y-m-d'))
            ->findAll();

        return view('invoices/client_views/all', $this->view_data);
    }

	public function view($id = null)
    {
        $this->view_data['submenu'] = [
            lang('application_back') => 'invoices',
        ];

        $invoice = $this->invoiceModel->find($id);
        if (!$invoice || $invoice->company_id != $this->client->company->id) {
            return redirect()->to('cinvoices');
        }

        $this->view_data['invoice'] = $invoice;
        $this->view_data['items'] = $invoice->invoice_has_items;

        // Calculate total
        $sum = array_reduce($this->view_data['items'], function ($carry, $item) {
            return $carry + ($item->amount * $item->value);
        }, 0);

        $discount = $this->calculateDiscount($invoice->discount, $sum);
        $sum -= $discount;

        $coreSettings = $this->settingModel->where('id_vcompanies', session()->get('current_company'))->first();
        $tax = $this->calculateTax($sum, $invoice->tax ?: $coreSettings->tax);
        $secondTax = $this->calculateTax($sum, $invoice->second_tax ?: $coreSettings->second_tax);
        $sum += $tax + $secondTax;

        $this->view_data['invoice']->sum = round($sum, 2);
        $this->view_data['invoice']->save();

        return view('invoices/client_views/view', $this->view_data);
    }

	private function calculateDiscount($discount, $sum)
    {
        if (str_ends_with($discount, '%')) {
            return round(($sum / 100) * rtrim($discount, '%'), 2);
        }
        return (float)$discount;
    }

	private function calculateTax($sum, $taxRate)
    {
        return round(($sum / 100) * $taxRate, 2);
    }

	public function download($id = null)
    {
        $this->load->helper(['dompdf', 'file']);
        $this->load->library('parser');

        $invoice = $this->invoiceModel->find($id);
        if ($invoice->company_id != $this->client->company->id) {
            return redirect()->to('cinvoices');
        }

        $coreSettings = $this->settingModel->where('id_vcompanies', session()->get('current_company'))->first();
        $dueDate = date($coreSettings->date_format, strtotime($invoice->due_date));

        $parseData = [
            'due_date' => $dueDate,
            'invoice_id' => $coreSettings->invoice_prefix . $invoice->reference,
            'client_link' => $coreSettings->domain,
            'company' => $coreSettings->company,
        ];

        $html = view($coreSettings->template . '/' . $coreSettings->invoice_pdf_template, compact('invoice'));
        $html = $this->parser->parseString($html, $parseData);
        $filename = lang('application_invoice') . '_' . $coreSettings->invoice_prefix . $invoice->reference;

        pdf_create($html, $filename, true);
    }

	public function banktransfer($id = null)
{
    $invoice = $this->invoiceModel->find($id);
    if (!$invoice || $invoice->company_id != $this->client->company->id) {
        return redirect()->to('cinvoices');
    }

    $this->view_data['invoice'] = $invoice;
    return view('invoices/client_views/bank_transfer', $this->view_data);
}
public function twocheckout($id = null)
{
    $invoice = $this->invoiceModel->find($id);
    if (!$invoice || $invoice->company_id != $this->client->company->id) {
        return redirect()->to('cinvoices');
    }

    // Assuming TwoCheckout SDK is loaded
    if ($this->request->getMethod() === 'post') {
        // Handle payment processing here...

        // Redirect on success or error
        return $this->handlePaymentResponse();
    }

    $this->view_data['invoice'] = $invoice;
    return view('invoices/client_views/twocheckout', $this->view_data);
}

public function stripepay($id = null)
{
    $invoice = $this->invoiceModel->find($id);
    if (!$invoice || $invoice->company_id != $this->client->company->id) {
        return redirect()->to('cinvoices');
    }

    if ($this->request->getMethod() === 'post') {
        // Handle Stripe payment processing here...

        // Redirect on success or error
        return $this->handlePaymentResponse();
    }

    $this->view_data['invoice'] = $invoice;
    return view('invoices/client_views/stripe', $this->view_data);
}

public function payumoney($id = null)
{
    $invoice = $this->invoiceModel->find($id);
    if (!$invoice || $invoice->company_id != $this->client->company->id) {
        return redirect()->to('cinvoices');
    }

    // Handle PayUmoney processing...
    if ($this->request->getMethod() === 'post') {
        // Process payment here...
        return $this->handlePaymentResponse();
    }

    $this->view_data['invoice'] = $invoice;
    return view('invoices/client_views/payumoney', $this->view_data);
}

private function handlePaymentResponse()
{
    // Logic to handle payment response (success/failure)
    $response = $this->request->getPost(); // Example of fetching response data
    $invoiceId = $response['invoice_id'] ?? null;

    if ($response['status'] === 'success') {
        session()->setFlashdata('message', 'success:' . lang('messages_payment_success'));
        return redirect()->to('cinvoices/view/' . $invoiceId);
    } else {
        session()->setFlashdata('message', 'error:' . lang('messages_payment_failed'));
        return redirect()->to('cinvoices/view/' . $invoiceId);
    }
}
	public function success($id = null)
    {
        session()->setFlashdata('message', 'success:' . lang('messages_payment_success'));
        return redirect()->to('cinvoices/view/' . $id);
    }

    public function authorizenet($id = null)
    {
        if ($this->request->getMethod() === 'post') {
            // Authorize.net logic here...
        } else {
            $this->view_data['invoice'] = $this->invoiceModel->find($id);
            $this->view_data['sum'] = sprintf("%01.2f", $this->view_data['invoice']->outstanding);
            return view('invoices/_authorizenet', $this->view_data);
        }
    }



}