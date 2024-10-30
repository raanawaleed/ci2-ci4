<?php

namespace App\Controllers;

use App\Models\RefTypeModel;
use App\Models\RefTypeOccurencesModel;
use App\Models\ExpenseModel;
use App\Models\UserModel;
use App\Models\ProjectModel;
use App\Models\CompanyModel;
use App\Models\SettingModel;

class ExpensesController extends BaseController
{
	protected $refTypeModel;
	protected $referentielsModel;
	protected $expenseModel;
	protected $userModel;
	protected $projectModel;
	protected $companyModel;
	protected $settingModel;
	protected $view_data = [];
	protected $theme_view, $db;

	public function __construct()
	{
		$this->refTypeModel = new RefTypeModel();
		$this->referentielsModel = new RefTypeOccurencesModel();
		$this->expenseModel = new ExpenseModel();
		$this->userModel = new UserModel();
		$this->projectModel = new ProjectModel();
		$this->companyModel = new CompanyModel();
		$this->settingModel = new SettingModel();

		$this->checkAccess();

		$this->view_data['submenu'] = [
			lang('application_all') => 'expenses',
			lang('application_open') => 'expenses/filter/open',
			lang('application_sent') => 'expenses/filter/sent',
			lang('application_paid') => 'expenses/filter/paid',
		];
	}

	protected function checkAccess()
	{
		if (session()->get('user')) {
			return redirect('cprojects');
		}

		if (session()->get('user')) {
			$access = $this->hasAccessToItems();
			if (!$access) {
				return redirect('login');
			}
		} else {
			return redirect('login');
		}
	}

	protected function hasAccessToItems(): bool
	{
		foreach ($this->view_data['menu'] as $value) {
			if ($value->link == "items") {
				return true;
			}
		}

		foreach ($this->view_data['submenuRight'] as $value) {
			if ($value->link == "items") {
				return true;
			}
		}

		return false;
	}

	public function index()
	{
		$this->view_data['userlist'] = $this->userModel->where('status', 'active')->findAll();
		$this->view_data['user_id'] = 0;
		$this->view_data['year'] = date("Y");
		$this->view_data['month'] = 0;

		$year = date("Y");
		$this->view_data['days_in_this_month'] = 12;
		$this->view_data['expenses_this_month'] = $this->expenseModel->where('date >=', "$year-01-01")
			->where('date <=', "$year-12-31")
			->countAllResults();

		$this->view_data['expenses_owed_this_month'] = $this->expenseModel->selectSum('value', 'owed')
			->where('date >=', "$year-01-01")
			->where('date <=', "$year-12-31")
			->first();

		$this->view_data['expenses_due_this_month_graph'] = $this->expenseModel->select('SUM(value) AS owed, MONTH(date) AS date')
			->where('date >=', "$year-01-01")
			->where('date <=', "$year-12-31")
			->groupBy('MONTH(date)')
			->findAll();

		$this->view_data['expenses'] = $this->expenseModel->where('date >=', "$year-01-01")
			->where('date <=', "$year-12-31")
			->findAll();

		$this->content_view = 'expenses/all';
	}

	public function filter($userid = null, $year = null, $month = null)
	{
		$this->view_data['userlist'] = $this->userModel->where('status', 'active')->findAll();
		$this->view_data['username'] = $this->userModel->find($userid);
		$this->view_data['user_id'] = $userid;
		$this->view_data['year'] = $year;
		$this->view_data['month'] = $month;

		$search = [];
		$stats_search = '';

		if ($userid) {
			$search[] = "user_id = $userid";
			$stats_search = " AND user_id = $userid ";
		}

		if ($month && $year) {
			$search[] = "date >= '$year-$month-01' AND date <= '$year-$month-31'";
		} else {
			$search[] = "date >= '$year-01-01' AND date <= '$year-12-31'";
		}

		$this->calculateMonthlyExpenses($year, $month, $stats_search);
		$this->view_data['expenses'] = $this->expenseModel->where(implode(' AND ', $search))->findAll();
		$this->content_view = 'expenses/all';
	}

