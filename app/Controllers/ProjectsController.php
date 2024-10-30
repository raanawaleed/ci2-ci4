<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\ClientModel;
use App\Models\TicketModel;
use App\Models\CompanyModel;
use App\Models\SettingModel;
use App\Models\RefTypeModel;
use App\Models\InvoiceModel;
use App\Models\ProjectModel;
use App\Models\MessageModel;
use App\Models\CategorieTicketsModel;
use App\Models\IntervenantModel;
use App\Models\ProjectHasFileModel;
use App\Models\ProjectHasTaskModel;
use App\Models\ProjectHasWorkerModel;
use App\Models\ProjectHasActivityModel;
use App\Models\ProjectHasTimeSheetModel;
use App\Models\ProjectHasMilestoneModel;
use App\Models\ProjectHasSubProjectModel;

use Spipu\Html2Pdf\Html2Pdf;
use Spipu\Html2Pdf\Exception\Html2PdfException;
use CodeIgniter\Exceptions\PageNotFoundException;

use App\Controllers\BaseController;

class ProjectsController extends BaseController
{

	protected $projectModel, $messageModel, $view_data = [];
	protected $userModel, $clientModel, $milestoneModel, $categoryTicketModel;
	protected $companyModel, $projectHasTimeSheetModel, $intervenantModel;
	protected $settingModel, $ticketModel, $projectHasfileModel, $projectHasSubProjectModel;
	protected $refTypeModel, $projectHasTaskModel, $projectHasWorkerModel, $projectHasActivityModel;

	protected $natureModel; // need to check the model

	public function __construct()
	{
		$this->userModel = new UserModel();
		$this->ticketModel = new TicketModel();
		$this->clientModel = new ClientModel();
		$this->projectModel = new ProjectModel();
		$this->companyModel = new CompanyModel();
		$this->invoiceModel = new InvoiceModel();
		$this->settingModel = new SettingModel();
		$this->refTypeModel = new RefTypeModel();
		$this->messageModel = new MessageModel();
		$this->categoryTicketModel = new CategorieTicketsModel();
		$this->intervenantModel = new IntervenantModel();
		$this->projectHasfileModel = new ProjectHasFileModel();
		$this->projectHasTaskModel = new ProjectHasTaskModel();
		$this->milestoneModel = new ProjectHasMilestoneModel();
		$this->projectHasWorkerModel = new ProjectHasWorkerModel();
		$this->projectHasWorkerModel = new ProjectHasWorkerModel();
		$this->projectHasActivityModel = new ProjectHasActivityModel();
		$this->projectHasSubProjectModel = new ProjectHasSubProjectModel();
		$this->projectHasTimeSheetModel = new ProjectHasTimeSheetModel();

		$this->load->database();
		$this->checkUserAccess();
	}

	private function checkUserAccess(): void
	{
		if (!session()->get('user') && !session()->get('client')) {
			return redirect()->to('login');
		}
	}

	public function index(): void
	{
		$salariesId = session()->get('user')->salaries_id;

		if ($salariesId === null) {
			$this->view_data['data'] = $this->projectModel->findAll();
		} else {
			$this->view_data['data'] = $this->userModel->getProjectsBySalaryId($salariesId);
		}

		return view('projects/all_projects', $this->view_data);
	}


	//Filtrer les projets
	public function filter(string $condition): void
	{
		$options = match ($condition) {
			'open' => 'progress < 100',
			'closed' => 'progress = 100',
			'all' => 'progress = 100 OR progress < 100',
			default => 'progress = 100 OR progress < 100',
		};

		$this->view_data['projects'] = $this->projectModel->where($options)->findAll();

		$this->view_data['projects_assigned_to_me'] = $this->projectHasWorkerModel
			->select('COUNT(DISTINCT projects.id) AS amount')
			->join('projects', 'projects.id = project_has_workers.project_id')
			->where('projects.progress != 100')
			->where('project_has_workers.intervenant_id', session()->get('user')->id)
			->first();

		$this->view_data['tasks_assigned_to_me'] = $this->projectHasTaskModel
			->where(['user_id' => session()->get('user')->id, 'status' => 'open'])
			->countAllResults();

		$now = time();
		$beginningOfWeek = strtotime('last Monday', $now);
		$endOfWeek = strtotime('next Sunday', $now) + 86400;

		$this->view_data['projects_opened_this_week'] = $this->projectModel
			->select('COUNT(id) AS amount, DATE_FORMAT(FROM_UNIXTIME(datetime), "%w") AS date_day, DATE_FORMAT(FROM_UNIXTIME(datetime), "%Y-%m-%d") AS date_formatted')
			->where('datetime >=', $beginningOfWeek)
			->where('datetime <=', $endOfWeek)
			->findAll();

		return view('projects/all', $this->view_data);
	}

	public function create()
	{
		if ($this->request->getMethod() === 'post') {
			$data = $this->request->getPost();
			$data['datetime'] = time();
			unset($data['send'], $data['files']);

			$data = array_map('htmlspecialchars', $data);
			$data['creation_date'] = date("Y-m-d");
			$year = date("Y");

			// Retrieve project reference logic here
			$projectReference = $this->projectModel->getNextProjectReference();

			// Project number generation logic
			$data['project_num'] = $this->generateProjectNumber($projectReference, $data);

			$project = $this->projectModel->insert($data);
			if (!$project) {
				session()->setFlashdata('message', 'error: Project creation failed.');
			} else {
				session()->setFlashdata('message', 'success: Project created successfully.');
			}
			return redirect()->to('projects');
		} else {
			// Load view data
			$this->view_data['companies'] = $this->companyModel->findAll();
			$this->view_data['core_settings'] = $this->projectModel->getCoreSettings();
			return view('projects/_project', $this->view_data);
		}
	}
	private function generateProjectNumber($projectReference, $data)
	{
		// Custom logic to generate project number based on your rules
		return 'PRJ-' . $projectReference->project_reference . '-' . date('Y'); // Example format
	}

