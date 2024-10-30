<?php

namespace App\Controllers;

use App\Controllers\BaseController;

use App\Models\InvoiceModel;
use App\Models\SettingModel;
use App\Models\PrivatemessageModel;
use App\Models\ProjectHasTaskModel;
use App\Models\CompanyModel;

class ReportsController extends BaseController
{
	protected $view_data = [];
	private InvoiceModel $invoiceModel;
	private SettingModel $settingModel;
	private PrivatemessageModel $privateMessageModel;
	private ProjectHasTaskModel $projectHasTaskModel;
	private CompanyModel $companyModel;
	function __construct()
	{

		$this->invoiceModel = new InvoiceModel();
		$this->settingModel = new SettingModel();
		$this->privateMessageModel = new PrivatemessageModel();
		$this->projectHasTaskModel = new ProjectHasTaskModel();
		$this->companyModel = new CompanyModel();

		$this->initializeAccess();
		$this->loadEvents();

	}

	private function initializeAccess(): void
	{
		if ($this->session->get('client')) {
			return redirect()->to('cprojects');
		}

		if ($this->session->get('user')) {
			if (!in_array("reports", $this->session->get('module_permissions'))) {
				return redirect()->to($this->session->get('menu')[0]->link ?? 'login');
			}
		} else {
			return redirect()->to('login');
		}
	}

	private function loadEvents(): void
	{
		if (in_array("messages", $this->session->get('module_permissions'))) {
			$this->view_data["message"] = $this->privateMessageModel->getRecentMessages($this->session->get('user')->id);
		}

		if (in_array("projects", $this->session->get('module_permissions'))) {
			$this->view_data["tasks"] = $this->projectHasTaskModel->getUserTasks($this->session->get('user')->id);
		}
	}
	public function period(): void
	{
		$report = $this->request->getPost('report');
		$start = $this->request->getPost('start');
		$end = $this->request->getPost('end');

		if ($report === "clients") {
			$this->incomeByClients($start, $end);
		} else {
			$this->index($start, $end);
		}
	}

	public function index(string $start = null, string $end = null): void
	{
		$start = $start ?: date('Y-01-01');
		$end = $end ?: date('Y-12-31');

		$coreSettings = $this->settingModel->getCompanySettings($this->session->get('current_company'));
		$this->view_data["stats"] = $this->invoiceModel->getStatisticFor($start, $end);
		$this->view_data["stats_expenses"] = $this->invoiceModel->getExpensesStatisticFor($start, $end);

		$this->prepareStatisticsView($start, $end, $coreSettings);
		$this->view_data['form_action'] = 'reports/period';
		$this->content_view = 'reports/reports';
	}

	private function prepareStatisticsView(string $start, string $end, object $coreSettings): void
	{
		// Process and prepare view data for statistics
		$this->view_data["stats_start"] = date($coreSettings->date_format, strtotime($start));
		$this->view_data["stats_end"] = date($coreSettings->date_format, strtotime($end));
		// Additional processing...
	}

	public function incomeByClients(string $start = null, string $end = null): void
	{
		$start = $start ?: date('Y-01-01');
		$end = $end ?: date('Y-12-31');

		$this->view_data["stats"] = $this->invoiceModel->getStatisticForClients($start, $end);
		$this->view_data['form_action'] = 'reports/period';
		$this->content_view = 'reports/reports';
	}

}


