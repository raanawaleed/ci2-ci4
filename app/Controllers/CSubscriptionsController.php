<?php

namespace App\Controllers;

use App\Models\SubscriptionModel;
use App\Models\SubscriptionHasItemModel;
use App\Controllers\BaseController;

class CSubscriptionsController extends BaseController
{

	protected $client, $user, $content_view;
	protected $view_data = [];

	protected $subscription_model, $subscription_has_item_model;

	function __construct()
	{
		// Initialize the client and user properties
		$this->client = session()->get('client');
		$this->user = session()->get('user');

		$this->subscription_model = new SubscriptionModel();
		$this->subscription_has_item_model = new SubscriptionHasItemModel();

		$this->checkAccess();
		$this->setSubmenu();
	}

	private function checkAccess(): void
	{
		if ($this->user) {
			return redirect()->to('subscriptions');
		} elseif ($this->client && !$this->hasAccessToCSubscriptions()) {
			return redirect()->to('login');
		} elseif (!$this->client) {
			return redirect()->to('login');
		}
	}

	private function hasAccessToCSubscriptions(): bool
	{
		foreach ($this->view_data['menu'] as $item) {
			if ($item->link === "csubscriptions") {
				return true;
			}
		}
		return false;
	}

	private function setSubmenu(): void
	{
		$this->view_data['submenu'] = [
			lang('application.application_all') => 'csubscriptions',
			lang('application.application_Active') => 'csubscriptions/filter/active',
			lang('application.application_Inactive') => 'csubscriptions/filter/inactive',
		];
	}

	public function index(): void
	{
		$this->view_data['subscriptions'] = $this->subscription_model->where('status', 'Active')
			->where('company_id', $this->client->company->id)
			->findAll();
		$this->content_view = 'subscriptions/client_views/all';

		return view($this->content_view, $this->view_data);
	}
	public function filter(string $condition = null): void
	{
		$query = $this->subscription_model->where('company_id', $this->client->company->id);

		if ($condition === 'active') {
			$query->where('status', 'Active');
		} elseif ($condition === 'inactive') {
			$query->where('status', 'Inactive');
		}

		$this->view_data['subscriptions'] = $query->findAll();
		$this->content_view = 'subscriptions/client_views/all';

		return view($this->content_view, $this->view_data);
	}
	public function view(int $id): void
	{
		$this->view_data['submenu'] = [
			lang('application.application_back') => 'subscriptions',
		];

		$this->view_data['subscription'] = $this->subscription_model->find($id);
		$this->view_data['items'] = $this->subscription_has_item_model->where('subscription_id', $id)->findAll();

		if ($this->view_data['subscription']->company_id !== $this->client->company->id) {
			return redirect()->to('csubscriptions');
		}

		$this->calculateRunTime($this->view_data['subscription']);
		$this->content_view = 'subscriptions/client_views/view';

		return view($this->content_view, $this->view_data);
	}

	private function calculateRunTime($subscription): void
	{
		$datediff = strtotime($subscription->end_date) - strtotime($subscription->issue_date);
		$timespan = floor($datediff / (60 * 60 * 24));

		$frequencyMapping = [
			'+7 day' => [7, 'W', 1],
			'+14 day' => [14, 'W', 2],
			'+1 month' => [30, 'M', 1],
			'+3 month' => [90, 'M', 3],
			'+6 month' => [182, 'M', 6],
			'+1 year' => [365, 'Y', 1],
		];

		if (array_key_exists($subscription->frequency, $frequencyMapping)) {
			[$days, $unit, $period] = $frequencyMapping[$subscription->frequency];
			$this->view_data['run_time'] = round($timespan / $days);
			$this->view_data['p3'] = (string) $period;
			$this->view_data['t3'] = $unit;
		}
	}

	public function success(int $id): void
	{
		session()->setFlashdata('message', 'success:' . lang('messages.messages_subscribe_success'));
		return redirect()->to("csubscriptions/view/$id");
	}

}