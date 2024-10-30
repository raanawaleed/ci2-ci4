<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class ProjectsController extends BaseController
{

	function __construct()
	{
		parent::__construct();
		if ($this->client) {
		} elseif ($this->user) {
		} else {
			redirect('login');
		}
		$this->load->model('Projects_model', 'project');
		$this->load->model('facture_model', 'invoice');
		$this->load->model('submenu_model', 'submenu');
		$this->load->model('refType_model', 'refType');
		$this->load->model('Ref_type_occurences_model', 'referentiels');
		$this->load->model('Setting_model', 'settingTables');
		$this->load->model('ProjectHasTask_model', 'projecthastask');
		$this->load->model('estimate_model', 'devis');
		$this->load->model('user_model');
		$this->load->model('nature_model');
		$this->load->model('client_model');
		$this->load->model('ticket_model');
		$this->load->helper('calcul_helper');


		$access = FALSE;
		$option = array('conditions' => array('user_id = ?', $this->user->id));
		$accessSubmenu = explode(',', AccesRigth::find($option)->submenu);
		$idInvoice = $this->submenu->getByName('invoices')[0]->id;
		if ($this->user && $this->user->admin == 1) {
			$this->view_data['invoice_access'] = true;
		}
		$this->view_data['submenu'] = array(
			$this->lang->line('application_all') => 'projects/filter/all',
			$this->lang->line('application_open') => 'projects/filter/open',
			$this->lang->line('application_closure') => 'projects/filter/closed'
		);
	}


	function index()
	{
		$ida = $this->user->salaries_id;
		if ($ida == NULL) {
			$this->content_view = 'projects/all_projects';
		} else {
			$ids = $this->user->salaries_id;
			$this->view_data['data'] = $this->user_model->idsal($ids);
			$this->content_view = 'projects/all_projects';
		}
	}

	//Filtrer les projets
	function filter($condition)
	{
		switch ($condition) {
			case 'open':
				$options = 'progress < 100';
				break;
			case 'closed':
				$options = 'progress = 100';
				break;
			case 'all':
				$options = 'progress = 100 OR progress < 100';
				break;
			default:
				$options = 'progress = 100 OR progress < 100';
		}
		$this->view_data['project'] = $this->db->query("SELECT * FROM projects where " . $options . "")->result();
		$this->content_view = 'projects/all';
		$this->view_data['projects_assigned_to_me'] = ProjectHasWorker::find_by_sql('select count(distinct(projects.id)) AS "amount" FROM projects, project_has_workers WHERE projects.progress != "100" AND (projects.id = project_has_workers.project_id AND project_has_workers.intervenant_id = "' . $this->user->id . '") ');
		$this->view_data['tasks_assigned_to_me'] = ProjectHasTask::count(array('conditions' => 'user_id = ' . $this->user->id . ' and status = "open"'));
		$now = time();
		$beginning_of_week = strtotime('last Monday', $now); // BEGINNING of the week
		$end_of_week = strtotime('next Sunday', $now) + 86400; // END of the last day of the week
		$this->view_data['projects_opened_this_week'] = Project::find_by_sql('select count(id) AS "amount", DATE_FORMAT(FROM_UNIXTIME(`datetime`), "%w") AS "date_day", DATE_FORMAT(FROM_UNIXTIME(`datetime`), "%Y-%m-%d") AS "date_formatted" from projects where datetime >= "' . $beginning_of_week . '" AND datetime <= "' . $end_of_week . '" ');
	}

	//Créer un nouveau projet
	function create()
	{
		if ($_POST) {
			unset($_POST['send']);
			$_POST['datetime'] = time();
			$_POST = array_map('htmlspecialchars', $_POST);
			unset($_POST['files']);
			$option = array("id_vcompanies" => $_SESSION['current_company']);
			$project_reference = Setting::find($option);
			$_POST['creation_date'] = date("Y-m-d");
			$year = date("Y");
			$latstproject = Project::last();
			$lastRef = explode('-', (date_format($latstproject->creation_date, 'Y-m-d')));
			if ($lastRef[0] != $year) {
				$project_reference->project_reference = 1;
			}
			//-------------------------- Project name 		
			$project_pieces = explode("-", strrev($project_reference->project_prefix));
			$var = date("y-m-d", strtotime($_POST['start']));
			$pieces = explode("-", $var);
			$piecesYear = $pieces[0];
			$piecesMounth = $pieces[1];
			$subpiecesYear = explode(' ', $pieces[0]);
			// année 
			if ($project_pieces[0] == 'YY') {
				$_POST['project_num'] = strrev($project_pieces[1]) . $subpiecesYear[0] . $_POST['reference'];
			}
			//année + mois 
			else {
				$_POST['project_num'] = strrev($project_pieces[2]) . $subpiecesYear[0] . $piecesMounth . $_POST['reference'];
			}
			//--------------------------
			$_POST['name'] = str_replace("'", "   ", $_POST['name']);

			$project = Project::create($_POST);
			//var_dump($_POST['name']) ;exit;
			$new_project_reference = $project_reference->project_reference + 1;
			$project_reference->update_attributes(array('project_reference' => $new_project_reference));
			if (!$project) {
				$this->session->set_flashdata('message', 'error:' . $this->lang->line('messages_create_project_error'));
			} else {
				$this->session->set_flashdata('message', 'success:' . $this->lang->line('messages_create_project_success'));
			}
			redirect('projects');
		} else {
			$this->view_data['companies'] = Company::find('all', array('conditions' => array('inactive=?', 0)));
			$this->view_data['next_reference'] = Project::last();
			$option = array("id_vcompanies" => $_SESSION['current_company']);
			$this->view_data['core_settings'] = Setting::find($option);
			$current_date = date('Y-m-d');
			$current_date = explode('-', $current_date);
			//revert ref project 
			$year = date("Y");
			$latstproject = Project::last();
			$lastRef = explode('-', (date_format($latstproject->creation_date, 'Y-m-d')));
			if ($lastRef[0] != $year) {
				$this->view_data['core_settings']->project_reference = '1';
			}
			$this->theme_view = 'modal';
			$this->view_data['clients'] = $this->db->query("SELECT * FROM  companies")->result();
			$this->view_data['title'] = $this->lang->line('application_create_project');
			//le référentiel des catégories projets
			$idType = $this->refType->getRefTypeByName("catégorie projet")->id;
			$this->view_data['chef_projet'] = $this->user_model->getAll();
			$this->view_data['categorie_projets'] = $this->referentiels->getReferentielsByIdType($idType);
			$this->view_data['etats_projet'] = $this->referentiels->getTabReferentielsByIdType($this->config->item('type_id_etat_projet'));
			$this->view_data['etat_encours'] = $this->referentiels->getReferentiels($this->config->item('type_id_etat_projet'), $this->config->item('type_occ_code_etat_projet_en_cours'));
			$this->view_data['data'] = $this->project->display_records();
			$this->view_data['form_action'] = 'projects/create';
			$this->content_view = 'projects/_project';
		}
	}

	//Mettre à jour un projet
	function update($id = FALSE)
	{
		if ($_POST) {
			unset($_POST['send']);
			$id = $_POST['id'];
			unset($_POST['files']);
			$_POST = array_map('htmlspecialchars', $_POST);
			if (!isset($_POST["progress_calc"])) {
				$_POST["progress_calc"] = 0;
			}
			if (!isset($_POST["hide_tasks"])) {
				$_POST["hide_tasks"] = 0;
			}

			if (!isset($_POST["enable_client_tasks"])) {
				$_POST["enable_client_tasks"] = 0;
			}

			$project = Project::find($id);
			$_POST['project_num'] = $_POST['reference'];
			unset($_POST['reference']);
			$project->update_attributes($_POST);
			if (!$project) {
				$this->session->set_flashdata('message', 'error:' . $this->lang->line('messages_save_project_error'));
			} else {
				$this->session->set_flashdata('message', 'success:' . $this->lang->line('messages_save_project_success'));
			}
			redirect('projects/view/' . $id);
		} else {
			$this->view_data['companies'] = Company::find('all', array('conditions' => array('inactive=?', 0)));
			$project = Project::find($id);
			$this->view_data['project'] = $project;
			$this->view_data['contacts_client'] = client::find('all', array("company_id" => $project->company_id));
			if ($this->view_data['project']->company_id != 0) {
				$this->view_data['project']->company_id = Company::find($this->view_data['project']->company_id);
			}
			$this->theme_view = 'modal';
			$this->view_data['title'] = $this->lang->line('application_edit_project');
			$this->view_data['form_action'] = 'projects/update';
			$this->view_data['clients'] = $this->db->query("SELECT * FROM  companies")->result();
			//le référentiel catégorie projet
			$idType = $this->refType->getRefTypeByName("catégorie projet")->id;
			$this->view_data['chef_projet'] = $this->user_model->getAll();
			$this->view_data['categorie_projets'] = $this->referentiels->getReferentielsByIdType($idType);
			$nat_projet = $project->type_projet;
			$nature = $this->nature_model->getNatureByCat($nat_projet);
			$nature = json_decode(json_encode($nature), true);
			$nature = array_column($nature, 'id_nature');
			$nature = implode(',', $nature);
			$this->view_data['natures_projetcs'] = $this->referentiels->getNature($nature);
			$this->view_data['etats_projet'] = $this->referentiels->getTabReferentielsByIdType($this->config->item('type_id_etat_projet'));
			$this->view_data['etat_encours'] = $this->referentiels->getReferentiels($this->config->item('type_id_etat_projet'), $this->config->item('type_occ_code_etat_projet_en_cours'));
			$this->content_view = 'projects/_project';
		}
	}

	//Afficher le détail d'un projet
	//---A voir à optimiser
	function view($id = FALSE, $taskId = FALSE)
	{
		$current_user = $this->db->select('*')
			->from('users')
			->where('id', $this->session->userdata["user_id"])
			->get()
			->result();
		$this->load->database();
		$project = Project::find($id);
		//var_dump(($project));exit;
		$this->view_data['submenu'] = array();
		$this->view_data['user'] = $this->user;
		$this->view_data['project'] = $project;


		$this->view_data['chef_projet'] = $this->user_model->getUserById($project->chef_projet_id);
		//$this->view_data['chef_client']= Client::find('all',array("company_id"=>$project->company_id));
		$this->view_data['chef_client'] = $this->client_model->getUserById($project->sub_client_id);
		//sous-projets
		$project_has_workers = ProjectHasWorker::find('all', array("project_id" => $id));
		$this->view_data['sub_projects'] = ProjectHasSubProject::all(array('project_id' => $id));
		//$project = Project::find($id);


		//Temps passé sur les tâches des sous projets
		$sub_projects_heures_pointees = $this->project->getPeriodTickets_Byprojet($id, true, true);
		$tab_sub_projects_heures_pointees = array();
		foreach ($sub_projects_heures_pointees as $key => $proj) {
			$tab_sub_projects_heures_pointees[$proj->project_pere] = $proj;
		}
		$this->view_data['tab_sub_projects_heures_pointees'] = $tab_sub_projects_heures_pointees;

		$this->view_data['url_add_ref'] = 'projects/addSousProjet/' . $id;
		$this->view_data['url_update_ref'] = 'projects/editSousProjet';
		$this->view_data['url_delete_ref'] = 'projects/supprimerSousProjet';

		$this->view_data['users'] = $this->db->query('SELECT * 
		FROM users,  project_has_workers 
		WHERE users.id = project_has_workers.user_id 
		AND project_id =' . $id)->result();

		if (!isset($this->view_data['users'])) {
			$this->view_data['users'] = array();
		}

		$this->db->where('project_id', $this->view_data['project']->id);
		$this->view_data['facture'] = $this->db->get('facture')->result();
		$this->view_data['go_to_taskID'] = $taskId;
		$this->view_data['project_has_invoices'] = $this->invoice->getByIdProject($id);
		$this->view_data['devis'] = $this->devis->getByIdProject($id);

		//ProjectHasInvoice::all(array('conditions' => array('project_id = ?', $id)));
		if (!isset($this->view_data['project_has_invoices'])) {
			$this->view_data['project_has_invoices'] = array();
		} else {
			foreach ($this->view_data['project_has_invoices'] as $val) {
				$val->project_id = Project::find($val->project_id);
				$val->invoice_id = $this->invoice->getById($val->invoice_id)[0];
				$val->invoice_id->company_id = Company::find($val->invoice_id->company_id);
				$val->invoice_id->status = $this->referentiels->getReferentielsById($val->invoice_id->status)->name;
				$refType = $this->refType->getRefTypeByName($val->invoice_id->currency)->id;
				$val->invoice_id->notes = $this->referentiels->getReferentielsByIdType($refType)->name;
			}
		}
		$ticket = $this->ticket_model->getTicketByTypeProjet($id);
		$this->view_data['ticket'] = $ticket;
		//var_dump($this->view_data['ticket']);
		$subj = $this->ticket_model->getTicketSubjectByIdProject($id);
		$this->view_data['subject'] = $subj;


		$test = $this->ticket_model->getPeriodPerTicket($taskId);
		//$this->view_data['test']=$test[0]->periode;

		$this->view_data['Heures'] = calcul_heure($this->view_data['subject']);


		$tasks = ProjectHasTask::count(array('conditions' => array('project_id = ?', $id)));

		$this->view_data['alltasks'] = $tasks;
		$this->view_data['opentasks'] = ProjectHasTask::count(array('conditions' => array('status != ? AND project_id = ?', 'done', $id)));
		$this->view_data['usercountall'] = User::count(array('conditions' => array('status = ?', 'active')));
		$this->view_data['usersassigned'] = ProjectHasWorker::count(array('conditions' => array('project_id = ?', $id)));
		$this->view_data['assigneduserspercent'] = round($this->view_data['usersassigned'] / $this->view_data['usercountall'] * 100);
		//Format statistic labels and values
		$this->view_data["labels"] = "";
		$this->view_data["line1"] = "";
		$this->view_data["line2"] = "";
		$this->view_data["current_user"] = $current_user;
		$daysOfWeek = getDatesOfWeek();
		$this->view_data['dueTasksStats'] = ProjectHasTask::getDueTaskStats($id, $daysOfWeek[0], $daysOfWeek[6]);
		$this->view_data['startTasksStats'] = ProjectHasTask::getStartTaskStats($id, $daysOfWeek[0], $daysOfWeek[6]);
		foreach ($daysOfWeek as $day) {
			$counter = "0";
			$counter2 = "0";
			foreach ($this->view_data['dueTasksStats'] as $value):
				if ($value->due_date == $day) {
					$counter = $value->tasksdue;
				}
			endforeach;
			foreach ($this->view_data['startTasksStats'] as $value):
				if ($value->start_date == $day) {
					$counter2 = $value->tasksdue;
				}
			endforeach;
			$this->view_data["labels"] .= '"' . $day . '"';
			$this->view_data["labels"] .= ',';
			$this->view_data["line1"] .= $counter . ",";
			$this->view_data["line2"] .= $counter2 . ",";
		}
		$this->view_data['time_days'] = round((human_to_unix($this->view_data['project']->end . ' 00:00') - human_to_unix($this->view_data['project']->start . ' 00:00')) / 3600 / 24);
		$this->view_data['time_left'] = $this->view_data['time_days'];
		$this->view_data['timeleftpercent'] = 100;
		if (human_to_unix($this->view_data['project']->start . ' 00:00') < time() && human_to_unix($this->view_data['project']->end . ' 00:00') > time()) {
			$this->view_data['time_left'] = round((human_to_unix($this->view_data['project']->end . ' 00:00') - time()) / 3600 / 24);
			$this->view_data['timeleftpercent'] = $this->view_data['time_left'] / $this->view_data['time_days'] * 100;
		}
		if (human_to_unix($this->view_data['project']->end . ' 00:00') < time()) {
			$this->view_data['time_left'] = 0;
			$this->view_data['timeleftpercent'] = 0;
		}
		$this->view_data['allmytasks'] = ProjectHasTask::all(array('conditions' => array('project_id = ? AND user_id = ?', $id, $this->user->id)));
		$this->view_data['mytasks'] = ProjectHasTask::count(array('conditions' => array('status != ? AND project_id = ? AND user_id = ?', 'done', $id, $this->user->id)));
		$this->view_data['tasksWithoutMilestone'] = ProjectHasTask::find('all', array('conditions' => array('milestone_id = ? AND project_id = ? ', '0', $id)));
		$tasks_done = ProjectHasTask::count(array('conditions' => array('status = ? AND project_id = ?', 'done', $id)));
		$this->view_data['progress'] = $this->view_data['project']->progress;
		if ($this->view_data['project']->progress_calc == 1) {
			if ($tasks) {
				$this->view_data['progress'] = round($tasks_done / $tasks * 100);
			}
			$attr = array('progress' => $this->view_data['progress']);
			$this->view_data['project']->update_attributes($attr);
		}
		$this->view_data['opentaskspercent'] = ($tasks == 0 ? 0 : $tasks_done / $tasks * 100);
		$projecthasworker = ProjectHasWorker::all(array('conditions' => array('user_id  = ? AND project_id = ?', $this->user->id, $id)));
		$tracking = $this->view_data['project']->time_spent;
		if (!empty($this->view_data['project']->tracking)) {
			$tracking = (time() - $this->view_data['project']->tracking) + $this->view_data['project']->time_spent;
		}
		$this->view_data['timertime'] = $tracking;
		$this->view_data['time_spent_from_today'] = time() - $this->view_data['project']->time_spent;
		$tracking = floor($tracking / 60);
		$tracking_hours = floor($tracking / 60);
		$tracking_minutes = $tracking - ($tracking_hours * 60);
		$this->view_data['time_spent'] = $tracking_hours . " " . $this->lang->line('application_hours') . " " . $tracking_minutes . " " . $this->lang->line('application_minutes');
		$this->view_data['time_spent_counter'] = sprintf("%02s", $tracking_hours) . ":" . sprintf("%02s", $tracking_minutes);
		$users = Intervenant::all(array('conditions' => array('visible = ?', '1')));

		//Liste de tous les tickets d'un projet		
		$alltasks = $this->project->getTickets_Byprojet($this->view_data['project']->id);
		//Liste de tous les tickets d'un projet avec la total de la saisie des temps 
		$alltasksTotalSaisie = $this->project->getPeriodTickets_Byprojet($this->view_data['project']->id);
		$this->view_data['unite_temps'] = $this->referentiels->getReferentiels($this->config->item("type_id_unite_temps"));

		foreach ($alltasks as $task) {
			foreach ($users as $user) {
				if ($task->user_id == $user->id) {
					$task->user_id = $user;
				}
			}
			$searchedValue = $task->id;
			$neededObject = array_filter(
				$alltasksTotalSaisie,
				function ($e) use (&$searchedValue) {
					return $e->id == $searchedValue;
				}
			);
			$task->temps = reset($neededObject);
		}

		$this->view_data['unite_temps'] = $this->referentiels->getReferentiels($this->config->item("type_id_unite_temps"));
		$projet_heures_pointees = $this->project->getPeriodTickets_Byprojet($this->view_data['project']->id, true);
		if (count($projet_heures_pointees) > 0)
			$projet_heures_pointees = $projet_heures_pointees[0];
		if ($this->view_data['project']->company_id != 0) {
			$this->view_data['project']->company_id = Company::find($this->view_data['project']->company_id);
		}
		$this->view_data['allintervenants'] = Intervenant::all(array(
			'conditions' => array(
				'visible = ? and id_vcompanies=?',
				'1',
				$_SESSION['current_company']
			)
		));
		$this->view_data['allusers'] = User::all(array('conditions' => array('status = ?', 'active')));
		$this->view_data['tasklist'] = $alltasks;
		$this->view_data['alltasksTotalSaisie'] = $alltasksTotalSaisie;
		$this->view_data['projet_heures_pointees'] = $projet_heures_pointees;

		$this->view_data['end'] = $this->view_data['project']->end;
		$totheures = $this->project->calculeheure($this->view_data['project']->id);
		$this->view_data['totheures'] = $totheures;
		//var_dump($this->view_data['totheures']->periode);exit;
		$this->view_data['type_projet'] = $this->project->getTypeProjet_Byid($this->view_data['project']->type_projet);
		$this->view_data['nature_projet'] = $this->project->getTypeProjet_Byid($this->view_data['project']->nature_projet);
		$this->view_data['etats_projet'] = $this->referentiels->getTabReferentielsByIdType($this->config->item('type_id_etat_projet'));
		$this->content_view = 'projects/view';
	}

	function sortlist($sort = FALSE, $list = FALSE)
	{
		if ($sort) {
			$sort = explode("-", $sort);
			$sortnumber = 1;
			foreach ($sort as $value) {
				$task = ProjectHasTask::find_by_id($value);
				if ($list != "task-list") {
					$task->milestone_order = $sortnumber;
				} else {
					$task->task_order = $sortnumber;
				}
				$task->save();
				$sortnumber = $sortnumber + 1;
			}
		}
		$this->theme_view = 'blank';
	}

	function sort_milestone_list($sort = FALSE, $list = FALSE)
	{
		if ($sort) {
			$sort = explode("-", $sort);
			$sortnumber = 1;
			foreach ($sort as $value) {
				$task = ProjectHasMilestone::find_by_id($value);
				$task->orderindex = $sortnumber;
				$task->save();
				$sortnumber = $sortnumber + 1;
			}
		}
		$this->theme_view = 'blank';
	}

	function move_task_to_milestone($taskId = FALSE, $listId = FALSE)
	{
		if ($listId && $taskId) {
			$task = ProjectHasTask::find_by_id($taskId);
			$task->milestone_id = $listId;
			$task->save();
		}
		$this->theme_view = 'blank';
	}

	function task_change_attribute()
	{
		if ($_POST) {
			$name = $_POST["name"];
			$taskId = $_POST["pk"];
			$value = $_POST["value"];
			$task = ProjectHasTask::find_by_id($taskId);
			$task->{$name} = $value;
			$task->save();
		}
		$this->theme_view = 'blank';
	}

	function task_start_stop_timer($taskId)
	{
		$currentitem = $this->db->select('time_spent')->from('project_has_tasks')->where('id', $taskId)->get()->result();
		//echo $currentitem[0]->time_spent;

		$task = ProjectHasTask::find_by_id($taskId);
		if ($task->tracking != 0) {
			$now = time();
			$diff = $now - $task->tracking;
			$timer_start = $task->tracking;
			$task->time_spent = $task->time_spent + $diff;
			$task->tracking = "";
			//add time to timesheet
			$attributes = array(
				'task_id' => $task->id,
				'user_id' => $task->user_id,
				'project_id' => $task->project_id,
				'client_id' => 0,
				'time' => $diff,
				'start' => $timer_start,
				'end' => $now
			);
			$timesheet = ProjectHasTimesheet::create($attributes);
			$date = date("Y-m-d");
			$data = array(
				'id_task' => $taskId,
				'time_task' => $task->time_spent - $currentitem[0]->time_spent,
				'date_task' => $date
			);

			$this->db->insert('time_date_tasks', $data);
		} else {
			$task->tracking = time();
		}
		$task->save();
		$this->theme_view = 'blank';
	}

	function get_milestone_list($projectId)
	{
		$milestone_list = "";
		$project = Project::find_by_id($projectId);
		foreach ($project->project_has_milestones as $value) {
			$milestone_list .= '{value:' . $value->id . ', text: "' . $value->name . '"},';
		}
		echo $milestone_list;
		$this->theme_view = 'blank';
	}

	//Copier un projet existant
	function copy($id = FALSE)
	{
		if ($_POST) {
			unset($_POST['send']);
			$id = $_POST['id'];
			unset($_POST['id']);
			$_POST['datetime'] = time();
			$_POST = array_map('htmlspecialchars', $_POST);
			unset($_POST['files']);
			if (isset($_POST['tasks'])) {
				unset($_POST['tasks']);
				$tasks = TRUE;
			}

			$project = Project::create($_POST);
			$new_project_reference = $_POST['reference'] + 1;
			$option = array("id_vcompanies" => $_SESSION['current_company']);
			$project_reference = Setting::find($option);
			$project_reference->update_attributes(array('project_reference' => $new_project_reference));

			if ($tasks) {
				unset($_POST['tasks']);
				$source_project = Project::find_by_id($id);
				foreach ($source_project->project_has_tasks as $row) {
					$attributes = array(
						'project_id' => $project->id,
						'name' => $row->name,
						'user_id' => '',
						'status' => 'open',
						'public' => $row->public,
						'datetime' => $project->start,
						'due_date' => $project->end,
						'description' => $row->description,
						'value' => $row->value,
						'priority' => $row->priority,

					);
					ProjectHasTask::create($attributes);
				}
			}

			if (!$project) {
				$this->session->set_flashdata('message', 'error:' . $this->lang->line('messages_create_project_error'));
			} else {
				$this->session->set_flashdata('message', 'success:' . $this->lang->line('messages_create_project_success'));
				$attributes = array('project_id' => $project->id, 'intervenant_id' => $this->user->id);
				ProjectHasWorker::create($attributes);
			}
			redirect('projects');
		} else {
			$this->view_data['companies'] = Company::find('all', array('conditions' => array('inactive=?', '0')));
			$this->view_data['project'] = Project::find($id);
			$this->theme_view = 'modal';
			$this->view_data['title'] = $this->lang->line('application_copy_project');
			$this->view_data['form_action'] = 'projects/copy';
			$this->content_view = 'projects/_copy';
		}
	}



	function delete($id = FALSE)
	{
		$project = Project::find($id);
		$project->delete();
		$sql = 'DELETE FROM project_has_tasks WHERE project_id = "' . $id . '"';
		$this->db->query($sql);
		$sql = 'DELETE FROM tickets where project_id ="' . $id . '"';
		$this->db->query($sql);
		$this->content_view = 'projects/all';
		if (!$project) {
			$this->session->set_flashdata('message', 'error:' . $this->lang->line('messages_delete_project_error'));
		} else {
			$this->session->set_flashdata('message', 'success:' . $this->lang->line('messages_delete_project_success'));
		} {
			redirect('projects');
		}
	}

	function timer_reset($id = FALSE)
	{
		$project = Project::find($id);
		$attr = array('time_spent' => '0');
		$project->update_attributes($attr);
		$this->session->set_flashdata('message', 'success:' . $this->lang->line('messages_timer_reset'));
		redirect('projects/view/' . $id);
	}

	function timer_set($id = FALSE)
	{
		if ($_POST) {
			$project = Project::find_by_id($_POST['id']);
			$hours = $_POST['hours'];
			$minutes = $_POST['minutes'];
			$timespent = ($hours * 60 * 60) + ($minutes * 60);
			$attr = array('time_spent' => $timespent);
			$project->update_attributes($attr);
			$this->session->set_flashdata('message', 'success:' . $this->lang->line('messages_timer_set'));
			redirect('projects/view/' . $_POST['id']);
		} else {
			$this->view_data['project'] = Project::find($id);
			$this->theme_view = 'modal';
			$this->view_data['title'] = $this->lang->line('application_timer_set');
			$this->view_data['form_action'] = 'projects/timer_set';
			$this->content_view = 'projects/_timer';
		}
	}

	function statistique()
	{
		$id = '';
		$item = $this->db->select('*')->from('salaries')->where('id', $id)->get()->result();
		$data["logo"] = $this->db->select('*')->from('setting_document_rh')->get()->result();
		$data["pic"] = $this->db->select('*')->from('v_companies')->where('id', $_SESSION['current_company'])->get()->result();
		$data["all"] = $item;
		$pdf = new HTML2PDF('P', 'A4', 'fr', 'true', 'UTF-8');
		$content = $this->load->view('blueline/rhpaie/pdftest', $data, true);
		$pdf->writeHTML($content);
		ob_end_clean();
		$pdf->Output('attestation_travail.pdf');
	}

	function stat()
	{
		$this->load->model('m_stat');
		$id = end($this->uri->segments);
		$name_project = $this->m_stat->project_name($id);
		$tasks_project = $this->m_stat->tasks_project($id);
		$count_heure = $this->m_stat->count_heure($id);
		$all_tasks = $this->m_stat->all_tasks($id);
		foreach ($tasks_project as $value) {
			if ($value->user_id != "") {
				$user_tache = $this->db->select('*')
					->from('users')
					->where('id', $value->user_id)
					->get()
					->result();
				$value->name_user = $user_tache[0]->firstname . " " . $user_tache[0]->lastname;
			}
		}
		$data["all"] = $tasks_project;
		$data["name_projext"] = $name_project;
		if ($count_heure[0]->total > 86400) {
			$day = $count_heure[0]->total / 86400;
			$hours = ($count_heure[0]->total % 86400) / 3600;
			$minutes = ($count_heure[0]->total % 3600) / 60;
		} elseif ($count_heure[0]->total > 3600) {
			$day = 0;
			$hours = $count_heure[0]->total / 3600;
			$minutes = ($count_heure[0]->total % 3600) / 60;
		} else {
			$day = 0;
			$hours = 0;
			$minutes = $count_heure[0]->total / 60;
		}
		$day = floor($day);
		$hours = floor($hours);
		$minutes = floor($minutes);
		$data["count_heure"] = $day . " jour(s) et " . $hours . " heure(s) et " . $minutes . " minute(s)";
		$data["all_tasks"] = $all_tasks;
		$pdf = new HTML2PDF('P', 'A4', 'fr', 'true', 'UTF-8');
		$content = $this->load->view('blueline/projects/pdf_tasks', $data, true);
		$pdf->writeHTML($content);
		ob_end_clean();
		$pdf->Output('project.pdf');
		$this->view_data['tasks_project'] = $tasks_project;
	}



	function ganttChart($id)
	{
		$gantt_data = "[";
		$project = Project::find_by_id($id);
		foreach ($project->project_has_milestones as $milestone):
			$counter = 0;
			foreach ($milestone->project_has_tasks as $value):
				$milestone_Name = ($counter == 0) ? $milestone->name : "";
				$counter++;
				$start = ($value->start_date) ? $value->start_date : $milestone->start_date;
				$end = ($value->due_date) ? $value->due_date : $milestone->due_date;
				$gantt_data .= '{ name: "' . $milestone_Name . '", desc: "' . $value->name . '", values: [';
				$gantt_data .= '{ label: "' . $value->name . '", from: "' . $start . '", to: "' . $end . '" }';
				$gantt_data .= ']},';
			endforeach;

		endforeach;
		$gantt_data .= "]";
		$this->theme_view = 'blank';
		echo $gantt_data;
	}

	function quickTask()
	{
		if ($_POST) {
			unset($_POST['send']);
			unset($_POST['files']);
			$task = ProjectHasTask::create($_POST);
			echo $task->id;
		}
		$this->theme_view = 'blank';
	}

	function generate_thumbs($id = FALSE)
	{
		if ($id) {
			$medias = Project::find_by_id($id)->project_has_files;
			//check image processor extension
			if (extension_loaded('gd2')) {
				$lib = 'gd2';
			} else {
				$lib = 'gd';
			}
			foreach ($medias as $value) {
				if (!file_exists('./files/media/thumb_' . $value->savename)) {
					$config['image_library'] = $lib;
					$config['source_image'] = './files/media/' . $value->savename;
					$config['new_image'] = './files/media/thumb_' . $value->savename;
					$config['create_thumb'] = TRUE;
					$config['thumb_marker'] = "";
					$config['maintain_ratio'] = TRUE;
					$config['width'] = 170;
					$config['height'] = 170;
					$config['master_dim'] = "height";
					$config['quality'] = "100%";
					$this->load->library('image_lib', $config);
					$this->image_lib->resize();
					$this->image_lib->clear();
				}
			}
			redirect('projects/view/' . $id);
		}
	}

	function dropzone($id = FALSE)
	{
		$attr = array();
		$config['upload_path'] = './files/media/';
		$config['encrypt_name'] = TRUE;
		$config['allowed_types'] = '*';
		$this->load->library('upload', $config);
		if ($this->upload->do_upload("file")) {
			$data = array('upload_data' => $this->upload->data());

			$attr['name'] = $data['upload_data']['orig_name'];
			$attr['filename'] = $data['upload_data']['orig_name'];
			$attr['savename'] = $data['upload_data']['file_name'];
			$attr['type'] = $data['upload_data']['file_type'];
			$attr['date'] = date("Y-m-d H:i", time());
			$attr['phase'] = "";

			$attr['project_id'] = $id;
			$attr['user_id'] = $this->user->id;
			$media = ProjectHasFile::create($attr);
			echo $media->id;

			//check image processor extension
			if (extension_loaded('gd2')) {
				$lib = 'gd2';
			} else {
				$lib = 'gd';
			}
			$config['image_library'] = $lib;
			$config['source_image'] = './files/media/' . $attr['savename'];
			$config['new_image'] = './files/media/thumb_' . $attr['savename'];
			$config['create_thumb'] = TRUE;
			$config['thumb_marker'] = "";
			$config['maintain_ratio'] = TRUE;
			$config['width'] = 170;
			$config['height'] = 170;
			$config['master_dim'] = "height";
			$config['quality'] = "100%";
			$this->load->library('image_lib', $config);
			$this->image_lib->resize();
		} else {
			echo "Upload faild";
			$error = $this->upload->display_errors('', ' ');
			$this->session->set_flashdata('message', $error);
			echo $error;
		}
		$this->theme_view = 'blank';
	}

	function timesheets($taskid)
	{
		$this->load->database();
		$timesheets = ProjectHasTimesheet::find("all", array("conditions" => array("task_id = ?", $taskid)));
		foreach ($timesheets as $timesheet) {
			$timesheet->user_id = Intervenant::find($timesheet->user_id);
		}
		$this->view_data['timesheets'] = $timesheets;
		$this->view_data['task'] = ProjectHasTask::find_by_id($taskid);
		$intervenants = Intervenant::find("all", array("conditions" => array("visible = ?", '1')));
		$allWorker = ProjectHasWorker::find("all", array("conditions" => array("project_id = ?", $this->view_data['task']->project_id)));
		$users = array();
		foreach ($allWorker as $worker) {
			foreach ($intervenants as $inter) {
				if ($worker->user_id == $inter->id) {
					$users[$inter->id] = strtoupper($inter->name . ' ' . $inter->surname);
				}
			}
		}
		$this->view_data['users'] = $users;
		$this->theme_view = 'modal';
		$this->view_data['title'] = $this->lang->line('application_timesheet');
		$this->view_data['form_action'] = 'projects/timesheet_add';
		$this->content_view = 'projects/_timesheets';
	}
	function timesheet_add()
	{
		if ($_POST) {
			$time = ($_POST["hours"] * 3600) + ($_POST["minutes"] * 60);
			$_POST['start'] = strtotime($_POST['start']);
			$_POST['start'] = date("Y-m-d H:i", $_POST['start']);
			//start + duration 
			$_POST['end'] = strtotime($_POST['start']) + $time;
			$_POST['end'] = date("Y-m-d H:i", $_POST['end']);
			$attr = array(
				"project_id" => $_POST["project_id"],
				"user_id" => $_POST["user_id"],
				"time" => $time,
				"client_id" => 0,
				"task_id" => $_POST["task_id"],
				"start" => $_POST["start"],
				"end" => $_POST["end"],
				"invoice_id" => 0,
				"description" => "",
			);
			$timesheet = ProjectHasTimesheet::create($attr);
			$task = ProjectHasTask::find_by_id($timesheet->task_id);
			//var_dump($task);
			$task->time_spent = $task->time_spent + $time;
			$task->save();
			echo $timesheet->id;
		}
		$this->theme_view = 'blank';
	}

	function timesheet_delete($timesheet_id)
	{
		$timesheet = ProjectHasTimesheet::find_by_id($timesheet_id);
		$task = ProjectHasTask::find_by_id($timesheet->task_id);
		$task->time_spent = $task->time_spent - $timesheet->time;
		$task->save();
		$timesheet->delete();
		//refresh : todo 
		$this->theme_view = 'blank';
	}

	function tasks($id = FALSE, $condition = FALSE, $task_id = FALSE)
	{
		$description = '';
		$this->view_data['submenu'] = array(
			$this->lang->line('application_back') => 'projects',
			$this->lang->line('application_overview') => 'projects/view/' . $id,
		);
		$this->db->select('*');
		$this->db->from('users');
		$this->view_data['users'] = $this->db->get()->result();
		switch ($condition) {
			case 'add':
				$this->content_view = 'projects/_tasks';
				if ($_POST) {
					unset($_POST['send']);
					unset($_POST['files']);
					$_POST = array_map('htmlspecialchars', $_POST);
					$_POST['description'] = $description;
					$_POST['project_id'] = $id;
					if (
						"inter" . strpos($_POST['user_id'], "inter") == "inter"
						&& "user" . strpos($_POST['user_id'], "user") != "user"
					) {
						$_POST['user_id'] = str_replace('user', "", $_POST['user_id']);
						$speaker = ProjectHasWorker::find(array("user_id" => $_POST['user_id']));
						if (!isset($speaker)) {
							$data = array(
								'project_id' => $_POST['project_id'],
								'user_id' => $_POST['user_id']
							);
							ProjectHasWorker::create($data);
						}
					} else {
						$_POST['user_id'] = str_replace('inter', "", $_POST['user_id']);
						$_POST['intervenant_id'] = str_replace('inter', "", $_POST['user_id']);
						$_POST['user_id'] = null;
						$speaker = ProjectHasWorker::find(array("intervenant_id" => $_POST['intervenant_id']));
						$intervenant = Intervenant::find($_POST['intervenant_id']);
						if (!isset($speaker)) {
							$data = array(
								'project_id' => $_POST['project_id'],
								'intervenant_id' => $_POST['intervenant_id'],
								'value' => $intervenant->value
							);
							ProjectHasWorker::create($data);
						}
					}
					$task = ProjectHasTask::create($_POST);
					if (!$task) {
						$this->session->set_flashdata('message', 'error:' . $this->lang->line('messages_save_task_error'));
					} else {
						$this->session->set_flashdata('message', 'success:' . $this->lang->line('messages_save_task_success'));
					}
					redirect('projects/view/' . $id);
				} else {
					$this->theme_view = 'modal';
					$this->view_data['project'] = Project::find($id);
					$this->view_data['intervenants'] = Intervenant::find("all", array("conditions" => array("visible=?", 1)));
					$this->view_data['users'] = User::find("all", array("conditions" => array("status=?", 'active')));
					$this->view_data['title'] = $this->lang->line('application_add_task');
					$this->view_data['form_action'] = 'projects/tasks/' . $id . '/add';
					$this->content_view = 'projects/_tasks';
				}
				break;
			case 'update':
				$this->content_view = 'projects/_tasks';
				$this->view_data['task'] = ProjectHasTask::find($task_id);
				if ($_POST) {
					unset($_POST['send']);
					unset($_POST['files']);
					if (!isset($_POST['public'])) {
						$_POST['public'] = 0;
					}
					$description = $_POST['description'];
					$_POST = array_map('htmlspecialchars', $_POST);
					$_POST['description'] = $description;
					$task_id = $_POST['id'];
					$task = ProjectHasTask::find($task_id);
					if (
						"inter" . strpos($_POST['user_id'], "inter") == "inter"
						&& "user" . strpos($_POST['user_id'], "user") != "user"
					) {
						$_POST['user_id'] = str_replace('user', "", $_POST['user_id']);
						$_POST['intervenant_id'] = null;
						$speaker = ProjectHasWorker::find(array("user_id" => $_POST['user_id'], "project_id" => $task->project_id));
						if (!isset($speaker)) {
							$data = array(
								'project_id' => $task->project_id,
								'user_id' => $_POST['user_id']
							);
							ProjectHasWorker::create($data);
						}
					} else {
						$_POST['user_id'] = str_replace('inter', "", $_POST['user_id']);
						$_POST['intervenant_id'] = str_replace('inter', "", $_POST['user_id']);
						$_POST['user_id'] = null;
						$speaker = ProjectHasWorker::find(array("intervenant_id" => $_POST['intervenant_id'], "project_id" => $task->project_id));
						$intervenant = Intervenant::find($_POST['intervenant_id']);
						if (!isset($speaker)) {
							$data = array(
								'project_id' => $task->project_id,
								'intervenant_id' => $_POST['intervenant_id'],
								'value' => $intervenant->value
							);
							ProjectHasWorker::create($data);
						}
					}
					if ($task->user_id != $_POST['user_id']) {
						//stop timer and add time to timesheet
						if ($task->tracking != 0) {
							$now = time();
							$diff = $now - $task->tracking;
							$timer_start = $task->tracking;
							$task->time_spent = $task->time_spent + $diff;
							$task->tracking = "";
							$attributes = array(
								'task_id' => $task->id,
								'user_id' => $task->user_id,
								'project_id' => $task->project_id,
								'client_id' => 0,
								'time' => $diff,
								'start' => $timer_start,
								'end' => $now
							);
							$timesheet = ProjectHasTimesheet::create($attributes);
						}
					}
					$task->update_attributes($_POST);
					if (!$task) {
						$this->session->set_flashdata('message', 'error:' . $this->lang->line('messages_save_task_error'));
					} else {
						$this->session->set_flashdata('message', 'success:' . $this->lang->line('messages_save_task_success'));
					}
					redirect('projects/view/' . $id);
				} else {
					$this->theme_view = 'modal';
					$this->view_data['project'] = Project::find($id);
					$this->view_data['intervenants'] = Intervenant::find("all", array("conditions" => array("visible=?", 1)));
					$this->view_data['title'] = $this->lang->line('application_edit_task');
					$this->view_data['form_action'] = 'projects/tasks/' . $id . '/update/' . $task_id;
					$this->content_view = 'projects/_tasks';
				}
				break;
			case 'check':
				$task = ProjectHasTask::find($task_id);
				if ($task->status == 'done') {
					$task->status = 'open';
				} else {
					$task->status = 'done';
				}
				$task->save();
				$project = Project::find($id);
				$tasks = ProjectHasTask::count(array('conditions' => 'project_id = ' . $id));
				$tasks_done = ProjectHasTask::count(array('conditions' => array('status = ? AND project_id = ?', 'done', $id)));
				if ($project->progress_calc == 1) {
					if ($tasks) {
						$progress = round($tasks_done / $tasks * 100);
					}
					$attr = array('progress' => $progress);
					$project->update_attributes($attr);
				}
				if (!$task) {
					$this->session->set_flashdata('message', 'error:' . $this->lang->line('messages_save_task_error'));
				}
				$this->theme_view = 'ajax';
				$this->content_view = 'projects';
				break;
			case 'delete':
				$task = ProjectHasTask::find($task_id);
				$task->delete();
				if (!$task) {
					$this->session->set_flashdata('message', 'error:' . $this->lang->line('messages_delete_task_error'));
				} else {
					$this->session->set_flashdata('message', 'success:' . $this->lang->line('messages_delete_task_success'));
				}
				redirect('projects/view/' . $id);
				break;
			default:
				$this->view_data['project'] = Project::find($id);
				$this->content_view = 'projects/tasks';
				break;
		}
	}
	function milestones($id = FALSE, $condition = FALSE, $milestone_id = FALSE)
	{
		$this->view_data['submenu'] = array(
			$this->lang->line('application_back') => 'projects',
			$this->lang->line('application_overview') => 'projects/view/' . $id,
		);
		switch ($condition) {
			case 'add':
				$this->content_view = 'projects/_milestones';
				if ($_POST) {
					unset($_POST['send']);
					unset($_POST['files']);
					$description = $_POST['description'];
					$_POST = array_map('htmlspecialchars', $_POST);
					$_POST['description'] = $description;
					$_POST['project_id'] = $id;
					$milestone = ProjectHasMilestone::create($_POST);
					if (!$milestone) {
						$this->session->set_flashdata('message', 'error:' . $this->lang->line('messages_save_milestone_error'));
					} else {
						$this->session->set_flashdata('message', 'success:' . $this->lang->line('messages_save_milestone_success'));
					}
					redirect('projects/view/' . $id);
				} else {
					$this->theme_view = 'modal';
					$this->view_data['project'] = Project::find($id);
					$this->view_data['title'] = $this->lang->line('application_add_milestone');
					$this->view_data['form_action'] = 'projects/milestones/' . $id . '/add';
					$this->content_view = 'projects/_milestones';
				}
				break;
			case 'update':
				$this->content_view = 'projects/_milestones';
				$this->view_data['milestone'] = ProjectHasMilestone::find($milestone_id);
				if ($_POST) {
					unset($_POST['send']);
					unset($_POST['files']);
					$description = $_POST['description'];
					$_POST = array_map('htmlspecialchars', $_POST);
					$_POST['description'] = $description;
					$milestone_id = $_POST['id'];
					$milestone = ProjectHasMilestone::find($milestone_id);
					$milestone->update_attributes($_POST);
					if (!$milestone) {
						$this->session->set_flashdata('message', 'error:' . $this->lang->line('messages_save_milestone_error'));
					} else {
						$this->session->set_flashdata('message', 'success:' . $this->lang->line('messages_save_milestone_success'));
					}
					redirect('projects/view/' . $id);
				} else {
					$this->theme_view = 'modal';
					$this->view_data['project'] = Project::find($id);
					$this->view_data['title'] = $this->lang->line('application_edit_milestone');
					$this->view_data['form_action'] = 'projects/milestones/' . $id . '/update/' . $milestone_id;
					$this->content_view = 'projects/_milestones';
				}
				break;
			case 'delete':
				$milestone = ProjectHasMilestone::find($milestone_id);

				foreach ($milestone->project_has_tasks as $value) {
					$value->milestone_id = "";
					$value->save();
				}
				$milestone->delete();
				if (!$milestone) {
					$this->session->set_flashdata('message', 'error:' . $this->lang->line('messages_delete_milestone_error'));
				} else {
					$this->session->set_flashdata('message', 'success:' . $this->lang->line('messages_delete_milestone_success'));
				}
				redirect('projects/view/' . $id);
				break;
			default:
				$this->view_data['project'] = Project::find($id);
				$this->content_view = 'projects/milestones';
				break;
		}
	}
	function notes($id = FALSE)
	{
		if ($_POST) {
			unset($_POST['send']);
			$_POST = array_map('htmlspecialchars', $_POST);
			$_POST['note'] = strip_tags($_POST['note']);
			$project = Project::find($id);
			$project->update_attributes($_POST);
		}
		$this->theme_view = 'ajax';
	}
	function media($id = FALSE, $condition = FALSE, $media_id = FALSE)
	{
		$this->load->helper('notification');
		$this->view_data['submenu'] = array(
			$this->lang->line('application_back') => 'projects',
			$this->lang->line('application_overview') => 'projects/view/' . $id,
			$this->lang->line('application_tasks') => 'projects/tasks/' . $id,
			$this->lang->line('application_media') => 'projects/media/' . $id,
		);
		switch ($condition) {
			case 'view':

				if ($_POST) {
					unset($_POST['send']);
					unset($_POST['_wysihtml5_mode']);
					unset($_POST['files']);
					//$_POST = array_map('htmlspecialchars', $_POST);
					$_POST['text'] = $_POST['message'];
					unset($_POST['message']);
					$_POST['project_id'] = $id;
					$_POST['media_id'] = $media_id;
					$_POST['from'] = $this->user->firstname . ' ' . $this->user->lastname;
					$this->view_data['project'] = Project::find_by_id($id);
					$this->view_data['media'] = ProjectHasFile::find($media_id);
					$message = Message::create($_POST);
					if (!$message) {
						$this->session->set_flashdata('message', 'error:' . $this->lang->line('messages_save_message_error'));
					} else {
						$this->session->set_flashdata('message', 'success:' . $this->lang->line('messages_save_message_success'));

						foreach ($this->view_data['project']->project_has_workers as $workers) {
							send_notification($workers->user->email, "[" . $this->view_data['project']->name . "] New comment", 'New comment on media file: ' . $this->view_data['media']->name . '<br><strong>' . $this->view_data['project']->name . '</strong>');
						}
						if (isset($this->view_data['project']->company->client->email)) {
							$access = explode(',', $this->view_data['project']->company->client->access);
							if (in_array('12', $access)) {
								send_notification($this->view_data['project']->company->client->email, "[" . $this->view_data['project']->name . "] New comment", 'New comment on media file: ' . $this->view_data['media']->name . '<br><strong>' . $this->view_data['project']->name . '</strong>');
							}
						}
					}
					redirect('projects/media/' . $id . '/view/' . $media_id);
				}
				$this->content_view = 'projects/view_media';
				$this->view_data['media'] = ProjectHasFile::find($media_id);
				$this->view_data['form_action'] = 'projects/media/' . $id . '/view/' . $media_id;
				$this->view_data['filetype'] = explode('.', $this->view_data['media']->filename);
				$this->view_data['filetype'] = $this->view_data['filetype'][1];
				$this->view_data['backlink'] = 'projects/view/' . $id;
				break;
			case 'add':
				$this->content_view = 'projects/_media';
				$this->view_data['project'] = Project::find($id);
				if ($_POST) {
					$config['upload_path'] = './files/media/';
					$config['encrypt_name'] = TRUE;
					$config['allowed_types'] = '*';

					$this->load->library('upload', $config);

					if (!$this->upload->do_upload()) {
						$error = $this->upload->display_errors('', ' ');
						$this->session->set_flashdata('message', 'error:' . $error);
						redirect('projects/media/' . $id);
					} else {
						$data = array('upload_data' => $this->upload->data());

						$_POST['filename'] = $data['upload_data']['orig_name'];
						$_POST['savename'] = $data['upload_data']['file_name'];
						$_POST['type'] = $data['upload_data']['file_type'];
						$_Post['phase'] = "";
						//check image processor extension
						if (extension_loaded('gd2')) {
							$lib = 'gd2';
						} else {
							$lib = 'gd';
						}
						$config['image_library'] = $lib;
						$config['source_image'] = './files/media/' . $_POST['savename'];
						$config['new_image'] = './files/media/thumb_' . $_POST['savename'];
						$config['create_thumb'] = TRUE;
						$config['thumb_marker'] = "";
						$config['maintain_ratio'] = TRUE;
						$config['width'] = 170;
						$config['height'] = 170;
						$config['master_dim'] = "height";
						$config['quality'] = "100%";

						$this->load->library('image_lib', $config);
						$this->image_lib->resize();
					}

					unset($_POST['send']);
					unset($_POST['userfile']);
					unset($_POST['file-name']);
					unset($_POST['files']);
					$_POST = array_map('htmlspecialchars', $_POST);
					$_POST['project_id'] = $id;
					$_POST['user_id'] = $this->user->id;
					$media = ProjectHasFile::create($_POST);
					if (!$media) {
						$this->session->set_flashdata('message', 'error:' . $this->lang->line('messages_save_media_error'));
					} else {
						$this->session->set_flashdata('message', 'success:' . $this->lang->line('messages_save_media_success'));

						$attributes = array('subject' => $this->lang->line('application_new_media_subject'), 'message' => '<b>' . $this->user->firstname . ' ' . $this->user->lastname . '</b> ' . $this->lang->line('application_uploaded') . ' ' . $_POST['name'], 'datetime' => time(), 'project_id' => $id, 'type' => 'media', 'user_id' => $this->user->id);
						$activity = ProjectHasActivity::create($attributes);

						foreach ($this->view_data['project']->project_has_workers as $workers) {
							send_notification($workers->user->email, "[" . $this->view_data['project']->name . "] " . $this->lang->line('application_new_media_subject'), $this->lang->line('application_new_media_file_was_added') . ' <strong>' . $this->view_data['project']->name . '</strong>');
						}
						if (isset($this->view_data['project']->company->client->email)) {
							$access = explode(',', $this->view_data['project']->company->client->access);
							if (in_array('12', $access)) {
								send_notification($this->view_data['project']->company->client->email, "[" . $this->view_data['project']->name . "] " . $this->lang->line('application_new_media_subject'), $this->lang->line('application_new_media_file_was_added') . ' <strong>' . $this->view_data['project']->name . '</strong>');
							}
						}
					}
					redirect('projects/view/' . $id);
				} else {
					$this->theme_view = 'modal';
					$this->view_data['title'] = $this->lang->line('application_add_media');
					$this->view_data['form_action'] = 'projects/media/' . $id . '/add';
					$this->content_view = 'projects/_media';
				}
				break;
			case 'update':
				$this->content_view = 'projects/_media';
				$this->view_data['media'] = ProjectHasFile::find($media_id);
				$this->view_data['project'] = Project::find($id);
				if ($_POST) {
					unset($_POST['send']);
					unset($_POST['_wysihtml5_mode']);
					unset($_POST['files']);
					$_POST = array_map('htmlspecialchars', $_POST);
					$media_id = $_POST['id'];
					$media = ProjectHasFile::find($media_id);
					$media->update_attributes($_POST);
					if (!$media) {
						$this->session->set_flashdata('message', 'error:' . $this->lang->line('messages_save_media_error'));
					} else {
						$this->session->set_flashdata('message', 'success:' . $this->lang->line('messages_save_media_success'));
					}
					redirect('projects/view/' . $id);
				} else {
					$this->theme_view = 'modal';
					$this->view_data['title'] = $this->lang->line('application_edit_media');
					$this->view_data['form_action'] = 'projects/media/' . $id . '/update/' . $media_id;
					$this->content_view = 'projects/_media';
				}
				break;
			case 'delete':
				$media = ProjectHasFile::find($media_id);
				$media->delete();
				$this->load->database();
				$sql = "DELETE FROM messages WHERE media_id = $media_id";
				$this->db->query($sql);
				if (!$media) {
					$this->session->set_flashdata('message', 'error:' . $this->lang->line('messages_delete_media_error'));
				} else {
					unlink('./files/media/' . $media->savename);
					$this->session->set_flashdata('message', 'success:' . $this->lang->line('messages_delete_media_success'));
				}
				redirect('projects/view/' . $id);
				break;
			default:
				$this->view_data['project'] = Project::find($id);
				$this->content_view = 'projects/view/' . $id;
				break;
		}
	}
	function deletemessage($project_id, $media_id, $id)
	{
		$message = Message::find($id);
		if ($message->from == $this->user->firstname . " " . $this->user->lastname || $this->user->admin == "1") {
			$message->delete();
		}
		if (!$message) {
			$this->session->set_flashdata('message', 'error:' . $this->lang->line('messages_delete_message_error'));
		} else {
			$this->session->set_flashdata('message', 'success:' . $this->lang->line('messages_delete_message_success'));
		}
		redirect('projects/media/' . $project_id . '/view/' . $media_id);
	}
	function tracking($id = FALSE)
	{
		$project = Project::find($id);
		if (empty($project->tracking)) {
			$project->update_attributes(array('tracking' => time()));
		} else {
			$timeDiff = time() - $project->tracking;
			$project->update_attributes(array('tracking' => '', 'time_spent' => $project->time_spent + $timeDiff));
		}
		//var_dump('traking');
		redirect('projects/view/' . $id);
	}

	function download($media_id = FALSE)
	{

		$this->load->helper('download');
		$this->load->helper('file');
		$media = ProjectHasFile::find($media_id);
		$media->download_counter = $media->download_counter + 1;
		$media->save();

		$file = './files/media/' . $media->savename;
		$mime = get_mime_by_extension($file);
		if (file_exists($file)) {
			header('Content-Description: File Transfer');
			header('Content-Type: ' . $mime);
			header('Content-Disposition: attachment; filename=' . basename($media->filename));
			header('Content-Transfer-Encoding: binary');
			header('Expires: 0');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Pragma: public');
			header('Content-Length: ' . filesize($file));
			readfile($file);
			@ob_clean();
			@flush();
			exit;
		}
	}
	function activity($id = FALSE, $condition = FALSE, $activityID = FALSE)
	{
		$this->load->helper('notification');
		$project = Project::find_by_id($id);
		//$activity = ProjectHasAktivity::find_by_id($activityID);
		switch ($condition) {
			case 'add':
				if ($_POST) {
					unset($_POST['send']);
					$_POST['subject'] = htmlspecialchars($_POST['subject']);
					$_POST['message'] = strip_tags($_POST['message'], '<br><br/><p></p><a></a><b></b><i></i><u></u><span></span>');
					$_POST['project_id'] = $id;
					$_POST['user_id'] = $this->user->id;
					$_POST['type'] = "comment";
					unset($_POST['files']);
					$_POST['datetime'] = time();
					$activity = ProjectHasActivity::create($_POST);
					if (!$activity) {
						$this->session->set_flashdata('message', 'error:' . $this->lang->line('messages_save_error'));
					} else {
						$this->session->set_flashdata('message', 'success:' . $this->lang->line('messages_save_success'));
						foreach ($project->project_has_workers as $workers) {
							send_notification($workers->user->email, "[" . $project->name . "] " . $_POST['subject'], $_POST['message'] . '<br><strong>' . $project->name . '</strong>');
						}
						if (isset($project->company->client->email)) {
							$access = explode(',', $project->company->client->access);
							if (in_array('12', $access)) {
								send_notification($project->company->client->email, "[" . $project->name . "] " . $_POST['subject'], $_POST['message'] . '<br><strong>' . $project->name . '</strong>');
							}
						}
					}
					//redirect('projects/view/'.$id);
				}
				break;
			case 'update':

				break;
			case 'delete':

				break;
		}
	}
	//All refernce of project 
	function AllReference()
	{
		//$reference = $this->project->getAllNumProject();
		$references = Project::find('all');
		$output = '';
		foreach ($references as $reference) {
			$output = $reference->project_num . ',' . $output;
		}
		header('Content-Type: application/json');
		echo json_encode($output);
		exit();
	}

	function getSpeakerValue($speaker)
	{
		if (
			"inter" . strpos($speaker, "inter") == "inter"
			&& "user" . strpos($speaker, "user") != "user"
		) {
			$speakerUser = str_replace('user', "", $speaker);
		} else {
			$speakerInter = str_replace('inter', "", $speaker);
		}
		if (isset($speakerInter)) {
			$output = Intervenant::find($speakerInter)->value;
		} else {
			$output = 0;
		}
		header('Content-Type: application/json');
		echo json_encode($output);
		exit();
	}


	//Sous Projet
	public function addSousProjet($proj_id)
	{
		if ($_POST) {
			$etat = $this->input->post('create_tickets');
			$etat = (!is_null($etat) && $etat) ? 1 : 0;
			$data = array(
				'project_id' => $proj_id,
				'name' => $this->input->post('name'),
				'description' => $this->input->post('description'),
				'create_tickets' => $etat,
				'created_by' => $this->user->id,
				'created_at' => date("Y-m-d h:i:s"),
			);
			$new_id = $this->settingTables->addData($data, ProjectHasSubProject::table_name());
			$proj_pere = Project::find(array('id' => $proj_id));

			$ins = array(
				'code' => $proj_pere->project_num . '-' . $new_id,
			);
			$this->settingTables->updatDataById($ins, $new_id, ProjectHasSubProject::table_name());

			if ($etat == 1) {
				//créer les tickets 
				$tickets_par_defaut = Categorie_tickets::all(array('categorie_type_id' => $proj_pere->type_projet));
				$ins = array();
				$option = array("id_vcompanies" => $_SESSION['current_company']);
				$ticket_reference = Setting::find($option);
				$core_ticket_ref = $ticket_reference->ticket_reference;
				$nb = 0;
				foreach ($tickets_par_defaut as $key => $item) {
					$ins[] = array(
						'subject' => $item->name,
						'text' => $item->description,
						'project_id' => $proj_id,
						'sub_project_id' => $new_id,
						'reference' => $core_ticket_ref + $nb
					);
					$nb++;
				}
				if (count($ins) > 0) {
					$this->settingTables->addBatchData($ins, Ticket::table_name());
					$ticket_reference->update_attributes(array('ticket_reference' => $core_ticket_ref + $nb));
				}
			}
			redirect('projects/view/' . $proj_id);
		} else {
			$this->theme_view = 'modal';
			$this->view_data['title'] = $this->lang->line('application-add');
			$this->view_data['create'] = true;
			$this->view_data['form_action'] = 'projects/addSousProjet/' . $proj_id;
			$this->content_view = 'projects/addSousProjet';
		}
	}
	/**
	 * Edition d'une catégorie
	 * @param  [type]
	 * @return [type]
	 */
	function editSousProjet($proj_id, $id)
	{
		if ($_POST) {
			$ins = array(
				'name' => $this->input->post('name'),
				'description' => $this->input->post('description'),
				'updated_by' => $this->user->id,
				'updated_at' => date("Y-m-d h:i:s"),
			);
			$this->settingTables->updatDataById($ins, $id, ProjectHasSubProject::table_name());

			redirect('projects/view/' . $proj_id);
		} else {
			$this->theme_view = 'modal';
			$this->view_data['title'] = $this->lang->line('application_edit');
			$this->view_data['form_action'] = 'projects/editSousProjet/' . $proj_id . '/' . $id;
			$this->view_data['data'] = $this->settingTables->getDataById($id, ProjectHasSubProject::table_name());
			$this->content_view = 'projects/addSousProjet';
		}
	}

	/**
	 * Désactiver une référence
	 * @param  [type]
	 * @return [type]
	 */
	function supprimerSousProjet($proj_id, $id)
	{
		$project = ProjectHasSubProject::find($id);
		$project->delete();

		redirect('projects/view/' . $proj_id);
	}

	function get_sub_projects()
	{
		if ($_POST) {
			$proj_id = $this->input->post("project_id");
			$proj = Project::find(array('conditions' => array('id=?', $proj_id)));
			$this->view_data['sub_projects'] = $proj->project_has_sub_projects;
			$this->theme_view = 'ajax_no_script';
			$this->content_view = 'projects/_list_sub_projects';
		}
	}
	// Trouver les contacts d'un client sélectionné
	function get_contacts_clients()
	{
		if ($_POST) {

			$company_id = $this->input->post("company_id");
			$contacts = client::find('all', array('conditions' => array('company_id=?', $company_id)));
			$this->view_data['contacts_client'] = $contacts;
			$this->theme_view = 'ajax_no_script';
			$this->content_view = 'clients/_list_contacts_client';
		}
	}

	// Trouver les natures d'une catégorie sélectionné
	function get_nature_projet()
	{
		if ($_POST) {

			$nat_projet = $this->input->post("type_projet");
			$nature = $this->nature_model->getNatureByCat($nat_projet);
			$nature = json_decode(json_encode($nature), true);
			$nature = array_column($nature, 'id_nature');
			$nature = implode(',', $nature);
			$this->view_data['natures_projetcs'] = $this->referentiels->getNature($nature);
			$this->theme_view = 'ajax_no_script';
			$this->content_view = 'projects/_list_nature_projects';
		}
	}

	//get project function

	function getProjects()
	{
		$data = $row = array();

		$model = $this->load->model('Projects_model');
		$idadmin = $this->user->salaries_id;

		if ($idadmin == NULL) {

			$projects = $this->Projects_model->getRows2($_POST);
			$i = $_POST['start'];
			foreach ($projects as $project) {
				$i++;

				$data[] = array(
					$project->project_id,
					$project->project_num,
					$project->project,
					$project->client,
					$project->start,
					$project->end,
					$project->nature,
					$project->state,
				);
			}

			$output = array(
				"draw" => $_POST['draw'],
				"recordsTotal" => $this->Projects_model->countAll($_POST),
				"recordsFiltered" => $this->Projects_model->countFiltered2($_POST),
				"data" => $data
			);

			$this->theme_view = 'blank';
			echo json_encode($output);
			//return $output ;
		} else {
			$ids = $this->user->salaries_id;

			$naturename = $this->view_data['data'] = $this->user_model->idsal($ids);

			$projects = $this->Projects_model->getRows($_POST);
			//$records= $this->Projects_model->getAll();

			$i = $_POST['start'];
			foreach ($projects as $project) {
				$i++;

				$data[] = array(
					$project->project_id,
					$project->project_num,
					$project->project,
					$project->client,
					$project->start,
					$project->end,
					$project->nature,
					$project->state,
				);
			}

			$output = array(
				"draw" => $_POST['draw'],
				"recordsTotal" => $this->Projects_model->countAll($_POST),
				"recordsFiltered" => $this->Projects_model->countFiltered($_POST),
				"data" => $data
			);

			$this->theme_view = 'blank';
			echo json_encode($output);
			//return $output ;
		}
	}
	//$draw = $this->Projects_model->countAll($_POST);
	//$records=$this->Projects_model->countAll($_POST);
	//filtred=$this->Projects_model->countFiltered($_POST);
}



//-------------------------------------------------
