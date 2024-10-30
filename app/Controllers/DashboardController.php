<?php

namespace App\Controllers;

use App\Models\{
	RefTypeModel,
	RefTypeOccurencesModel,
	InvoiceModel,
	ProjectModel,
	TicketModel,
	EventModel,
	PrivatemessageModel,
	SettingModel,
	CompanyModel,
	ProjectHasActivityModel
};

use App\Controllers\BaseController;

class DashboardController extends BaseController
{
	protected $models = [];
	protected $view_data = [];
	protected $user;

	public function __construct()
	{
		// Initialize models
		$this->models['refType'] = new RefTypeModel();
		$this->models['event'] = new EventModel();
		$this->models['ticket'] = new TicketModel();
		$this->models['company'] = new CompanyModel();
		$this->models['invoice'] = new InvoiceModel();
		$this->models['project'] = new ProjectModel();
		$this->models['setting'] = new SettingModel();
		$this->models['referentiels'] = new RefTypeOccurencesModel();
		$this->models['privateMessage'] = new PrivatemessageModel();
		$this->models['projectHasActivity'] = new ProjectHasActivityModel();

		$this->user = session()->get('user');

		if (!$this->user) {
			return redirect('login');
		}
	}

	public function year(?string $year = null): void
	{
		$this->index($year);
	}

	public function index(?string $year = null)
	{
		$year = $year ?? date('Y'); // Default to current year if not provided
		$currentYearMonth = date('Y-m');
		$currentMonth = date('m');

		// Prepare view data
		$this->view_data = [
			'month' => date('M'),
			'year' => $year,
			'stats' => $this->models['invoice']->getStatisticForYear($year),
			'stats_expenses' => $this->models['invoice']->getExpensesStatisticForYear($year),
			'payments' => $this->models['invoice']->paymentsForMonth($currentYearMonth),
			'paymentsOutstandingMonth' => $this->models['invoice']->outstandingPayments($currentYearMonth),
			'paymentsOutstanding' => $this->models['invoice']->outstandingPayments(),
			'totalExpenses' => $this->models['invoice']->totalExpensesForYear($year),
			'totalIncomeForYear' => $this->models['invoice']->totalIncomeForYear($year),
		];

		// Calculate totals and percentages
		$this->view_data['totalProfit'] = $this->view_data['totalIncomeForYear'] - $this->view_data['totalExpenses'];
		$this->calculatePercentages();

		// Generate monthly data for graphing
		$this->generateMonthlyData($year, $currentMonth);

		// Load additional data for view
		$this->loadAdditionalData();

		return view('blueline/dashboard/dashboardv2', $this->view_data);
	}

	private function calculatePercentages(): void
	{
		$this->view_data['paymentsForThisMonthInPercent'] =
			$this->calculatePercentage($this->view_data['payments'], $this->view_data['paymentsOutstandingMonth']);
		$this->view_data['openProjectsPercent'] =
			$this->calculatePercentage($this->view_data['projects_open'] ?? 0, $this->view_data['projects_all'] ?? 1);
		$this->view_data['openInvoicePercent'] =
			$this->calculatePercentage($this->view_data['invoices_open'] ?? 0, $this->view_data['invoices_all'] ?? 1);
		$this->view_data['paymentsOutstandingPercent'] =
			min($this->calculatePercentage($this->view_data['paymentsOutstanding'], $this->view_data['totalIncomeForYear']), 100);
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
			$monthName = lang("application_" . date('M', strtotime("{$year}-{$i}-01")));
			$num = $this->getMonthlySummary($this->view_data['stats'], $i);
			$num2 = $this->getMonthlySummary($this->view_data['stats_expenses'], $i);

			$labels[] = $monthName;
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
		$this->view_data['ticket'] = $this->models['ticket']->where('status !=', 'closed')->limit(5)->findAll();
		$this->view_data['ticketCounter'] = $this->models['ticket']->where('status !=', 'closed')->countAllResults();
		$this->view_data['clientCounter'] = $this->models['company']->where('inactive', '0')->countAllResults();
		$this->view_data['recentActivities'] = $this->models['projectHasActivity']->orderBy('datetime', 'desc')->limit(10)->findAll();

		// Prepare project and event data
		$this->prepareEventData($this->models['project']->getAll());
	}

	private function prepareEventData(array $projects): void
	{
		$projectEvents = array_map(function ($value) {
			return [
				'title' => lang('application_project') . ': ' . addslashes($value->name),
				'start' => $value->start,
				'end' => $value->end . 'T23:59:00',
				'url' => base_url("projects/view/{$value->id}"),
				'className' => 'project-event',
				'description' => addslashes(preg_replace("/\r|\n/", "", $value->description)),
			];
		}, $projects);

		$this->view_data['project_events'] = json_encode($projectEvents);
		$this->view_data['events_list'] = json_encode($this->models['event']->all());
	}

	public function countItem(): void
	{
		$idType = $this->models['refType']->getRefTypeByName("ticket")->id;
		$idClosed = $this->models['referentiels']->getReferentiels($idType, "closed")->id;

		$ticketCount = $this->models['ticket']->where([
			'status !=' => $idClosed,
			'company_id' => $_SESSION['current_company'],
			'new_created' => 1,
			'deleted' => 0,
			'collaborater_id' => $this->user->id,
		])->countAllResults();

		echo json_encode($ticketCount > 0 ? $ticketCount : false);
		exit();
	}

	public function countMessage(): void
	{
		$messageCount = $this->models['privateMessage']
			->where('new_created = ? AND recipient = ?', [1, $this->user->id])
			->countAllResults();

		echo json_encode($messageCount > 0 ? $messageCount : false);
		exit();
	}
}