	//Mettre à jour un projet
	function update($id = FALSE)
	{
		if ($_POST) {
			unset($_POST['send'], $_POST['files']);
			$id = $_POST['id'];

			// Sanitize input
			$_POST = array_map('htmlspecialchars', $_POST);

			// Set default values
			$_POST["progress_calc"] = $_POST["progress_calc"] ?? 0;
			$_POST["hide_tasks"] = $_POST["hide_tasks"] ?? 0;
			$_POST["enable_client_tasks"] = $_POST["enable_client_tasks"] ?? 0;

			$project = $this->projectModel->find($id);
			if ($project) {
				$_POST['project_num'] = $_POST['reference'];
				unset($_POST['reference']);
				$project->update_attributes($_POST);

				// Set flash message based on the outcome
				$messageKey = $project ? 'messages_save_project_success' : 'messages_save_project_error';
				$this->session->set_flashdata('message', ($project ? 'success:' : 'error:') . $this->lang->line($messageKey));
				redirect('projects/view/' . $id);

			} else {
				// Handle project not found
				$this->session->set_flashdata('message', 'error:' . $this->lang->line('messages_project_not_found'));
				redirect('projects');
			}
		} else {

			// Prepare data for the view
			$this->view_data['companies'] = $this->companyModel->find('all', ['conditions' => ['inactive=?', 0]]);
			$project = $this->projectModel->find($id);

			if ($project) {

				$this->view_data['project'] = $project;
				$this->view_data['contacts_client'] = $this->clientModel->find('all', ['company_id' => $project->company_id]);

				if ($this->view_data['project']->company_id != 0) {
					$this->view_data['project']->company_id = $this->companyModel->find($this->view_data['project']->company_id);
				}

				$this->theme_view = 'modal';
				$this->view_data['title'] = $this->lang->line('application_edit_project');
				$this->view_data['form_action'] = 'projects/update';
				$this->view_data['clients'] = $this->db->query("SELECT * FROM  companies")->result();

				// Retrieve project category details
				$idType = $this->refType->getRefTypeByName("catégorie projet")->id;
				$this->view_data['chef_projet'] = $this->userModel->getAll();
				$this->view_data['categorie_projets'] = $this->referentiels->getReferentielsByIdType($idType);

				// Fetch project nature
				$nat_projet = $project->type_projet;
				$nature = $this->nature_model->getNatureByCat($nat_projet);
				$this->view_data['natures_projetcs'] = $this->referentiels->getNature(implode(',', array_column(json_decode(json_encode($nature), true), 'id_nature')));

				$this->view_data['etats_projet'] = $this->referentiels->getTabReferentielsByIdType($this->config->item('type_id_etat_projet'));
				$this->view_data['etat_encours'] = $this->referentiels->getReferentiels($this->config->item('type_id_etat_projet'), $this->config->item('type_occ_code_etat_projet_en_cours'));
				$this->content_view = 'projects/_project';

			} else {

				// Handle project not found
				$this->session->set_flashdata('message', 'error:' . $this->lang->line('messages_project_not_found'));
				redirect('projects');

			}
		}
	}

	public function view(int $id, ?int $taskId = null)
	{
		// Get current user
		$currentUser = $this->userModel->find($this->session->get('user_id'));

		// Fetch project and related data
		$project = $this->projectModel->find($id);
		if (!$project) {
			throw new PageNotFoundException("Project not found");
		}

		$this->view_data['submenu'] = [];
		$this->view_data['user'] = $currentUser;
		$this->view_data['project'] = $project;

		$this->view_data['chef_projet'] = $this->userModel->getUserById($project->chef_projet_id);
		$this->view_data['chef_client'] = $this->clientModel->getUserById($project->sub_client_id);
		$this->view_data['sub_projects'] = $this->projectHasSubProjectModel->getSubProjectsByProjectId($id);

		// Temps passé sur les tâches des sous projets
		$subProjectsHeuresPointees = $this->projectModel->getPeriodTickets_Byprojet($id, true, true);
		$tabSubProjectsHeuresPointees = [];
		foreach ($subProjectsHeuresPointees as $proj) {
			$tabSubProjectsHeuresPointees[$proj->project_pere] = $proj;
		}
		$this->view_data['tab_sub_projects_heures_pointees'] = $tabSubProjectsHeuresPointees;

		// URLs for actions
		$this->view_data['url_add_ref'] = "projects/addSousProjet/$id";
		$this->view_data['url_update_ref'] = 'projects/editSousProjet';
		$this->view_data['url_delete_ref'] = 'projects/supprimerSousProjet';

		// Get users assigned to the project
		$this->view_data['users'] = $this->projectHasWorkerModel->getWorkersByProjectId($id);

		// Fetch invoices and quotes
		$this->view_data['facture'] = $this->invoiceModel->getByIdProject($id);
		$this->view_data['project_has_invoices'] = $this->invoiceModel->getByIdProject($id);
		$this->view_data['devis'] = $this->devisModel->getByIdProject($id);

		// Process project invoices
		if ($this->view_data['project_has_invoices']) {
			foreach ($this->view_data['project_has_invoices'] as $val) {
				// Assuming $val contains invoice_id and project_id
				$val->project_id = $this->projectModel->find($val->project_id);
				$val->invoice_id = $this->invoiceModel->find($val->invoice_id);
				if ($val->invoice_id) {
					$val->invoice_id->company_id = $this->clientModel->find($val->invoice_id->company_id);
					$val->invoice_id->status = $this->referentiels->getReferentielsById($val->invoice_id->status)->name;
					$refType = $this->refType->getRefTypeByName($val->invoice_id->currency)->id;
					$val->invoice_id->notes = $this->referentiels->getReferentielsByIdType($refType)->name;
				}
			}
		}

		// Fetch tickets and subjects
		$this->view_data['ticket'] = $this->ticketModel->getTicketByTypeProjet($id);
		$this->view_data['subject'] = $this->ticketModel->getTicketSubjectByIdProject($id);
		$this->view_data['Heures'] = calcul_heure($this->view_data['subject']);

		// Count tasks
		$tasks = $this->projectHasTaskModel->countTasksByProjectId($id);
		$this->view_data['alltasks'] = $tasks;
		$this->view_data['opentasks'] = $this->projectHasTaskModel->where('status !=', 'done')->where('project_id', $id)->countAllResults();
		$this->view_data['usercountall'] = $this->userModel->where('status', 'active')->countAllResults();
		$this->view_data['usersassigned'] = $this->projectHasWorkerModel->where('project_id', $id)->countAllResults();
		$this->view_data['assigneduserspercent'] = round($this->view_data['usersassigned'] / $this->view_data['usercountall'] * 100);

		// Statistics
		$daysOfWeek = getDatesOfWeek();
		$this->view_data['dueTasksStats'] = $this->projectHasTaskModel->getDueTaskStats($id, $daysOfWeek[0], $daysOfWeek[6]);
		$this->view_data['startTasksStats'] = $this->projectHasTaskModel->getStartTaskStats($id, $daysOfWeek[0], $daysOfWeek[6]);
		$this->view_data["labels"] = "";
		$this->view_data["line1"] = "";
		$this->view_data["line2"] = "";
		$this->view_data['current_user'] = $currentUser;

		foreach ($daysOfWeek as $day) {
			$counter = 0;
			$counter2 = 0;
			foreach ($this->view_data['dueTasksStats'] as $value) {
				if ($value->due_date == $day) {
					$counter = $value->tasksdue;
				}
			}
			foreach ($this->view_data['startTasksStats'] as $value) {
				if ($value->start_date == $day) {
					$counter2 = $value->tasksdue;
				}
			}
			$this->view_data["labels"] .= '"' . $day . '",';
			$this->view_data["line1"] .= $counter . ",";
			$this->view_data["line2"] .= $counter2 . ",";
		}

		// Time calculations
		$this->view_data['time_days'] = round((strtotime($project->end) - strtotime($project->start)) / 3600 / 24);
		$this->view_data['time_left'] = $this->view_data['time_days'];
		$this->view_data['timeleftpercent'] = 100;

		if (strtotime($project->start) < time() && strtotime($project->end) > time()) {
			$this->view_data['time_left'] = round((strtotime($project->end) - time()) / 3600 / 24);
			$this->view_data['timeleftpercent'] = $this->view_data['time_left'] / $this->view_data['time_days'] * 100;
		}

		if (strtotime($project->end) < time()) {
			$this->view_data['time_left'] = 0;
			$this->view_data['timeleftpercent'] = 0;
		}

		// User's tasks
		$this->view_data['allmytasks'] = $this->projectHasTaskModel->where(['project_id' => $id, 'user_id' => $currentUser->id])->findAll();
		$this->view_data['mytasks'] = $this->projectHasTaskModel->where(['status !=' => 'done', 'project_id' => $id, 'user_id' => $currentUser->id])->countAllResults();
		$this->view_data['tasksWithoutMilestone'] = $this->projectHasTaskModel->where(['milestone_id' => 0, 'project_id' => $id])->findAll();

		$tasks_done = $this->projectHasTaskModel->where(['status' => 'done', 'project_id' => $id])->countAllResults();
		$this->view_data['progress'] = $project->progress;

		if ($project->progress_calc == 1 && $tasks) {
			$this->view_data['progress'] = round($tasks_done / $tasks * 100);
			$this->projectModel->update($id, ['progress' => $this->view_data['progress']]);
		}

		// Load the view
		return view('projects/view', $this->view_data);
	}