	protected function calculateMonthlyExpenses($year, $month, $stats_search)
	{
		$graph_month = $month != 0 ? $month : date('m');
		$days_in_this_month = days_in_month($graph_month, $year);
		$lastday_in_month = strtotime("$year-$graph_month-$days_in_this_month");
		$firstday_in_month = strtotime("$year-$graph_month-01");

		$this->view_data['days_in_this_month'] = $days_in_this_month;
		$this->view_data['expenses_this_month'] = $this->expenseModel->where('UNIX_TIMESTAMP(`date`) <=', $lastday_in_month)
			->where('UNIX_TIMESTAMP(`date`) >=', $firstday_in_month . $stats_search)
			->countAllResults();

		$this->view_data['expenses_owed_this_month'] = $this->expenseModel->selectSum('value', 'owed')
			->where('UNIX_TIMESTAMP(`date`) >=', $firstday_in_month)
			->where('UNIX_TIMESTAMP(`date`) <=', $lastday_in_month . $stats_search)
			->first();

		$this->view_data['expenses_due_this_month_graph'] = $this->expenseModel->select('SUM(value) AS owed, date')
			->where('UNIX_TIMESTAMP(`date`) >=', $firstday_in_month)
			->where('UNIX_TIMESTAMP(`date`) <=', $lastday_in_month . $stats_search)
			->groupBy('date')
			->findAll();
	}

	public function create()
	{
		if ($this->request->getMethod() === 'post') {
			$data = $this->request->getPost();
			unset($data['send'], $data['_wysihtml5_mode'], $data['files']);
			$data['id_vcompanies'] = session('current_company');

			$this->uploadFile($data);

			$expense = $this->expenseModel->insert($data);
			if (!$expense) {
				session()->setFlashdata('message', 'error: ' . lang('messages_create_expense_error'));
			} else {
				session()->setFlashdata('message', 'success: ' . lang('messages_create_expense_success'));
			}
			return redirect('expenses');
		} else {
			$this->prepareCreateView();
		}
	}

	protected function uploadFile(array &$data): void
	{
		$config = [
			'upload_path' => './files/media/',
			'encrypt_name' => true,
			'allowed_types' => '*'
		];

		$this->load->library('upload', $config);
		if ($this->upload->do_upload()) {
			$upload_data = $this->upload->data();
			if (empty($data['attachment_description'])) {
				$data['attachment_description'] = $upload_data['orig_name'];
			}
			$data['attachment'] = $upload_data['file_name'];
		}
	}

	protected function prepareCreateView(): void
	{
		$this->view_data['expenses'] = $this->expenseModel->findAll();
		$this->view_data['next_reference'] = $this->expenseModel->orderBy('id', 'desc')->first();
		if ($this->user->admin != 1) {
			$this->view_data['projects'] = $this->user->projects;
			$this->view_data['companies'] = $this->user->companies;
		} else {
			$this->view_data['projects'] = $this->projectModel->findAll();
			$this->view_data['companies'] = $this->companyModel->where('inactive', '0')->findAll();
		}
		$this->view_data['core_settings'] = $this->settingModel->where('id_vcompanies', session('current_company'))->findAll();
		$this->view_data['currencys'] = $this->referentielsModel->getReferentielsByIdType($this->refTypeModel->getRefTypeByName("Devise")->id);
		$this->view_data['title'] = lang('application_create_expense');
		$this->view_data['form_action'] = 'expenses/create';
		$this->theme_view = 'modal';
		$this->content_view = 'expenses/_expense';
	}

	public function update($id = null)
	{
		if ($this->request->getMethod() === 'post') {
			$data = $this->request->getPost();
			unset($data['send'], $data['_wysihtml5_mode'], $data['files']);
			$data['id_vcompanies'] = session('current_company');

			$this->uploadFile($data);

			$this->expenseModel->update($id, $data);
			session()->setFlashdata('message', 'success: ' . lang('messages_update_expense_success'));

			return redirect('expenses');
		} else {
			$this->prepareUpdateView($id);
		}
	}

	protected function prepareUpdateView($id): void
	{
		$this->view_data['item'] = $this->expenseModel->find($id);
		$this->view_data['expenses'] = $this->expenseModel->findAll();
		$this->view_data['next_reference'] = $this->expenseModel->orderBy('id', 'desc')->first();
		if ($this->user->admin != 1) {
			$this->view_data['projects'] = $this->user->projects;
			$this->view_data['companies'] = $this->user->companies;
		} else {
			$this->view_data['projects'] = $this->projectModel->findAll();
			$this->view_data['companies'] = $this->companyModel->where('inactive', '0')->findAll();
		}
		$this->view_data['core_settings'] = $this->settingModel->where('id_vcompanies', session('current_company'))->findAll();
		$this->view_data['currencys'] = $this->referentielsModel->getReferentielsByIdType($this->refTypeModel->getRefTypeByName("Devise")->id);
		$this->view_data['title'] = lang('application_edit_expense');
		$this->view_data['form_action'] = "expenses/update/$id";
		$this->theme_view = 'modal';
		$this->content_view = 'expenses/_expense';
	}

	public function delete($id)
	{
		$this->expenseModel->delete($id);
		session()->setFlashdata('message', 'success: ' . lang('messages_delete_expense_success'));
		return redirect('expenses');
	}
}
