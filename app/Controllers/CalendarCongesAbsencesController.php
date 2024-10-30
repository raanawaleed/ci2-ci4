<?php

namespace App\Controllers;


use App\Models\RefTypeOccurencesModel;
use App\Models\SalariesModel;
use App\Models\CongesModel;

use App\Controllers\BaseController;

class CalendarCongesAbsencesController extends BaseController
{

	private RefTypeOccurencesModel $referentiels;
	private SalariesModel $salariesModel;
	private CongesModel $congesModel;

	public function __construct()
	{
		// Removed parent constructor call, ensure any necessary initializations are handled here
		$this->referentiels = model('Ref_type_occurences_model');

		$this->handleRedirects();
		$this->setSubmenu();
		$this->loadDatabase();
	}

	private function handleRedirects(): void
	{
		if ($this->client) {
			$link = $this->request->getCookie('fc2_link');
			return $link ? redirect(str_replace("/tickets/", "/ctickets/", $link)) : redirect('cprojects');
		}

		if (!$this->user) {
			return redirect('login');
		}

		if (!$this->hasAccessToCalendar()) {
			return $this->handleNoAccess();
		}
	}

	private function hasAccessToCalendar(): bool
	{
		foreach ($this->view_data['menu'] as $item) {
			if ($item->link === "Calendar_conges_absences") {
				return true;
			}
		}
		return false;
	}

	private function setSubmenu(): void
	{
		$this->view_data['submenu'] = [
			$this->lang->line('application_all') => 'projects/filter/all',
			$this->lang->line('application_open') => 'projects/filter/open',
			$this->lang->line('application_closed') => 'projects/filter/closed'
		];
	}

	private function loadDatabase(): void
	{
		$this->load->database();
	}


	public function index(): void
	{
		$events = $this->db->table('t_pasa_conges')
			->whereIn('statut', ['28'])
			->get()
			->getResult();

		foreach ($events as $event) {
			$user = Salaries::find($event->id_salarie);
			$event->id_salarie = $user;
		}

		$this->view_data['events_list'] = $this->generateEventList($events);
		$this->view_data['salaries'] = Salaries::all();
		$this->content_view = 'calendar_conges/full';
	}
	private function generateEventList(array $events): string
	{
		$event_list = [];

		foreach ($events as $value) {
			$motif = $value->motif;
			$statut = $value->statut;

			$result_motif = $this->db->table('ref_type_occurences')->whereIn('id_type_occ', [$motif])->get()->getRow();
			$result_statut = $this->db->table('ref_type_occurences')->whereIn('id_type_occ', [$statut])->get()->getRow();

			$class = $this->getClassForMotif($value->motif);
			$time = ($value->motif === "162") ? date('H:i', strtotime($value->date_debut)) . ' -- ' . date('H:i', strtotime($value->date_fin)) : '';

			$event_list[] = [
				'title' => "{$value->id_salarie->nom} {$value->id_salarie->prenom} -- {$result_motif->name} {$time}",
				'start' => $value->date_debut,
				'end' => date('Y-m-d H:i', strtotime($value->date_fin . '+1 hour')),
				'className' => $class,
				'modal' => 'true',
				'id' => "{$value->id_salarie->nom} {$value->id_salarie->prenom}",
				'motif' => $result_statut->name,
			];
		}

		return json_encode($event_list);
	}

	private function getClassForMotif(string $motif): string
	{
		return match ($motif) {
			"120" => "bgColor11",
			"121" => "bgColor1",
			"122" => "bgColor3",
			"162" => "bgColor6",
			default => "defaultClass", // Fallback class
		};
	}

	public function update_calendar($id = null): void
	{
		if ($this->request->getMethod() === 'post') {
			$data = $this->request->getPost();
			unset($data['send']);

			$this->db->table('t_pasa_conges')->where('id', $id)->update($data);
			return redirect('Calendar_conges_absences');
		}

		$this->view_data['motif'] = $this->referentiels->getReferentielsByIdType($this->config->item("type_id_motif_absence"));
		$this->view_data['statut'] = $this->referentiels->getReferentielsByIdType($this->config->item("type_id_statut_conges"));
		$this->view_data['salaries'] = $this->salariesModel->all();
		$this->view_data['item'] = $this->congesModel->find(['id' => $id]);

		$this->theme_view = 'modal';
		$this->view_data['title'] = $this->lang->line('application_edit_conge');
		$this->view_data['form_action'] = "Calendar_conges_absences/update_calendar/$id";
		$this->content_view = 'rhpaie/validateconge';
	}

	public function view($name = null): array
	{
		return $this->db->table('t_pasa_conges')
			->select('* , salaries.nom, salaries.prenom')
			->join('salaries', 'salaries.id = t_pasa_conges.id_salarie')
			->get()
			->getResultArray();
	}

}