	public function sortList(string $sort = null, string $list = null)
	{
		if ($sort) {
			$sortArray = explode("-", $sort);
			$sortNumber = 1;
			foreach ($sortArray as $value) {
				$task = $this->projectHasTaskModel->find($value);
				if ($task) {
					if ($list !== "task-list") {
						$task->milestone_order = $sortNumber;
					} else {
						$task->task_order = $sortNumber;
					}
					$this->projectHasTaskModel->save($task);
					$sortNumber++;
				}
			}
		}
		return $this->response->setStatusCode(204); // No Content
	}

	public function sortMilestoneList(string $sort = null)
	{
		if ($sort) {
			$sortArray = explode("-", $sort);
			$sortNumber = 1;
			foreach ($sortArray as $value) {
				$milestone = $this->milestoneModel->find($value);
				if ($milestone) {
					$milestone->orderindex = $sortNumber;
					$this->milestoneModel->save($milestone);
					$sortNumber++;
				}
			}
		}
		return $this->response->setStatusCode(204); // No Content
	}

	public function moveTaskToMilestone(int $taskId = null, int $listId = null)
	{
		if ($listId && $taskId) {
			$task = $this->projectHasTaskModel->find($taskId);
			if ($task) {
				$task->milestone_id = $listId;
				$this->projectHasTaskModel->save($task);
			}
		}
		return $this->response->setStatusCode(204); // No Content
	}

	public function taskChangeAttribute()
	{
		if ($this->request->getMethod() === 'post') {
			$name = $this->request->getPost("name");
			$taskId = $this->request->getPost("pk");
			$value = $this->request->getPost("value");
			$task = $this->projectHasTaskModel->find($taskId);
			if ($task) {
				$task->{$name} = $value;
				$this->projectHasTaskModel->save($task);
			}
		}
		return $this->response->setStatusCode(204); // No Content
	}

	function task_start_stop_timer(int $taskId)
	{

		$currentItem = $this->projectHasTaskModel->select('time_spent')->where('id', $taskId)->first();

		$task = $this->projectHasTaskModel->find($taskId);
		if ($task->tracking != 0) {
			$now = time();
			$diff = $now - $task->tracking;
			$timerStart = $task->tracking;
			$task->time_spent += $diff;
			$task->tracking = null;

			// Add time to timesheet
			$attributes = [
				'task_id' => $task->id,
				'user_id' => $task->user_id,
				'project_id' => $task->project_id,
				'client_id' => 0,
				'time' => $diff,
				'start' => $timerStart,
				'end' => $now
			];
			$this->projectHasTimeSheetModel->insert($attributes);

			$date = date("Y-m-d");
			$data = [
				'id_task' => $taskId,
				'time_task' => $task->time_spent - $currentItem->time_spent,
				'date_task' => $date
			];

			$this->db->table('time_date_tasks')->insert($data);

		} else {
			$task->tracking = time();
		}
		$this->projectHasTaskModel->save($task);
		return $this->response->setStatusCode(204);
	}

	public function getMilestoneList(int $projectId)
	{
		$milestoneList = '';
		$project = $this->projectModel->find($projectId);
		if ($project) {
			foreach ($project->project_has_milestones as $value) {
				$milestoneList .= json_encode(['value' => $value->id, 'text' => $value->name]) . ',';
			}
		}
		return $this->response->setJSON(rtrim($milestoneList, ',')); // Return as JSON
	}

	public function copy(int $id = null)
	{
		if ($this->request->getMethod() === 'post') {
			$data = $this->request->getPost();
			unset($data['send'], $data['id'], $data['files'], $data['tasks']);
			$data['datetime'] = time();
			$data = array_map('htmlspecialchars', $data);

			$project = $this->projectModel->insert($data);
			$newProjectReference = $data['reference'] + 1;

			$option = ["id_vcompanies" => session()->get('current_company')];
			$projectReference = $this->settingModel->where($option)->first();
			if ($projectReference) {
				$projectReference->project_reference = $newProjectReference;
				$this->settingModel->save($projectReference);
			}

			if (isset($data['tasks'])) {
				$sourceProject = $this->projectModel->find($id);
				if ($sourceProject) {
					foreach ($sourceProject->project_has_tasks as $row) {
						$taskAttributes = [
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
						];
						$this->projectHasTaskModel->insert($taskAttributes);
					}
				}
			}

			session()->setFlashdata('message', 'success:' . lang('messages_create_project_success'));
			$workerAttributes = ['project_id' => $project->id, 'intervenant_id' => $this->user->id];
			$this->projectHasWorkerModel->insert($workerAttributes);
			return redirect()->to('projects');

		} else {
			$this->view_data['companies'] = $this->companyModel->where('inactive', '0')->findAll();
			$this->view_data['project'] = $this->projectModel->find($id);
			$this->view_data['title'] = lang('application_copy_project');
			$this->view_data['form_action'] = 'projects/copy';
			return view('projects/_copy', $this->view_data);
		}
	}

	public function delete(int $id)
	{
		$project = $this->projectModel->find($id);
		if ($project) {
			$this->projectModel->delete($id);
			$this->db->table('project_has_tasks')->where('project_id', $id)->delete();
			$this->db->table('tickets')->where('project_id', $id)->delete();
			session()->setFlashdata('message', 'success:' . lang('messages_delete_project_success'));
		} else {
			session()->setFlashdata('message', 'error:' . lang('messages_delete_project_error'));
		}
		return redirect()->to('projects');
	}

