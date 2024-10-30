<?php

namespace App\Controllers;

use App\Models\RefTypeOccurencesModel;
use App\Models\SalarieModel;
use App\Models\CongesModel;
use App\Controllers\BaseController;
use CodeIgniter\Database\Database;

class CalendarCongesAbsencesController extends BaseController
{
	private RefTypeOccurencesModel $referentiels;
	private SalarieModel $salariesModel;
	private CongesModel $congesModel;
	protected array $view_data = [];
	protected $theme_view, $db;

	public function __construct()
	{
		$this->referentiels = new RefTypeOccurencesModel();
		$this->salariesModel = new SalarieModel();
		$this->congesModel = new CongesModel();

		$this->handleRedirects();
		$this->setSubmenu();
	}

	private function handleRedirects()
	{
		if ($this->client) {
			$link = $this->request->getCookie('fc2_link');
			return redirect($link ? str_replace("/tickets/", "/ctickets/", $link) : 'cprojects');
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
		return array_filter($this->view_data['menu'], fn($item) => $item->link === "Calendar_conges_absences") !== [];
	}

	private function setSubmenu(): void
	{
		$this->view_data['submenu'] = [
			$this->lang->line('application_all') => 'projects/filter/all',
			$this->lang->line('application_open') => 'projects/filter/open',
			$this->lang->line('application_closed') => 'projects/filter/closed'
		];
	}

	public function index(): void
	{
		$events = $this->db->table('t_pasa_conges')
			->whereIn('statut', ['28'])
			->get()
			->getResult();

		$this->view_data['events_list'] = $this->generateEventList($events);
		$this->view_data['salaries'] = $this->salariesModel->findAll();
		$this->content_view = 'calendar_conges/full';
	}

	private function generateEventList(array $events): string
	{
		return json_encode(array_map(fn($value) => $this->formatEvent($value), $events));
	}

	private function formatEvent($value): array
	{
		$motif = $value->motif;
		$statut = $value->statut;

		$result_motif = $this->db->table('ref_type_occurences')->where('id_type_occ', $motif)->get()->getRow();
		$result_statut = $this->db->table('ref_type_occurences')->where('id_type_occ', $statut)->get()->getRow();

		$class = $this->getClassForMotif($motif);
		$time = ($motif === "162") ? date('H:i', strtotime($value->date_debut)) . ' -- ' . date('H:i', strtotime($value->date_fin)) : '';

		return [
			'title' => "{$value->id_salarie->nom} {$value->id_salarie->prenom} -- {$result_motif->name} {$time}",
			'start' => $value->date_debut,
			'end' => date('Y-m-d H:i', strtotime($value->date_fin . '+1 hour')),
			'className' => $class,
			'modal' => true,
			'id' => "{$value->id_salarie->nom} {$value->id_salarie->prenom}",
			'motif' => $result_statut->name,
		];
	}

	private function getClassForMotif(string $motif): string
	{
		return match ($motif) {
			"120" => "bgColor11",
			"121" => "bgColor1",
			"122" => "bgColor3",
			"162" => "bgColor6",
			default => "defaultClass",
		};
	}

	public function update_calendar(int $id = null): void
	{
		if ($this->request->getMethod() === 'post') {
			$data = $this->request->getPost();
			unset($data['send']);

			$this->db->table('t_pasa_conges')->where('id', $id)->update($data);
			return redirect('Calendar_conges_absences');
		}

		$this->view_data['motif'] = $this->referentiels->getReferentielsByIdType($this->config->item("type_id_motif_absence"));
		$this->view_data['statut'] = $this->referentiels->getReferentielsByIdType($this->config->item("type_id_statut_conges"));
		$this->view_data['salaries'] = $this->salariesModel->findAll();
		$this->view_data['item'] = $this->congesModel->find($id);

		$this->theme_view = 'modal';
		$this->view_data['title'] = $this->lang->line('application_edit_conge');
		$this->view_data['form_action'] = "Calendar_conges_absences/update_calendar/$id";
		$this->content_view = 'rhpaie/validateconge';
	}

	public function view(): array
	{
		return $this->db->table('t_pasa_conges')
			->select('* , salaries.nom, salaries.prenom')
			->join('salaries', 'salaries.id = t_pasa_conges.id_salarie')
			->get()
			->getResultArray();
	}
}
