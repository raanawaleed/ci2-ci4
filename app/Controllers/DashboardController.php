<?php

namespace App\Controllers;

use App\Models\RefTypeModel;
use App\Models\RefTypeOccurencesModel;
use App\Models\InvoiceModel;
use App\Models\ProjectModel;
use App\Models\TicketModel;
use App\Models\EventModel;
use App\Models\PrivatemessageModel;
use App\Models\SettingModel;
use App\Models\CompanyModel;
use App\Models\ProjectHasActivityModel;

use App\Controllers\BaseController;

class DashboardController extends BaseController
{
	protected $referentiels, $refType, $eventModel, $ticketModel;
	protected $db, $user, $companyModel, $projectHasActivity;
	protected $invoiceModel, $projectModel, $settingModel, $privateMessageModel, $view_data = [];
	function __construct()
	{
		$this->refType = new RefTypeModel();
		$this->eventModel = new EventModel();
		$this->ticketModel = new TicketModel();
		$this->companyModel = new CompanyModel();
		$this->invoiceModel = new InvoiceModel();
		$this->projectModel = new ProjectModel();
		$this->settingModel = new SettingModel();
		$this->referentiels = new RefTypeOccurencesModel();
		$this->privateMessageModel = new PrivatemessageModel();
		$this->projectHasActivity = new ProjectHasActivityModel();

		$this->db = \Config\Database::connect();
		$this->user = session()->get('user');

		if (!$this->user) {
			redirect('login');
		}

		//$this->db->query("SET GLOBAL sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))");
	}

	public function year(?string $year = null): void
	{
		$this->index($year);
	}

	public function index(?string $year = null): void
	{
		$year ??= date('Y'); // Default to current year if not provided
		$currentYearMonth = date('Y-m');
		$thismonth = date('m');

		// Prepare view data
		$this->view_data = [
			'month' => date('M'),
			'year' => $year,
			'stats' => $this->invoiceModel->getStatisticForYear($year),
			'stats_expenses' => $this->invoiceModel->getExpensesStatisticForYear($year),
			'payments' => $this->invoiceModel->paymentsForMonth($currentYearMonth),
			'paymentsOutstandingMonth' => $this->invoiceModel->outstandingPayments($currentYearMonth),
			'paymentsoutstanding' => $this->invoiceModel->outstandingPayments(),
			'totalExpenses' => $this->invoiceModel->totalExpensesForYear($year),
			'totalIncomeForYear' => $this->invoiceModel->totalIncomeForYear($year),
			'totalProfit' => $this->invoiceModel->totalIncomeForYear($year) - $this->invoiceModel->totalExpensesForYear($year),
		];

		// Calculate percentages
		$this->view_data['paymentsForThisMonthInPercent'] = $this->calculatePercentage($this->view_data['payments'], $this->view_data['paymentsOutstandingMonth']);
		$this->view_data['openProjectsPercent'] = isset($this->view_data['projects_open'], $this->view_data['projects_all'])
			? $this->calculatePercentage($this->view_data['projects_open'], $this->view_data['projects_all'])
			: 0;

		$this->view_data['openInvoicePercent'] = isset($this->view_data['invoices_open'], $this->view_data['invoices_all'])
			? $this->calculatePercentage($this->view_data['invoices_open'], $this->view_data['invoices_all'])
			: 0;
		$this->view_data['paymentsOutstandingPercent'] = min($this->calculatePercentage($this->view_data['paymentsoutstanding'], $this->view_data['totalIncomeForYear']), 100);

		// Generate month data for graphing
		$this->generateMonthlyData($year, $thismonth);

		// Load additional data for view
		$this->loadAdditionalData();

		$this->view_data['content_view'] = 'dashboard/dashboardV2';
	}

	private function calculatePercentage(float $part, float $whole): float
	{
		return $whole ? round(($part / $whole) * 100) : 0;
	}

	private function generateMonthlyData(string $year, string $currentMonth): void
	{
		$labels = [];
		$line1 = [];
		$line2 = [];
		$untilMonth = ($year === date('Y')) ? (int) $currentMonth : 12;

		for ($i = 1; $i <= $untilMonth; $i++) {
			$monthname = date('M', strtotime("{$year}-{$i}-01"));
			$monthname = lang("application_{$monthname}");

			$num = $this->getMonthlySummary($this->view_data['stats'], $i);
			$num2 = $this->getMonthlySummary($this->view_data['stats_expenses'], $i);

			$labels[] = $monthname;
			$line1[] = sprintf("%02.2d", $num);
			$line2[] = sprintf("%02.2d", $num2);
		}

		$this->view_data['labels'] = implode(',', $labels);
		$this->view_data['line1'] = implode(',', $line1);
		$this->view_data['line2'] = implode(',', $line2);
	}

	private function getMonthlySummary(array $stats, int $month): int
	{
		foreach ($stats as $value) {
			if (date('m', strtotime($value->paid_date)) == sprintf('%02d', $month)) {
				return $value->summary;
			}
		}
		return 0;
	}

	private function loadAdditionalData(): void
	{
		// Load tickets, clients, recent activities, etc.
		$projects = $this->projectModel->all();
		$this->view_data['ticket'] = $this->ticketModel->where('status !=', 'closed')->limit(5)->findAll();
		$this->view_data['ticketcounter'] = $this->ticketModel->where('status !=', 'closed')->countAllResults();
		$this->view_data['clientcounter'] = $this->companyModel->where('inactive', '0')->countAllResults();
		$this->view_data['recent_activities'] = $this->projectHasActivity->orderBy('datetime', 'desc')->limit(10)->findAll();

		// Prepare project and event data
		$this->prepareEventData($projects);
	}

	private function prepareEventData(array $projects): void
	{
		$project_events = [];
		foreach ($projects as $value) {
			$project_events[] = [
				'title' => lang('application_project') . ': ' . addslashes($value->name),
				'start' => $value->start,
				'end' => $value->end . 'T23:59:00',
				'url' => base_url("projects/view/{$value->id}"),
				'className' => 'project-event',
				'description' => addslashes(preg_replace("/\r|\n/", "", $value->description)),
			];
		}
		$this->view_data['project_events'] = json_encode($project_events);

		// Load events
		$this->view_data['events_list'] = json_encode($this->eventModel->all());
	}

	public function countItem(): void
	{
		$idType = $this->refType->getRefTypeByName("ticket")->id;
		$idClosed = $this->referentiels->getReferentiels($idType, "closed")->id;

		$options = [
			'conditions' => [
				'status != ? AND company_id = ? AND new_created = ? AND deleted = 0 AND collaborater_id = ?',
				$idClosed,
				$_SESSION['current_company'],
				1,
				$this->user->id,
			]
		];

		$tickets = $this->ticketModel->where($options['conditions'])->findAll();
		echo json_encode(!empty($tickets) ? count($tickets) : false);
		exit();
	}

	public function countMessage(): void
	{
		$messages = $this->privateMessageModel->where('new_created = ? AND recipient = ?', [1, $this->user->id])->findAll();
		echo json_encode(!empty($messages) ? count($messages) : false);
		exit();
	}
}