	public function timerReset(int $id)
	{
		$project = $this->projectModel->find($id);
		if ($project) {
			$project->time_spent = 0;
			$this->projectModel->save($project);
			session()->setFlashdata('message', 'success:' . lang('messages_timer_reset'));
		}
		return redirect()->to('projects/view/' . $id);
	}

	public function timerSet(int $id = null)
	{
		if ($this->request->getMethod() === 'post') {
			$project = $this->projectModel->find($this->request->getPost('id'));
			$hours = $this->request->getPost('hours');
			$minutes = $this->request->getPost('minutes');
			$timeSpent = ($hours * 3600) + ($minutes * 60);
			$project->time_spent = $timeSpent;
			$this->projectModel->save($project);
			session()->setFlashdata('message', 'success:' . lang('messages_timer_set'));
			return redirect()->to('projects/view/' . $this->request->getPost('id'));
		} else {
			$data['project'] = $this->projectModel->find($id);
			$data['title'] = lang('application_timer_set');
			$data['form_action'] = 'projects/timer_set';
			return view('projects/_timer', $data);
		}
	}

	function statistique()
	{
		$id = $this->request->getVar('id');
		$item = $this->db->table('salaries')->where('id', $id)->get()->getResult();
		$data["logo"] = $this->db->table('setting_document_rh')->get()->getResult();
		$data["pic"] = $this->db->table('v_companies')->where('id', session()->get('current_company'))->get()->getResult();
		$data["all"] = $item;

		// Use spipu/html2pdf
		$html2pdf = new Html2Pdf('P', 'A4', 'fr');
		$content = view('blueline/rhpaie/pdftest', $data);
		$html2pdf->writeHTML($content);
		$html2pdf->output('attestation_travail.pdf', 'D'); // Change 'D' to 'I' for inline display
	}

	public function stat()
	{
		$id = end($this->uri->getSegments());
		$nameProject = $this->statisticModel->projectName($id);
		$tasksProject = $this->statisticModel->tasksProject($id);
		$countHour = $this->statisticModel->countHour($id);
		$allTasks = $this->statisticModel->allTasks($id);

		foreach ($tasksProject as $value) {
			if (!empty($value->user_id)) {
				$userTask = $this->db->table('users')->where('id', $value->user_id)->get()->getRow();
				if ($userTask) {
					$value->name_user = $userTask->firstname . " " . $userTask->lastname;
				}
			}
		}

		$data["all"] = $tasksProject;
		$data["name_project"] = $nameProject;

		// Calculate time
		$totalSeconds = $countHour[0]->total ?? 0;
		$day = floor($totalSeconds / 86400);
		$hours = floor(($totalSeconds % 86400) / 3600);
		$minutes = floor(($totalSeconds % 3600) / 60);

		$data["count_hour"] = "{$day} jour(s) et {$hours} heure(s) et {$minutes} minute(s)";
		$data["all_tasks"] = $allTasks;

		// Use spipu/html2pdf
		$html2pdf = new Html2Pdf('P', 'A4', 'fr');
		$content = view('blueline/projects/pdf_tasks', $data);
		$html2pdf->writeHTML($content);
		$html2pdf->output('project.pdf', 'D'); // Change 'D' to 'I' for inline display
	}

	function ganttChart($id)
	{
		$ganttData = [];
		$project = $this->projectModel->find($id);
		foreach ($project->project_has_milestones as $milestone) {
			$counter = 0;
			foreach ($milestone->project_has_tasks as $task) {
				$milestoneName = ($counter === 0) ? $milestone->name : '';
				$counter++;

				$start = $task->start_date ?: $milestone->start_date;
				$end = $task->due_date ?: $milestone->due_date;

				$ganttData[] = [
					'name' => $milestoneName,
					'desc' => $task->name,
					'values' => [
						[
							'label' => $task->name,
							'from' => $start,
							'to' => $end,
						]
					]
				];
			}
		}

		return $this->response->setJSON($ganttData);
	}

	public function quickTask()
	{
		if ($this->request->getMethod() === 'post') {
			$data = $this->request->getPost();
			unset($data['send'], $data['files']); // Clean up unwanted data

			$task = $this->projectHasTaskModel->insert($data);
			return $this->response->setJSON(['id' => $task]);
		}

		return $this->response->setStatusCode(400); // Bad Request
	}

	public function generateThumbs($id = false)
	{
		if ($id) {
			$medias = $this->projectModel->find($id)->project_has_files;

			foreach ($medias as $media) {
				$thumbPath = './files/media/thumb_' . $media->savename;

				if (!file_exists($thumbPath)) {
					$this->resizeImage('./files/media/' . $media->savename, $thumbPath);
				}
			}

			return redirect()->to('projects/view/' . $id);
		}

		return $this->response->setStatusCode(404); // Not Found
	}

	protected function resizeImage($source, $destination)
	{
		// Check for GD library
		if (!extension_loaded('gd')) {
			throw new \Exception('GD library is not loaded.');
		}

		$config = [
			'image_library' => 'gd2',
			'source_image' => $source,
			'new_image' => $destination,
			'create_thumb' => true,
			'thumb_marker' => '',
			'maintain_ratio' => true,
			'width' => 170,
			'height' => 170,
			'master_dim' => 'height',
			'quality' => '100%',
		];

		$this->image_lib->initialize($config);
		if (!$this->image_lib->resize()) {
			throw new \Exception($this->image_lib->display_errors());
		}

		$this->image_lib->clear();
	}

	public function dropzone($id = false)
	{
		$config = [
			'upload_path' => './files/media/',
			'encrypt_name' => true,
			'allowed_types' => '*',
		];

		$this->load->library('upload', $config);

		if ($this->upload->do_upload("file")) {
			$data = $this->upload->data();
			$attr = [
				'name' => $data['orig_name'],
				'filename' => $data['orig_name'],
				'savename' => $data['file_name'],
				'type' => $data['file_type'],
				'date' => date("Y-m-d H:i"),
				'phase' => '',
				'project_id' => $id,
				'user_id' => $this->user->id,
			];

			$media = $this->project->create($attr);
			echo $media->id;

			$this->dropzoneResizeImage($attr['savename']);
		} else {
			$this->handleUploadError();
		}

		$this->theme_view = 'blank';
	}

