<?php

namespace App\Controllers;

use App\Models\InvoiceModel;
use App\Models\SettingModel;
use App\Models\InvoiceHasItemModel;

use App\Controllers\BaseController;
class CEstimatesController extends BaseController
{

	protected $view_data = [];

	private $invoiceModel;
	private $settingModel;
	private $invoiceHasItemModel;

	public function __construct()
	{
		$this->invoiceModel = new InvoiceModel();
		$this->settingModel = new SettingModel();
		$this->invoiceHasItemModel = new InvoiceHasItemModel();

		// Load necessary helpers and libraries
		helper(['form', 'notification']);

		// Check access permissions
		if (!$this->checkAccess()) {
			return redirect()->to('login');
		}

		// Initialize submenu
		$this->view_data['submenu'] = [
			lang('application_all') => 'cestimates',
		];
	}

	private function checkAccess(): bool
	{
		if ($this->client) {
			return in_array("cestimates", array_column($this->view_data['menu'], 'link'));
		} elseif ($this->user) {
			return redirect()->to('estimates');
		}

		return false;
	}

	public function index()
	{
		$this->view_data['estimates'] = $this->invoiceModel->where('estimate != ?', 0)
			->where('company_id', $this->client->company_id)
			->where('estimate_status != ?', 'Open')
			->findAll();

		return view('estimates/client_views/all', $this->view_data);
	}
	public function filter(string $condition = null)
	{
		$conditions = [
			'open' => 'Open',
			'sent' => 'Sent',
			'accepted' => 'Accepted',
			'declined' => 'Declined',
			'invoiced' => 'Invoiced',
		];

		$status = $conditions[$condition] ?? null;

		$this->view_data['estimates'] = $this->invoiceModel->where('estimate != ?', 0)
			->where('company_id', $this->client->company_id)
			->when($status, function ($query) use ($status) {
				return $query->where('estimate_status', $status);
			})
			->findAll();

		return view('estimates/client_views/all', $this->view_data);
	}


	public function accept(int $id)
	{
		$estimate = $this->invoiceModel->find($id);
		if (!$estimate)
			return redirect()->to('cestimates');

		$estimate->estimate_status = "Accepted";
		$estimate->estimate_accepted_date = date("Y-m-d");
		$estimate->save();

		$coreSettings = $this->getCoreSettings();
		send_notification(
			$coreSettings->email,
			$coreSettings->estimate_prefix . $estimate->estimate_reference . ' - ' . lang('application_Accepted'),
			lang('messages_estimate_accepted')
		);

		return redirect()->to("cestimates/view/$id");
	}

	public function decline(int $id = null)
	{
		if ($this->request->getMethod() === 'post') {
			$invoiceId = $this->request->getPost('invoice_id');
			$estimate = $this->invoiceModel->find($invoiceId);
			if (!$estimate)
				return redirect()->to('cestimates');

			$estimate->estimate_status = "Declined";
			$estimate->save();

			$coreSettings = $this->getCoreSettings();
			send_notification(
				$coreSettings->email,
				$coreSettings->estimate_prefix . $estimate->estimate_reference . ' - ' . lang('application_Declined'),
				$this->request->getPost('reason')
			);

			return redirect()->to("cestimates/view/$invoiceId");
		}

		$this->view_data['estimate'] = $this->invoiceModel->find($id);
		$this->view_data['title'] = lang('application_Declined');
		$this->view_data['form_action'] = 'cestimates/decline';

		return view('estimates/client_views/_decline', $this->view_data);
	}
	public function view(int $id)
	{
		$estimate = $this->invoiceModel->find($id);
		if (!$estimate || $estimate->company_id != $this->client->company->id) {
			return redirect()->to('cestimates');
		}

		$this->view_data['estimate'] = $estimate;
		$this->view_data['items'] = $this->invoiceHasItemModel->where('invoice_id', $id)->findAll();
		$this->calculateSum($estimate);

		return view('estimates/client_views/view', $this->view_data);
	}

	private function calculateSum(InvoiceModel $estimate): void
	{
		$sum = array_reduce($this->view_data['items'], function ($carry, $item) {
			return $carry + ($item->amount * $item->value);
		}, 0);

		$discount = $estimate->discount;
		$sum -= $this->calculateDiscount($sum, $discount);

		$taxValue = $estimate->tax ?: $this->getCoreSettings()->tax;
		$tax = ($sum / 100) * $taxValue;
		$estimate->sum = round($sum + $tax, 2);
		$estimate->save();
	}

	private function calculateDiscount(float $sum, string $discount): float
	{
		return substr($discount, -1) === "%"
			? round(($sum / 100) * substr($discount, 0, -1), 2)
			: (float) $discount;
	}
	public function preview(int $id)
	{
		helper(['dompdf', 'file']);
		$this->load->library('parser');

		$estimate = $this->invoiceModel->find($id);
		$items = $this->invoiceHasItemModel->where('invoice_id', $id)->findAll();
		$coreSettings = $this->getCoreSettings();

		$data = [
			"estimate" => $estimate,
			"items" => $items,
			"core_settings" => $coreSettings,
		];

		$dueDate = date($coreSettings->date_format, strtotime($estimate->due_date));
		$parseData = [
			'due_date' => $dueDate,
			'estimate_id' => $coreSettings->estimate_prefix . $estimate->estimate_reference,
			'client_link' => $coreSettings->domain,
			'company' => $coreSettings->company,
		];

		$html = view($coreSettings->template . '/' . $coreSettings->estimate_pdf_template, $data);
		$html = $this->parser->parse_string($html, $parseData);

		$filename = lang('application_estimate') . '_' . $coreSettings->estimate_prefix . $estimate->estimate_reference;
		pdf_create($html, $filename, true);
	}

	private function getCoreSettings()
	{
		return $this->settingModel->where('id_vcompanies', $_SESSION['current_company'])->first();
	}

}