	protected function dropzoneResizeImage($filename)
	{
		$lib = extension_loaded('gd2') ? 'gd2' : 'gd';
		$config = [
			'image_library' => $lib,
			'source_image' => './files/media/' . $filename,
			'new_image' => './files/media/thumb_' . $filename,
			'create_thumb' => true,
			'thumb_marker' => '',
			'maintain_ratio' => true,
			'width' => 170,
			'height' => 170,
			'master_dim' => 'height',
			'quality' => '100%',
		];

		$this->load->library('image_lib', $config);
		$this->image_lib->initialize($config);

		if (!$this->image_lib->resize()) {
			throw new \Exception($this->image_lib->display_errors());
		}

		$this->image_lib->clear();
	}
	protected function handleUploadError()
	{
		$error = $this->upload->display_errors('', ' ');
		$this->session->set_flashdata('message', $error);
		echo "Upload failed: " . $error;
	}

	function timesheets($taskid)
	{
		$timesheets = $this->projectHasTimeSheetModel->findAll(['task_id' => $taskid]);

		foreach ($timesheets as $timesheet) {
			$timesheet->user_id = $this->intervenantModel->find($timesheet->user_id);
		}

		$this->view_data['timesheets'] = $timesheets;
		$this->view_data['task'] = $this->projectHasTaskModel->find($taskid);

		$intervenants = $this->intervenantModel->findAll(['visible' => '1']);
		$allWorkers = $this->projectHasWorkerModel->findAll(['project_id' => $this->view_data['task']->project_id]);

		$users = [];
		foreach ($allWorkers as $worker) {
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
	public function timesheet_add()
	{
		if ($this->request->getMethod() === 'post') {
			$time = ($this->request->getPost("hours") * 3600) + ($this->request->getPost("minutes") * 60);
			$start = strtotime($this->request->getPost('start'));
			$end = $start + $time;

			$attr = [
				"project_id" => $this->request->getPost("project_id"),
				"user_id" => $this->request->getPost("user_id"),
				"time" => $time,
				"client_id" => 0,
				"task_id" => $this->request->getPost("task_id"),
				"start" => date("Y-m-d H:i", $start),
				"end" => date("Y-m-d H:i", $end),
				"invoice_id" => 0,
				"description" => "",
			];

			$timesheet = $this->projectHasTimeSheetModel->create($attr);
			$task = $this->projectHasTaskModel->find($timesheet->task_id);
			$task->time_spent += $time;
			$task->save();

			echo $timesheet->id;
		}

		$this->theme_view = 'blank';
	}

	public function timesheet_delete($timesheet_id)
	{
		$timesheet = $this->projectHasTimeSheetModel->find($timesheet_id);
		$task = $this->projectHasTaskModel->find($timesheet->task_id);
		$task->time_spent -= $timesheet->time;
		$task->save();

		$timesheet->delete();
		$this->theme_view = 'blank';
	}

	public function tasks($id = false, $condition = false, $task_id = false)
	{
		$this->view_data['submenu'] = [
			$this->lang->line('application_back') => 'projects',
			$this->lang->line('application_overview') => 'projects/view/' . $id,
		];
		switch ($condition) {
			case 'add':
				$this->addTask($id);
				break;

			case 'update':
				$this->updateTask($id, $task_id);
				break;

			case 'check':
				$this->checkTask($id, $task_id);
				break;

			case 'delete':
				$this->deleteTask($id, $task_id);
				break;

			default:
				$this->listTasks($id);
				break;
		}
	}

	protected function addTask($id)
	{
		if ($_POST) {
			// Sanitize and prepare data
			$_POST = array_map('htmlspecialchars', $_POST);
			$description = $_POST['description'] ?? '';
			$_POST['project_id'] = $id;

			// Handle user and intervenant assignment
			$this->handleUserAssignment($_POST);

			$task = $this->projectHasTaskModel->create($_POST);

			if (!$task) {
				$this->session->set_flashdata('message', 'error:' . $this->lang->line('messages_save_task_error'));
			} else {
				$this->session->set_flashdata('message', 'success:' . $this->lang->line('messages_save_task_success'));
			}
			return redirect('projects/view/' . $id);
		}

		$this->prepareTaskView($id, 'application_add_task', 'add');
	}

	protected function updateTask($id, $task_id)
	{
		if ($_POST) {
			// Sanitize and prepare data
			$_POST = array_map('htmlspecialchars', $_POST);
			$_POST['project_id'] = $id;

			// Handle user and intervenant assignment
			$this->handleUserAssignment($_POST);

			$task = $this->projectHasTaskModel->find($task_id);
			if ($task->user_id != $_POST['user_id']) {
				// Handle timer and timesheet
				$this->handleTimerAndTimesheet($task);
			}

			$task->update($_POST);
			if (!$task) {
				$this->session->set_flashdata('message', 'error:' . $this->lang->line('messages_save_task_error'));
			} else {
				$this->session->set_flashdata('message', 'success:' . $this->lang->line('messages_save_task_success'));
			}
			return redirect('projects/view/' . $id);
		}

		$this->prepareTaskView($id, 'application_edit_task', 'update', $task_id);
	}

	protected function checkTask($id, $task_id)
	{
		$task = $this->projectHasTaskModel->find($task_id);
		$task->status = $task->status === 'done' ? 'open' : 'done';
		$task->save();

		// Update project progress
		$this->updateProjectProgress($id);

		$this->theme_view = 'ajax';
		$this->content_view = 'projects';
	}

	protected function deleteTask($id, $task_id)
	{
		$task = $this->projectHasTaskModel->find($task_id);
		$task->delete();

		if (!$task) {
			$this->session->set_flashdata('message', 'error:' . $this->lang->line('messages_delete_task_error'));
		} else {
			$this->session->set_flashdata('message', 'success:' . $this->lang->line('messages_delete_task_success'));
		}
		return redirect('projects/view/' . $id);
	}

	protected function listTasks($id)
	{
		$this->view_data['project'] = $this->projectModel->find($id);
		$this->content_view = 'projects/tasks';
	}

	protected function handleTimerAndTimesheet($task)
	{
		if ($task->tracking != 0) {
			$now = time();
			$diff = $now - $task->tracking;
			$task->time_spent += $diff;
			$task->tracking = '';

			$attributes = [
				'task_id' => $task->id,
				'user_id' => $task->user_id,
				'project_id' => $task->project_id,
				'time' => $diff,
				'start' => $task->tracking,
				'end' => $now,
			];
			$this->projectHasTimeSheetModel->create($attributes);
		}
	}

	protected function updateProjectProgress($id)
	{
		$project = $this->projectModel->find($id);
		$tasks = $this->projectHasTaskModel->count(['conditions' => 'project_id = ' . $id]);
		$tasks_done = $this->projectHasTaskModel->count(['conditions' => ['status = ? AND project_id = ?', 'done', $id]]);
		if ($project->progress_calc == 1 && $tasks) {
			$progress = round($tasks_done / $tasks * 100);
			$project->update(['progress' => $progress]);
		}
	}

	protected function prepareTaskView($id, $titleLangKey, $formAction, $task_id = null)
	{
		$this->theme_view = 'modal';
		$this->view_data['project'] = $this->projectModel->find($id);
		$this->view_data['intervenants'] = $this->intervenantModel->findAll(['conditions' => ['visible = ?', 1]]);
		$this->view_data['users'] = $this->userModel->findAll(['conditions' => ['status = ?', 'active']]);
		$this->view_data['title'] = $this->lang->line($titleLangKey);
		$this->view_data['form_action'] = "projects/tasks/$id/$formAction" . ($task_id ? "/$task_id" : '');
		$this->content_view = 'projects/_tasks';
	}
	protected function handleUserAssignment(&$data)
	{
		// Determine if the user ID is for an intervenant or a regular user
		if (strpos($data['user_id'], 'inter') === 0) {
			// Handle intervenant assignment
			$data['intervenant_id'] = str_replace('inter', '', $data['user_id']);
			$data['user_id'] = null; // Clear user_id for intervenants

			// Check if the intervenant is already assigned to the project
			$speaker = $this->projectHasWorkerModel->find(['intervenant_id' => $data['intervenant_id'], 'project_id' => $data['project_id']]);

			if (!$speaker) {
				$intervenant = $this->intervenantModel->find($data['intervenant_id']);
				$data['value'] = $intervenant->value ?? null; // Get value for the intervenant
				$this->projectHasWorkerModel->create([
					'project_id' => $data['project_id'],
					'intervenant_id' => $data['intervenant_id'],
					'value' => $data['value']
				]);
			}
		} elseif (strpos($data['user_id'], 'user') === 0) {
			// Handle regular user assignment
			$data['user_id'] = str_replace('user', '', $data['user_id']);
			$data['intervenant_id'] = null; // Clear intervenant_id for regular users

			// Check if the user is already assigned to the project
			$speaker = $this->projectHasWorkerModel->find(['user_id' => $data['user_id'], 'project_id' => $data['project_id']]);

			if (!$speaker) {
				$this->projectHasWorkerModel->create([
					'project_id' => $data['project_id'],
					'user_id' => $data['user_id']
				]);
			}
		}
	}
	function milestones($id = FALSE, $condition = FALSE, $milestone_id = FALSE)
	{
		$this->view_data['submenu'] = array(
			$this->lang->line('application_back') => 'projects',
			$this->lang->line('application_overview') => 'projects/view/' . $id,
		);

		if ($_POST) {
			return $this->handleMilestonePost($id, $condition, $milestone_id);
		}

		switch ($condition) {
			case 'add':
				$this->view_data['project'] = $this->projectModel->find($id);
				$this->view_data['title'] = $this->lang->line('application_add_milestone');
				$this->view_data['form_action'] = 'projects/milestones/' . $id . '/add';
				$this->content_view = 'projects/_milestones';
				break;
			case 'update':
				$this->view_data['milestone'] = $this->milestoneModel->find($milestone_id);
				$this->view_data['project'] = $this->projectModel->find($id);
				$this->view_data['title'] = $this->lang->line('application_edit_milestone');
				$this->view_data['form_action'] = 'projects/milestones/' . $id . '/update/' . $milestone_id;
				$this->content_view = 'projects/_milestones';
				break;
			case 'delete':
				return $this->handleMilestoneDelete($milestone_id, $id);
			default:
				$this->view_data['project'] = $this->projectModel->find($id);
				$this->content_view = 'projects/milestones';
				break;
		}
	}

	private function handleMilestonePost($id, $condition, $milestone_id)
	{
		unset($_POST['send'], $_POST['files']);
		$_POST = array_map('htmlspecialchars', $_POST);
		$_POST['description'] = $_POST['description'];
		$_POST['project_id'] = $id;

		if ($condition === 'add') {
			$milestone = $this->milestoneModel->create($_POST);
		} else {
			$milestone = $this->milestoneModel->find($milestone_id);
			$milestone->update_attributes($_POST);
		}

		if (!$milestone) {
			$this->session->set_flashdata('message', 'error:' . $this->lang->line('messages_save_milestone_error'));
		} else {
			$this->session->set_flashdata('message', 'success:' . $this->lang->line('messages_save_milestone_success'));
		}
		redirect('projects/view/' . $id);
	}

	private function handleMilestoneDelete($milestone_id, $project_id)
	{
		$milestone = $this->milestoneModel->find($milestone_id);

		foreach ($milestone->project_has_tasks as $task) {
			$task->milestone_id = "";
			$task->save();
		}

		$milestone->delete();

		if (!$milestone) {
			$this->session->set_flashdata('message', 'error:' . $this->lang->line('messages_delete_milestone_error'));
		} else {
			$this->session->set_flashdata('message', 'success:' . $this->lang->line('messages_delete_milestone_success'));
		}
		redirect('projects/view/' . $project_id);
	}
	function notes($id = false)
	{
		if ($_POST) {
			unset($_POST['send']);
			$_POST = array_map('htmlspecialchars', $_POST);
			$_POST['note'] = strip_tags($_POST['note']);
			$project = $this->projectModel->find($id);
			$project->update_attributes($_POST);
		}
		$this->theme_view = 'ajax';
	}
	function media($id = false, $condition = false, $media_id = false)
	{
		$this->load->helper('notification');
		$this->view_data['submenu'] = array(
			$this->lang->line('application_back') => 'projects',
			$this->lang->line('application_overview') => 'projects/view/' . $id,
			$this->lang->line('application_tasks') => 'projects/tasks/' . $id,
			$this->lang->line('application_media') => 'projects/media/' . $id,
		);

		if ($_POST) {
			return $this->handleMediaPost($id, $condition, $media_id);
		}

		switch ($condition) {
			case 'view':
				$this->view_data['media'] = $this->projectHasfileModel->find($media_id);
				$this->view_data['form_action'] = 'projects/media/' . $id . '/view/' . $media_id;
				$this->view_data['filetype'] = pathinfo($this->view_data['media']->filename, PATHINFO_EXTENSION);
				$this->view_data['backlink'] = 'projects/view/' . $id;
				$this->content_view = 'projects/view_media';
				break;
			case 'add':
				$this->view_data['project'] = $this->projectModel->find($id);
				$this->view_data['title'] = $this->lang->line('application_add_media');
				$this->view_data['form_action'] = 'projects/media/' . $id . '/add';
				$this->content_view = 'projects/_media';
				break;
			case 'update':
				$this->view_data['media'] = $this->projectHasfileModel->find($media_id);
				$this->view_data['project'] = $this->projectModel->find($id);
				$this->view_data['title'] = $this->lang->line('application_edit_media');
				$this->view_data['form_action'] = 'projects/media/' . $id . '/update/' . $media_id;
				$this->content_view = 'projects/_media';
				break;
			case 'delete':
				return $this->handleMediaDelete($media_id, $id);
			default:
				$this->view_data['project'] = $this->projectModel->find($id);
				$this->content_view = 'projects/view/' . $id;
				break;
		}
	}

	private function handleMediaPost($id, $condition, $media_id)
	{
		unset($_POST['send'], $_POST['_wysihtml5_mode'], $_POST['files']);
		$_POST = array_map('htmlspecialchars', $_POST);

		if ($condition === 'view') {
			$this->processMediaComment($id, $media_id);
		} elseif ($condition === 'add') {
			$this->processMediaUpload($id);
		} elseif ($condition === 'update') {
			$this->updateMedia($media_id);
		}
	}

	private function processMediaComment($id, $media_id)
	{
		$_POST['text'] = $_POST['message'];
		unset($_POST['message']);
		$_POST['project_id'] = $id;
		$_POST['media_id'] = $media_id;
		$_POST['from'] = $this->user->firstname . ' ' . $this->user->lastname;

		$message = $this->messageModel->create($_POST);
		$this->setFlashMessage($message, 'messages_save_message');

		if ($message) {
			$this->sendMediaCommentNotifications($id, $media_id);
		}

		redirect('projects/media/' . $id . '/view/' . $media_id);
	}

	private function processMediaUpload($id)
	{
		$this->load->library('upload', $this->getUploadConfig());

		if (!$this->upload->do_upload()) {
			$this->session->set_flashdata('message', 'error:' . $this->upload->display_errors('', ' '));
			redirect('projects/media/' . $id);
		}

		$upload_data = $this->upload->data();
		$_POST['filename'] = $upload_data['orig_name'];
		$_POST['savename'] = $upload_data['file_name'];
		$_POST['type'] = $upload_data['file_type'];
		$_POST['project_id'] = $id;
		$_POST['user_id'] = $this->user->id;

		$media = $this->projectHasfileModel->create($_POST);
		$this->setFlashMessage($media, 'messages_save_media');

		if ($media) {
			$this->sendMediaUploadNotifications($id);
		}

		redirect('projects/view/' . $id);
	}

	private function sendMediaCommentNotifications($project_id, $media_id)
	{
		$project = $this->projectModel->find($project_id);
		$media = $this->projectHasfileModel->find($media_id);

		if (!$project || !$media) {
			return;
		}

		$subject = "[" . $project->name . "] New comment";
		$message = 'New comment on media file: ' . $media->name . '<br><strong>' . $project->name . '</strong>';

		// Notify project workers
		foreach ($project->project_has_workers as $worker) {
			send_notification($worker->user->email, $subject, $message);
		}

		// Notify client if applicable
		if (isset($project->company->client->email)) {
			$access = explode(',', $project->company->client->access);
			if (in_array('12', $access)) { // Assuming '12' corresponds to access level for media notifications
				send_notification($project->company->client->email, $subject, $message);
			}
		}
	}

	private function sendMediaUploadNotifications($project_id)
	{
		$project = $this->projectModel->find($project_id);

		if (!$project) {
			return;
		}

		$subject = "[" . $project->name . "] New media uploaded";
		$message = 'A new media file has been added to the project: <strong>' . $project->name . '</strong>';

		// Notify project workers
		foreach ($project->project_has_workers as $worker) {
			send_notification($worker->user->email, $subject, $message);
		}

		// Notify client if applicable
		if (isset($project->company->client->email)) {
			$access = explode(',', $project->company->client->access);
			if (in_array('12', $access)) { // Assuming '12' corresponds to access level for media notifications
				send_notification($project->company->client->email, $subject, $message);
			}
		}
	}
	private function updateMedia($media_id)
	{
		$media = $this->projectHasfileModel->find($media_id);
		$media->update_attributes($_POST);
		$this->setFlashMessage($media, 'messages_save_media');

		redirect('projects/view/' . $media->project_id);
	}

	private function handleMediaDelete($media_id, $project_id)
	{
		$media = $this->projectHasfileModel->find($media_id);
		$media->delete();
		$this->db->query("DELETE FROM messages WHERE media_id = ?", array($media_id));

		if (!$media) {
			$this->session->set_flashdata('message', 'error:' . $this->lang->line('messages_delete_media_error'));
		} else {
			unlink('./files/media/' . $media->savename);
			$this->session->set_flashdata('message', 'success:' . $this->lang->line('messages_delete_media_success'));
		}

		redirect('projects/view/' . $project_id);
	}

	private function setFlashMessage($result, $message_key)
	{
		if (!$result) {
			$this->session->set_flashdata('message', 'error:' . $this->lang->line($message_key . '_error'));
		} else {
			$this->session->set_flashdata('message', 'success:' . $this->lang->line($message_key . '_success'));
		}
	}

	private function getUploadConfig()
	{
		return [
			'upload_path' => './files/media/',
			'encrypt_name' => true,
			'allowed_types' => '*'
		];
	}
	function deletemessage($project_id, $media_id, $id)
	{
		$message = $this->messageModel->find($id);
		if ($message && ($message->from == $this->user->firstname . " " . $this->user->lastname || $this->user->admin == "1")) {
			$message->delete();
			$this->session->set_flashdata('message', 'success:' . $this->lang->line('messages_delete_message_success'));
		} else {
			$this->session->set_flashdata('message', 'error:' . $this->lang->line('messages_delete_message_error'));
		}

		redirect('projects/media/' . $project_id . '/view/' . $media_id);
	}
	function tracking($id = FALSE)
	{
		$project = $this->projectModel->find($id);
		if (empty($project->tracking)) {
			$project->update_attributes(['tracking' => time()]);
		} else {
			$timeDiff = time() - $project->tracking;
			$project->update_attributes(['tracking' => '', 'time_spent' => $project->time_spent + $timeDiff]);
		}
		redirect('projects/view/' . $id);
	}

	function download($media_id = FALSE)
	{
		$this->load->helper(['download', 'file']);
		$media = $this->projectHasfileModel->find($media_id);

		if (!$media) {
			show_error('Media not found');
		}

		// Increment download counter
		$media->download_counter += 1;
		$media->save();

		$filePath = './files/media/' . $media->savename;
		if (file_exists($filePath)) {
			$mime = get_mime_by_extension($filePath);
			header('Content-Description: File Transfer');
			header('Content-Type: ' . $mime);
			header('Content-Disposition: attachment; filename=' . basename($media->filename));
			header('Content-Transfer-Encoding: binary');
			header('Expires: 0');
			header('Cache-Control: must-revalidate');
			header('Pragma: public');
			header('Content-Length: ' . filesize($filePath));
			readfile($filePath);
			exit;
		} else {
			show_error('File does not exist.');
		}
	}
	function activity($id = FALSE, $condition = FALSE, $activityID = FALSE)
	{
		$this->load->helper('notification');
		$project = $this->projectModel->find_by_id($id);

		switch ($condition) {
			case 'add':
				if ($_POST) {
					$this->handleActivityAdd($project, $_POST);
				}
				break;
			case 'update':
				// Update logic would go here
				break;
			case 'delete':
				// Delete logic would go here
				break;
		}
	}

	private function handleActivityAdd($project, $postData)
	{
		unset($postData['send'], $postData['files']);
		$postData['subject'] = htmlspecialchars($postData['subject']);
		$postData['message'] = strip_tags($postData['message'], '<br><br/><p></p><a></a><b></b><i></i><u></u><span></span>');
		$postData['project_id'] = $project->id;
		$postData['user_id'] = $this->user->id;
		$postData['type'] = "comment";
		$postData['datetime'] = time();

		$activity = $this->projectHasActivityModel->create($postData);
		if (!$activity) {
			$this->session->set_flashdata('message', 'error:' . $this->lang->line('messages_save_error'));
		} else {
			$this->session->set_flashdata('message', 'success:' . $this->lang->line('messages_save_success'));
			$this->sendActivityNotifications($project, $postData);
		}
	}

	private function sendActivityNotifications($project, $postData)
	{
		$subject = "[" . $project->name . "] " . $postData['subject'];
		$message = $postData['message'] . '<br><strong>' . $project->name . '</strong>';

		foreach ($project->project_has_workers as $worker) {
			send_notification($worker->user->email, $subject, $message);
		}

		// Notify client if applicable
		if (isset($project->company->client->email)) {
			$access = explode(',', $project->company->client->access);
			if (in_array('12', $access)) {
				send_notification($project->company->client->email, $subject, $message);
			}
		}
	}
	//All refernce of project 
	function AllReference()
	{
		$references = $this->projectModel->find('all');
		$output = array_map(fn($reference) => $reference->project_num, $references);

		header('Content-Type: application/json');
		echo json_encode($output);
		exit();
	}

	function getSpeakerValue($speaker)
	{
		if (strpos($speaker, "inter") === 0 && strpos($speaker, "user") === false) {
			$speakerId = str_replace('inter', '', $speaker);
			$output = $this->intervenantModel->find($speakerId)->value ?? 0;
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
			$etat = $this->input->post('create_tickets') ? 1 : 0;
			$data = [
				'project_id' => $proj_id,
				'name' => $this->input->post('name'),
				'description' => $this->input->post('description'),
				'create_tickets' => $etat,
				'created_by' => $this->user->id,
				'created_at' => date("Y-m-d H:i:s"),
			];

			$new_id = $this->settingModel->addData($data, $this->projectHasSubProjectModel->table_name());
			$proj_pere = $this->projectModel->find(['id' => $proj_id]);

			$this->updateSubProjectCode($new_id, $proj_pere->project_num);

			if ($etat == 1) {
				$this->createDefaultTickets($proj_pere, $proj_id, $new_id);
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

	private function updateSubProjectCode($subProjectId, $projectNum)
	{
		$ins = ['code' => $projectNum . '-' . $subProjectId];
		$this->settingModel->updatDataById($ins, $subProjectId, $this->projectHasSubProjectModel->table_name());
	}

	private function createDefaultTickets($proj_pere, $proj_id, $new_id)
	{
		$tickets_par_defaut = $this->categoryTicketModel->all(['categorie_type_id' => $proj_pere->type_projet]);
		$ins = [];
		$ticket_reference = $this->settingModel->find(['id_vcompanies' => $_SESSION['current_company']]);
		$core_ticket_ref = $ticket_reference->ticket_reference;

		foreach ($tickets_par_defaut as $key => $item) {
			$ins[] = [
				'subject' => $item->name,
				'text' => $item->description,
				'project_id' => $proj_id,
				'sub_project_id' => $new_id,
				'reference' => $core_ticket_ref + $key
			];
		}

		if (!empty($ins)) {
			$this->settingModel->addBatchData($ins, $this->ticketModel->table_name());
			$ticket_reference->update_attributes(['ticket_reference' => $core_ticket_ref + count($ins)]);
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
			$ins = [
				'name' => $this->input->post('name'),
				'description' => $this->input->post('description'),
				'updated_by' => $this->user->id,
				'updated_at' => date("Y-m-d H:i:s"),
			];
			$this->settingModel->updatDataById($ins, $id, $this->projectHasSubProjectModel->table_name());

			redirect('projects/view/' . $proj_id);
		} else {
			$this->theme_view = 'modal';
			$this->view_data['title'] = $this->lang->line('application_edit');
			$this->view_data['form_action'] = 'projects/editSousProjet/' . $proj_id . '/' . $id;
			$this->view_data['data'] = $this->settingModel->getDataById($id, $this->projectHasSubProjectModel->table_name());
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
		$project = $this->projectHasSubProjectModel->find($id);
		if ($project) {
			$project->delete();
		}

		redirect('projects/view/' . $proj_id);
	}

	function get_sub_projects()
	{
		if ($_POST) {
			$proj_id = $this->input->post("project_id");
			$proj = $this->projectModel->find(['conditions' => ['id = ?', $proj_id]]);
			$this->view_data['sub_projects'] = $proj->project_has_sub_projects;
			$this->theme_view = 'ajax_no_script';
			$this->content_view = 'projects/_list_sub_projects';
		}
	}
	// Trouver les contacts d'un client sélectionné
	public function get_contacts_clients()
	{
		if ($_POST) {
			$company_id = $this->input->post("company_id");
			$contacts = $this->clientModel->find('all', ['conditions' => ['company_id = ?', $company_id]]);

			$this->view_data['contacts_client'] = $contacts;
			$this->theme_view = 'ajax_no_script';
			$this->content_view = 'clients/_list_contacts_client';
		}
	}

	// Trouver les natures d'une catégorie sélectionné
	public function get_nature_projet()
	{
		if ($_POST) {
			$nat_projet = $this->input->post("type_projet");
			$nature = $this->natureModel->getNatureByCat($nat_projet);

			$nature_ids = array_column(json_decode(json_encode($nature), true), 'id_nature');
			$this->view_data['natures_projetcs'] = $this->referentiels->getNature(implode(',', $nature_ids));

			$this->theme_view = 'ajax_no_script';
			$this->content_view = 'projects/_list_nature_projects';
		}
	}

	//get project function

	public function getProjects()
	{
		$data = [];
		$this->load->model('Projects_model');

		$idadmin = $this->user->salaries_id;
		$projects = ($idadmin === NULL) ? $this->projectModel->getRows2($_POST) : $this->projectModel->getRows($_POST);

		$i = $_POST['start'];
		foreach ($projects as $project) {
			$i++;
			$data[] = [
				$project->project_id,
				$project->project_num,
				$project->project,
				$project->client,
				$project->start,
				$project->end,
				$project->nature,
				$project->state,
			];
		}

		$output = [
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->projectModel->countAll($_POST),
			"recordsFiltered" => ($idadmin === NULL) ? $this->projectModel->countFiltered2($_POST) : $this->projectModel->countFiltered($_POST),
			"data" => $data,
		];

		$this->theme_view = 'blank';
		echo json_encode($output);
	}
	//$draw = $this->Projects_model->countAll($_POST);
//$records=$this->Projects_model->countAll($_POST);
//filtred=$this->Projects_model->countFiltered($_POST);
}



//-------------------------------------------------
