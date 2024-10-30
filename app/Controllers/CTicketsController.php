<?php

namespace App\Controllers;

use App\Models\RefTypeOccurencesModel;
use App\Models\RefTypeModel;
use App\Models\TicketModel;



use App\Controllers\BaseController;
use App\Models\ArticleHasAttachmentModel;
use App\Models\ClientModel;
use App\Models\ProjectModel;
use App\Models\SettingModel;
use App\Models\TicketHasArticleModel;
use App\Models\TicketHasAttachmentModel;
use App\Models\UserModel;
use CodeIgniter\Language\Language;

class CTicketsController extends BaseController
{
	protected array $view_data = [];
	protected  $referentiels, $refType, $ticketModel, $userModel, $projectModel, $settingModel, $clientModel, $theme_view;
	protected TicketHasArticleModel $ticketHasArticleModel;
	protected ArticleHasAttachmentModel $articleHasAttachmentModel;


	public function __construct()
	{
		$this->refType = new RefTypeModel();
		$this->referentiels = new RefTypeOccurencesModel();
		$this->ticketModel = new TicketModel();
		$this->userModel = new UserModel();
		$this->projectModel = new ProjectModel();
		$this->settingModel = new SettingModel();
		$this->clientModel = new ClientModel();
		$this->ticketHasArticleModel = new TicketHasArticleModel();
		$this->articleHasAttachmentModel = new ArticleHasAttachmentModel();
		// Check if user is logged in
		if (!session()->get('client') && !session()->get('user')) {
			return redirect()->to('login');
		}

		// Initialize submenu and category project
		$this->initializeSubmenu();
		$this->initializeCategoryProject();
	}

	protected function initializeSubmenu(): void
	{
		$idType = $this->refType->getRefTypeByName("ticket")->id;
		$submenus = $this->referentiels->getReferentielsByIdType($idType);
		$this->view_data['submenu'] = [];

		foreach ($submenus as $submenu) {
			$this->view_data['submenu'][$submenu->name] = 'ctickets/filter/' . $submenu->id;
		}
	}

	protected function initializeCategoryProject(): void
	{
		$idType = $this->refType->getRefTypeByName("catégorie projet")->id;
		$categorieProject = $this->referentiels->getReferentielsByIdType($idType);

		// Initialize with a default value
		$this->view_data['categorie_projet'] = ['Tous' => 'ctickets/categorieprojet/0'];

		foreach ($categorieProject as $cat) {
			$this->view_data['categorie_projet'][$cat->name] = 'ctickets/categorieprojet/' . $cat->id;
		}
	}


	//test
	public function getTicketsToDatatables()
	{
		// Ensure UTF-8 internal encoding
		mb_internal_encoding('UTF-8');

		// Get request data using CodeIgniter's request object
		$request = service('request');
		$postData = $request->getPost();

		$data = [];
		$i = $postData['start'] ?? 0;  // Fallback to 0 if 'start' is not set

		// Get tickets from the model
		$tickets = $this->ticketModel->getRows($postData);

		// Loop through the tickets and prepare data for DataTables
		foreach ($tickets as $ticket) {
			$i++;
			$data[] = [
				$ticket->ticket_id,
				$ticket->ticket_id,
				$ticket->subject,
				$ticket->project,
				$ticket->start,
				$ticket->end,
				$ticket->collaborater_firstname . ' ' . $ticket->collaborater_lastname,
				$ticket->type,
			];
		}

		// Prepare output array for DataTables
		$output = [
			'draw' => (int)($postData['draw'] ?? 1),  // Fallback to 1 if 'draw' is not set
			'recordsTotal' => $this->ticketModel->countAll($postData),
			'recordsFiltered' => $this->ticketModel->countFiltered($postData),
			'data' => $data,
		];

		// Return JSON response
		return $this->response->setJSON($output);
	}

	// Afficher tous les tickets fermés
	public function closed()
	{
		// Fetch closed tickets using CI4's query builder
		$tickets = $this->ticketModel->where('closed', 1)->findAll();

		// Loop through tickets and resolve relationships manually
		foreach ($tickets as &$ticket) {
			// Find project by project_id
			if (!empty($ticket['project_id']) && $ticket['project_id'] != 0) {
				$ticket['project'] = $this->projectModel->find($ticket['project_id']);
			}

			// Find sub-project by sub_project_id
			if (!empty($ticket['sub_project_id']) && $ticket['sub_project_id'] != 0) {
				$ticket['sub_project'] = $this->projectModel->getSubProjectById($ticket['sub_project_id']);  // Assuming this is a method in your ProjectModel
			}

			// Find collaborator by collaborater_id
			if (!empty($ticket['collaborater_id']) && $ticket['collaborater_id'] != 0) {
				$ticket['collaborater'] = $this->userModel->find($ticket['collaborater_id']);
			}

			// Get status name from referentiels
			$ticket['status'] = $this->referentiels->getReferentielById($ticket['status'])->name ?? 'Unknown';
		}

		// Fetch all projects
		$projects = $this->projectModel->findAll();

		// Pass data to the view
		$data = [
			'user' => $this->userModel->getUser(),  // Assuming you have a method to get the logged-in user
			'tickets' => $tickets,
			'projects' => $projects,
		];

		// Load the view with data
		return view('tickets/all', $data);
	}


	//Afficher tous les tickets (sans restriction sur le user)
	public function index()
	{
		// Fetch the current user (assuming session is used for user management)
		$userId = session()->get('user_id'); // Adjust if you're using another method to get user ID
		$user = $this->userModel->find($userId);

		// Check if the user is an admin and load the appropriate view
		if ($user && $user['admin'] === "1") {
			return view('tickets/all_tickets');
		} else {
			return view('tickets/all_tickets_not_admin');
		}
	}

	//Filtrer sur les catégories projets
	public function categorieprojet($id)
	{
		// Fetch the current user (assuming session is used for user management)
		$userId = session()->get('user_id');
		$user = $this->userModel->find($userId);

		// If ID is 0, redirect to all tickets
		if ($id == 0) {
			return redirect()->to(base_url('ctickets'));
		}

		// Get tickets by project type
		$tickets = $this->ticketModel->getTicketByTypeProjet($id);

		// Process tickets to fetch related project, subproject, collaborator, and status
		foreach ($tickets as $ticket) {
			// Fetch project if project_id is not null or zero
			if (!empty($ticket->project_id)) {
				$ticket->project_id = $this->projectModel->find($ticket->project_id);
			}

			// Fetch sub-project if sub_project_id is not null or zero
			if (!empty($ticket->sub_project_id)) {
				$ticket->sub_project_id = $this->projectModel->find($ticket->sub_project_id);
			}

			// Fetch collaborator if collaborater_id is not zero
			if ($ticket->collaborater_id != 0) {
				$ticket->collaborater_id = $this->userModel->find($ticket->collaborater_id);
			}

			// Fetch status name using referentiels model
			$ticket->status = $this->referentiels->getReferentielsById($ticket->status)->name;
		}

		// Pass data to the view
		$data = [
			'user' => $user,
			'tickets' => $tickets
		];

		// Load the view
		return view('tickets/all', $data);
	}

	//Filtrer sur les types tickets
	public function filter($condition = false, $id = false)
	{
		// Fetch the current user (assuming session is used for user management)
		$userId = session()->get('user_id');
		$user = $this->userModel->find($userId);

		$idType = $this->refType->getRefTypeByName("ticket")->id;
		$idClosed = $this->referentiels->getReferentiels($idType, "Fermé")->id;
		$occDeleted = $this->referentiels->getReferentiels($idType, "Supprimé");

		$options = [];

		// Handle ticket filtering based on condition and ID
		if ($id === false) {
			// Filter based on status
			switch ($condition) {
				case 0: // All tickets
					return redirect()->to(base_url('ctickets/tous'));
				case 1: // Closed tickets
					return redirect()->to(base_url('ctickets/closed'));
				case 2: // Deleted tickets
					$options['conditions'] = ['deleted' => 1];
					break;
				default: // Specific status filtering
					$options['conditions'] = ['status' => $condition, 'deleted' => 0];
			}
		} else {
			// If filtering by collaborator and status (deleted or not)
			if ($condition == $occDeleted->id) {
				$options['conditions'] = ['collaborater_id' => $id, 'deleted' => 1];
			} else {
				$options['conditions'] = ['collaborater_id' => $id, 'closed' => 0, 'deleted' => 0];
			}
		}

		// Fetch tickets based on the options
		$tickets = $this->ticketModel->where($options['conditions'])->findAll();

		// Process tickets and fetch related data
		foreach ($tickets as &$ticket) {
			// Fetch project if project_id is not null or zero
			if (!empty($ticket->project_id)) {
				$ticket->project_id = $this->projectModel->find($ticket->project_id);
			}

			// Fetch sub-project if sub_project_id is not null or zero
			if (!empty($ticket->sub_project_id)) {
				$ticket->sub_project_id = $this->projectModel->find($ticket->sub_project_id);
			}

			// Fetch collaborator if collaborater_id is not zero
			if ($ticket->collaborater_id != 0) {
				$ticket->collaborater_id = $this->userModel->find($ticket->collaborater_id);
			}

			// Fetch status name
			$ticket->status = $this->referentiels->getReferentielsById($ticket->status)->name;
		}

		// Prepare data for the view
		$data = [
			'user' => $user,
			'tickets' => $tickets,
		];

		// Pass deleted occurrence data if applicable
		if ($condition == $occDeleted->id) {
			$data['occDeleted'] = $occDeleted;
		}

		// Load the view with data
		return view('tickets/all', $data);
	}


	//Filtrer sur les types tickets
	public function filter_deleted($id = false)
	{
		// Fetch the current user
		$userId = session()->get('user_id');
		$user = $this->userModel->find($userId);

		// Build query options based on ID presence
		$conditions = ['deleted' => 1];

		if ($id !== false) {
			$idClosed = $this->referentiels->getReferentielsByName("Fermé")->id;
			$conditions = [
				'collaborater_id' => $id,
				'status !=' => $idClosed,
				'deleted' => 1
			];
		}

		// Retrieve tickets based on conditions
		$tickets = $this->ticketModel->where($conditions)->findAll();

		// Process each ticket
		foreach ($tickets as &$ticket) {
			// Fetch associated project if applicable
			if (!empty($ticket->project_id)) {
				$ticket->project_id = $this->projectModel->find($ticket->project_id);
			}

			// Fetch associated sub-project if applicable
			if (!empty($ticket->sub_project_id)) {
				$ticket->sub_project_id = $this->projectModel->find($ticket->sub_project_id);
			}

			// Fetch collaborator if applicable
			if (!empty($ticket->collaborater_id)) {
				$ticket->collaborater_id = $this->userModel->find($ticket->collaborater_id);
			}

			// Fetch ticket status name
			$ticket->status = $this->referentiels->getReferentielsById($ticket->status)->name;
		}

		// Prepare view data
		$data = [
			'user' => $user,
			'tickets' => $tickets,
		];

		// Load the view
		return view('tickets/all', $data);
	}
	//Créer un nouveau ticket
	public function create()
	{
		if ($this->request->getMethod() === 'post') {
			// Sanitize input
			$postData = $this->request->getPost();
			unset($postData['userfile'], $postData['file-name'], $postData['send'], $postData['_wysihtml5_mode'], $postData['files']);

			// Fetch current company settings and user details
			$currentCompanyId = session()->get('current_company');
			$settings = $this->settingModel->where('id_vcompanies', $currentCompanyId)->first();
			$client = $this->clientModel->find($currentCompanyId);
			$defaultOwner = $settings->ticket_default_owner;

			$postData['from'] = session()->get('user_id');
			$postData['user_id'] = $defaultOwner;
			$postData['created'] = time();
			$postData['subject'] = htmlspecialchars($postData['subject'], ENT_QUOTES, 'UTF-8');
			$postData['new_created'] = 1;

			// Handle project and sub-project IDs
			if ($postData['project_id'] === '#') {
				$postData['project_id'] = null;
			}
			if ($postData['sub_project_id'] === '#') {
				$postData['sub_project_id'] = null;
			}

			// Check for project ID validity
			if (empty($postData['project_id'])) {
				session()->setFlashdata('message', 'error: Veuillez vérifier le projet sélectionné');
				return redirect()->to('/ctickets');
			}

			// Handle file upload
			$uploadData = false;
			if ($this->request->getFiles()) {
				$uploadData = $this->uploadFiles($this->request->getFiles());
			}

			// Begin database transaction
			$db = \Config\Database::connect();
			$db->transBegin();

			// Save tickets for each collaborator
			foreach ($postData['collaborater_id'] as $recUser) {
				$postData['collaborater_id'] = $recUser;
				$ticketReference = $settings->ticket_reference;
				$postData['reference'] = $ticketReference;

				// Create ticket
				$ticket = $this->ticketModel->insert($postData);

				// Update the ticket reference number
				$this->settingModel->update($settings->id, ['ticket_reference' => $postData['reference'] + 1]);

				// Save attachments if any
				if ($uploadData) {
					foreach ($uploadData as $file) {
						$this->ticketModel->insert([
							'ticket_id' => $ticket,
							'filename' => $file['orig_name'],
							'savename' => $file['file_name']
						]);
					}
				}
			}

			// Check transaction status
			if ($db->transStatus() === false) {
				$db->transRollback();
				session()->setFlashdata('message', 'error: ' . lang('messages_create_ticket_error'));
				return redirect()->to('/ctickets');
			} else {
				$db->transCommit();
				session()->setFlashdata('message', 'success: ' . lang('messages_create_ticket_success'));
				return redirect()->to('/ctickets');
			}
		} else {
			// Prepare data for the form view
			$data['title'] = lang('application_create_ticket');
			$data['collaboraters'] = $this->userModel->where('status', 'active')->findAll();
			$data['projects'] = $this->projectModel->where('progress !=', 100)->findAll();

			// Load status, priority, and other form options
			$idType = $this->refType->getRefTypeByName('ticket')->id;
			$data['status'] = $this->referentiels->getReferentielsByIdType($idType);
			$data['etats'] = $this->referentiels->getReferentielsByIdType($this->config->item('type_id_etat_tache'));
			$data['priorite'] = $this->referentiels->getReferentielsByIdType($this->config->item('type_id_priorite_tache'));

			return view('tickets/_ticket', $data);
		}
	}

	// Upload file method (optimized)
	private function uploadFiles(array $files)
	{
		$uploadedFiles = [];

		foreach ($files as $file) {
			if ($file->isValid() && !$file->hasMoved()) {
				$newName = $file->getRandomName();
				$file->move('./files/media/', $newName);
				$uploadedFiles[] = [
					'orig_name' => $file->getClientName(),
					'file_name' => $file->getName()
				];
			}
		}

		return $uploadedFiles;
	}

	/**
	 * upmoad multiple des pièces jointes
	 * @return array
	 */
	private function upload_pj()
	{
		$uploadedData = [];
		$files = $this->request->getFiles();

		// Loop through each uploaded file
		foreach ($files['userfile'] as $file) {
			if ($file->isValid() && !$file->hasMoved()) {
				// Generate a random file name for security
				$newFileName = $file->getRandomName();
				// Move file to the specified directory
				if ($file->move(FCPATH . 'files/media/', $newFileName)) {
					// Store upload data
					$uploadedData[] = [
						'upload_data' => [
							'orig_name' => $file->getClientName(),
							'file_name' => $file->getName()
						]
					];
				} else {
					// Set an error message if the upload fails
					session()->setFlashdata('message', 'error: Failed to upload file');
					return redirect()->to('ctickets');
				}
			} else {
				// Set an error message if the file is invalid
				session()->setFlashdata('message', 'error: ' . $file->getErrorString());
				return redirect()->to('ctickets');
			}
		}

		return $uploadedData;
	}
	//Editer un ticket
	public function editTicket($id)
	{
		if ($this->request->getMethod() === 'post') {
			$collaboratorId = $this->request->getPost('collaborater_id')[0] ?? null;

			// Remove unwanted POST fields
			$postData = $this->request->getPost();
			unset($postData['userfile'], $postData['file-name'], $postData['send'], $postData['_wysihtml5_mode'], $postData['files']);

			$postData['from'] = session()->get('user_id');
			$postData['created'] = time();
			$postData['subject'] = htmlspecialchars($postData['subject'], ENT_QUOTES, 'UTF-8');
			$postData['collaborater_id'] = $collaboratorId ?: null;

			if ($collaboratorId) {
				$postData['new_created'] = 1;
			}

			// Find the ticket and update its attributes
			$ticket = $this->ticketModel->find($id);
			if (!$ticket) {
				session()->setFlashdata('message', 'error:' . lang('messages_update_ticket_error'));
				return redirect()->to('ctickets');
			}

			$ticket->update_attributes($postData);

			// Handle file upload
			$emailAttachment = false;
			$files = $this->request->getFile('userfile');

			if ($files && $files->isValid() && !$files->hasMoved()) {
				// Set file configurations
				$newFileName = $files->getRandomName();

				// Move the uploaded file to the destination folder
				if ($files->move(FCPATH . 'files/media/', $newFileName)) {
					// Update attachment information
					$attachmentData = [
						'ticket_id' => $ticket->id,
						'filename' => $files->getClientName(),
						'savename' => $files->getName()
					];
					$attachment = new TicketHasAttachmentModel();
					$attachment->find(['ticket_id' => $id]);
					if ($attachment) {
						$attachment->update_attributes($attachmentData);
					}
					$emailAttachment = $files->getName();
				} else {
					session()->setFlashdata('message', 'error: Failed to upload file');
					return redirect()->to('ctickets');
				}
			}

			// Flash success message
			session()->setFlashdata('message', 'success:' . lang('messages_update_ticket_success'));
			return redirect()->to('ctickets/view/' . $id);
		} else {
			// Prepare data for the view
			$this->view_data['title'] = lang('application_edit_ticket');
			$this->view_data['ticket'] = $this->ticketModel->find($id);
			$this->view_data['collaboraters'] = $this->userModel->findAll();
			$this->view_data['projects'] = $this->projectModel->findAll();

			// Fetch referentials
			$refType = $this->refType->getRefTypeByName('ticket')->id;
			$this->view_data['status'] = $this->referentiels->getReferentielsByIdType($refType);

			$idType = config('App')->type_id_type_tache;
			$this->view_data['types'] = $this->referentiels->getReferentielsByIdType($idType);

			$idPriorite = config('App')->type_id_priorite_tache;
			$refEtat = config('App')->type_id_etat_tache;
			$this->view_data['etats'] = $this->referentiels->getReferentielsByIdType($refEtat);
			$this->view_data['priorite'] = $this->referentiels->getReferentielsByIdType($idPriorite);

			// Render view
			return view('tickets/_ticket', $this->view_data);
		}
	}


	//Copier un ticket
	public function copyTicket(int $id)
	{
		// Fetch the original ticket
		$ticketSource = $this->ticketModel->find($id);
		if (!$ticketSource) {
			// Handle ticket not found
			session()->set_flashdata('message', 'error: Ticket not found.');
			return redirect()->to('ctickets');
		}

		// Prepare data for the new ticket
		$tabTicketSource = $ticketSource->toArray(); // Convert model attributes to an array
		unset($tabTicketSource['id']); // Remove the ID for duplication

		// Fetch company reference
		$ticketReference = $this->settingModel->where('id_vcompanies', $_SESSION['current_company'])->first();
		if ($ticketReference) {
			$tabTicketSource['reference'] = $ticketReference->ticket_reference;
			$ticketReference->update(['ticket_reference' => $tabTicketSource['reference'] + 1]);
		}

		// Create the duplicated ticket
		$ticketCopy = $this->ticketModel->create($tabTicketSource);

		// Handle attachments
		$attachments = new  TicketHasAttachmentModel();
		$attachments->where('ticket_id', $id)->findAll();
		foreach ($attachments as $attachment) {
			$attachmentData = $attachment->toArray();
			unset($attachmentData['id']); // Remove the ID

			// Set new ticket ID for the copied attachment
			$attachmentData['ticket_id'] = $ticketCopy->id;

			// Create new attachment record
			$attachments->create($attachmentData);
		}

		// Set success message and redirect
		session()->set_flashdata('message', 'success:' . lang('messages_duplicate_ticket_success'));
		return redirect()->to('ctickets/view/' . $ticketCopy->id);
	}

	// Afficher le détail d'un ticket
	public function view(int $id = null, int $taskId = null)
	{
		// Get current user
		$currentUserId = $this->session->get('user_id');
		$currentUser = $this->userModel->find($currentUserId);

		// Load the ticket by ID
		$ticket = $this->ticketModel->find($id);
		if (!$ticket) {
			return redirect()->to('ctickets')->with('message', 'error: Ticket not found.');
		}

		// Get the period associated with the ticket
		$this->view_data['periode'] = $this->ticketModel->getPeriodPerTicket($id)->periode ?? null;

		// Load ticket creator's user information
		$this->view_data['user'] = $this->userModel->find($ticket->user_id);

		// Prepare ticket data
		$ticket->from = $this->getUserFullName($ticket->from);
		foreach ($ticket->ticket_has_articles as $article) {
			$article->from = $this->getUserFullName($article->from);
		}

		// Map status and type names
		$ticket->status = $this->referentiels->getReferentielsById($ticket->status)->name ?? null;
		$this->view_data['ticket_type'] = $this->referentiels->getReferentielsById($ticket->type_id)->name ?? null;

		// Load additional related entities
		$ticket->collaborater_id = $this->getUserById($ticket->collaborater_id);
		$ticket->project_id = $this->getProjectById($ticket->project_id);
		$ticket->sub_project_id = $this->getSubProjectById($ticket->sub_project_id);

		// Update ticket if the current user is the collaborator
		if ($ticket->collaborater_id && $ticket->collaborater_id->id === $currentUserId) {
			$this->ticketModel->update($ticket->id, ['new_created' => 0]);
		}

		// Set view data
		$this->view_data['ticket'] = $ticket;
		$this->view_data['current_user'] = $currentUser;

		// Render the view
		return view('tickets/viewdetail', $this->view_data);
	}

	// Helper function to get full name
	private function getUserFullName($userId): ?string
	{
		if ($userId) {
			$user = $this->userModel->find($userId);
			return $user ? "{$user->firstname} {$user->lastname}" : null;
		}
		return null;
	}

	// Helper function to get user by ID
	private function getUserById(?int $userId): ?object
	{
		return $userId ? $this->userModel->find($userId) : null;
	}

	// Helper function to get project by ID
	private function getProjectById(?int $projectId): ?object
	{
		return $projectId ? $this->projectModel->find($projectId) : null;
	}

	// Helper function to get sub-project by ID
	private function getSubProjectById(?int $subProjectId): ?object
	{
		return $subProjectId ? $this->projectModel->find($subProjectId) : null;
	}

	//Ajouter une note à un ticket
	public function article(int $id = null, string $condition = null, int $article_id = null)
	{
		// Set up the submenu for navigation
		$this->view_data['submenu'] = [
			lang('application_back') => 'ctickets',
			lang('application_overview') => 'ctickets/view/' . $id,
		];

		switch ($condition) {
			case 'add':
				$this->handleAddArticle($id);
				break;

			default:
				return redirect()->to('ctickets');
		}
	}

	private function handleAddArticle(int $id): void
	{
		view('tickets/_note');

		if ($this->request->getMethod() === 'post') {
			$this->uploadArticle($id);
		} else {
			$this->showArticleForm($id);
		}
	}

	private function uploadArticle(int $id)
	{
		$ticket = $this->ticketModel->find($id);
		if (!$ticket) {
			return redirect()->to('ctickets')->with('message', 'error: Ticket not found.');
		}

		// Configure file upload
		$config = [
			'upload_path' => './files/media/',
			'encrypt_name' => true,
			'allowed_types' => '*',
		];
		$this->load->library('upload', $config);
		$this->load->helper('notification');

		// Prepare article data
		$articleData = $this->request->getPost();
		$this->prepareArticleData($articleData, $id);

		// Create the article
		$article = $this->ticketHasArticleModel->create($articleData);
		if (!$article) {
			return redirect()->to('ctickets/view/' . $id)
				->with('message', 'error:' . lang('messages_save_article_error'));
		}

		// Handle file upload
		if (!$this->upload->do_upload('userfile')) {
			$error = $this->upload->display_errors('', ' ');
			return redirect()->to('ctickets/view/' . $id)
				->with('message', 'error:' . $error);
		}

		// Save attachment details
		$this->saveAttachment($article->id, $this->upload->data());

		return redirect()->to('ctickets/view/' . $id)
			->with('message', 'success:' . $this->lang->line('messages_save_article_success'));
	}

	private function prepareArticleData(array &$articleData, int $ticketId): void
	{
		unset(
			$articleData['userfile'],
			$articleData['file-name'],
			$articleData['send'],
			$articleData['_wysihtml5_mode'],
			$articleData['files']
		);

		$articleData['internal'] = "0"; // Default internal status
		unset($articleData['notify']); // Remove notify if set
		$articleData['subject'] = htmlspecialchars($articleData['subject']);
		$articleData['datetime'] = time();
		$articleData['ticket_id'] = $ticketId;
		$articleData['from'] = session()->get('user_id'); // Assuming this is set in the controller
		$articleData['reply_to'] = $ticketId; // Should match your logic
	}

	private function saveAttachment(int $articleId, array $uploadData): void
	{
		$attributes = [
			'article_id' => $articleId,
			'filename' => $uploadData['orig_name'],
			'savename' => $uploadData['file_name'],
		];
		$this->articleHasAttachmentModel->create($attributes);
	}

	private function showArticleForm(int $id): void
	{
		$this->theme_view = 'modal';
		$this->view_data['ticket'] = $this->ticketModel->find($id);
		$this->view_data['title'] = $this->lang->line('application_add_note');
		$this->view_data['form_action'] = 'ctickets/article/' . $id . '/add';
		view('tickets/_note');
	}

	//Ajouter un attachement à un ticket
	function attachment($id = FALSE)
	{
		helper('download');
		helper('file');
		$attachment = new TicketHasAttachment();
		$attachment->find_by_savename($id);
		$file = './files/media/' . $attachment->savename;
		$mime = get_mime_by_extension($file);
		if (file_exists($file)) {
			header('Content-Description: File Transfer');
			header('Content-Type: ' . $mime);
			header('Content-Disposition: attachment; filename=' . basename($attachment->filename));
			header('Content-Transfer-Encoding: binary');
			header('Expires: 0');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Pragma: public');
			header('Content-Length: ' . filesize($file));
			readfile($file);
			ob_clean();
			flush();
			exit;
		}
	}

	function articleattachment($id = FALSE)
	{
		helper('download');
		helper('file');
		$attachment = new ArticleHasAttachmentModel();
		$attachment->find_by_savename($id);
		$file = './files/media/' . $attachment->savename;
		$mime = get_mime_by_extension($file);
		if (file_exists($file)) {
			header('Content-Description: File Transfer');
			header('Content-Type: ' . $mime);
			header('Content-Disposition: attachment; filename=' . basename($attachment->filename));
			header('Content-Transfer-Encoding: binary');
			header('Expires: 0');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Pragma: public');
			header('Content-Length: ' . filesize($file));
			readfile($file);
			ob_clean();
			flush();
			exit;
		}
	}

	// Assigner un ticket à un collaborateur
	function assign($id = FALSE)
	{
		helper('notification');
		if ($_POST) {
			$config['upload_path'] = './files/media/';
			$config['encrypt_name'] = TRUE;
			$config['allowed_types'] = '*';
			library('upload', $config);
			unset($_POST['userfile']);
			unset($_POST['file-name']);
			unset($_POST['_wysihtml5_mode']);
			unset($_POST['files']);
			unset($_POST['send']);
			unset($_POST['_wysihtml5_mode']);
			$id = $_POST['id'];
			unset($_POST['id']);
			unset($_POST['notify']);
			$assign = $this->ticketModel->find('all', $id);
			$attr = array();
			$attr['collaborater_id'] = $_POST['to'];
			$attr['new_created'] = "1";
			$assign->update_attributes($attr);
			$_POST['subject'] = htmlspecialchars($_POST['subject']);
			$_POST['datetime'] = time();
			$_POST['from'] = $this->user->id;
			$_POST['reply_to'] = $assign->user_id;
			$_POST['ticket_id'] = $id;
			$_POST['internal'] = 0;
			unset($_POST['user_id']);
			$article = $this->ticketHasArticleModel->create($_POST);
			$lastArticle = $this->ticketHasArticleModel->Last();
			if (! $this->upload->do_upload()) {
				$error = $this->upload->display_errors('', ' ');
				session()->set_flashdata('message', 'error:' . $error);
			} else {
				$data = array('upload_data' => $this->upload->data());

				$attributes = array('article_id' => $lastArticle->id, 'filename' => $data['upload_data']['orig_name'], 'savename' => $data['upload_data']['file_name']);
				$attachment = new TicketHasAttachmentModel();
				$attachment->create($attributes);
			}
			if (!$assign) {
				session()->set_flashdata('message', 'error:' . lang('messages_save_ticket_error'));
			} else {
				session()->set_flashdata('message', 'success:' . lang('messages_assign_ticket_success'));
			}
			redirect('ctickets/');
		} else {
			$this->theme_view = 'modal';
			$this->view_data['ticket'] =  $this->ticketModel->find($id);
			$this->view_data['collaboraters'] = $this->userModel->find('all', array('conditions' => array('status=? And 
			id !=?', 'active', $this->user->id)));
			$this->view_data['projects'] = $this->projectModel->find('all');
			view('tickets/_ticket');
			$this->view_data['title'] = $this->lang->line('application_assign_to_agents');
			$this->view_data['form_action'] = 'ctickets/assign';
			view('tickets/_assign');
		}
	}

	// Afficher le statut d'un ticket
	function status($id = FALSE)
	{
		helper('notification');
		if ($_POST) {
			unset($_POST['send']);
			unset($_POST['_wysihtml5_mode']);
			unset($_POST['files']);
			$id = $_POST['id'];
			unset($_POST['id']);
			$ticket = $this->ticketModel->find_by_id($id);
			$attr = array('status' => $_POST['status']);
			$ticket->update_attributes($attr);
			if (!$ticket) {
				$this->session->set_flashdata('message', 'error:' . $this->lang->line('messages_status_error'));
			} else {
				$this->session->set_flashdata('message', 'success:' . $this->lang->line('messages_status_success'));
			}
			redirect('ctickets/view/' . $id);
		} else {
			$refType = $this->refType->getRefTypeByName("ticket")->id;
			$this->view_data['status'] = $this->referentiels->getReferentielsByIdType($refType);
			$this->view_data['ticket'] = $this->ticketModel->find_by_id($id);
			$this->theme_view = 'modal';
			$this->view_data['title'] = lang('application_status');
			$this->view_data['form_action'] = 'ctickets/status';
			view('tickets/_status');
		}
	}



	// Fermer un ticket, il n'est plus visible dans la liste des tickets
	function close($id = FALSE)
	{
		helper('notification');
		if ($_POST) {
			unset($_POST['send']);
			unset($_POST['_wysihtml5_mode']);
			unset($_POST['files']);
			$id = $_POST['ticket_id'];
			unset($_POST['ticket_id']);
			$ticket = $this->ticketModel->find_by_id($id);
			$attr['closed'] = 1;
			$ticket->update_attributes($attr);
			if (isset($ticket->client->email)) {
				$email = $ticket->client->email;
			} else {
				$emailex = explode(' - ', $ticket->from);
				$email = $emailex[1];
			}
			if (isset($_POST['notify'])) {
				send_ticket_notification($email, '[Ticket#' . $ticket->reference . '] - ' . $ticket->subject, $_POST['message'], $ticket->id);
			}
			send_ticket_notification($ticket->user->email, '[Ticket#' . $ticket->reference . '] - ' . $ticket->subject, $_POST['message'], $ticket->id);
			$_POST['internal'] = "0";
			unset($_POST['notify']);
			$_POST['subject'] = htmlspecialchars($_POST['subject']);
			$_POST['datetime'] = time();
			$_POST['from'] = session()->get('user_id');
			$_POST['reply_to'] = session()->get('user_id');
			$_POST['ticket_id'] = $id;
			$_POST['to'] = $email;
			unset($_POST['client_id']);
			$article = $this->ticketHasArticleModel->create($_POST);
			if (!$ticket) {
				session()->set_flashdata('message', 'error:' . lang('messages_save_ticket_error'));
			} else {
				session()->set_flashdata('message', 'success:' . lang('messages_ticket_close_success'));
			}
			redirect('ctickets');
		} else {
			$this->view_data['ticket'] = $this->ticketModel->find($id);
			$this->theme_view = 'modal';
			$this->view_data['title'] = lang('application_close');
			$this->view_data['form_action'] = 'ctickets/close';
			view('tickets/_close');
		}
	}

	// Charger - sauvegarder le type d'une tâche
	function type($id = FALSE)
	{
		helper('notification');
		if ($_POST) {
			unset($_POST['send']);
			unset($_POST['_wysihtml5_mode']);
			unset($_POST['files']);
			$id = $_POST['id'];
			unset($_POST['id']);
			$ticket = $this->ticketModel->find_by_id($id);
			$attr = array('type_id' => $_POST['type_id']);
			$ticket->update_attributes($attr);

			if (!$ticket) {
				session()->set_flashdata('message', 'error:' . lang('messages_assign_type_error'));
			} else {
				session()->set_flashdata('message', 'success:' . lang('messages_assign_type_success'));
			}
			redirect('ctickets/view/' . $id);
		} else {

			$refType = $this->config->item("type_id_type_tache");
			$this->view_data['types'] = $this->referentiels->getReferentielsByIdType($refType);
			$this->view_data['ticket'] = $this->ticketModel->find_by_id($id);
			$this->theme_view = 'modal';
			$this->view_data['title'] = $this->lang->line('application_type');
			$this->view_data['form_action'] = 'ctickets/type';
			view('tickets/_type');
		}
	}
	// Charger - sauvegarder l'etat d'une tâche
	function etat($id = FALSE)
	{
		helper('notification');
		if ($_POST) {
			unset($_POST['send']);
			unset($_POST['_wysihtml5_mode']);
			unset($_POST['files']);
			$id = $_POST['id'];
			unset($_POST['id']);
			$ticket = $this->ticketModel->find_by_id($id);
			$attr = array('etat_id' => $_POST['etat_id']);
			$ticket->update_attributes($attr);

			if (!$ticket) {
				session()->set_flashdata('message', 'error:' . lang('messages_assign_etat_error'));
			} else {
				session()->set_flashdata('message', 'success:' . lang('messages_assign_etat_success'));
			}
			redirect('ctickets/view/' . $id);
		} else {

			$refEtat = $this->config->item("type_id_etat_tache");
			$this->view_data['etats'] = $this->referentiels->getReferentielsByIdType($refEtat);
			$this->view_data['ticket'] = $this->ticketModel->find_by_id($id);
			$this->theme_view = 'modal';
			$this->view_data['title'] = lang('application_etat');
			$this->view_data['form_action'] = 'ctickets/etat';
			view('tickets/_etat');
		}
	}
	/* Charger - sauvegarder la surface d'une tâche
	function surface($id = FALSE)
	{	
		$this->load->helper('notification');
		if($_POST){
			unset($_POST['send']);
			unset($_POST['_wysihtml5_mode']);
			unset($_POST['files']);
			$id = $_POST['id'];
			unset($_POST['id']);
			$ticket = Ticket::find_by_id($id); 
			$attr = $_POST['surface'];
			$ticket->"UPDATE "($attr);

       		if(!$ticket){$this->session->set_flashdata('message', 'error:'.$this->lang->line('messages_assign_etat_error'));}
       		else{$this->session->set_flashdata('message', 'success:'.$this->lang->line('messages_assign_etat_success'));}
			redirect('ctickets/view/'.$id);
		}else
		{
			
			//$ref =$this->config->item("type_id_etat_tache");
			$this->view_data['surface'] = $attr ;
			//var_dump($ref);exit;
			$this->view_data['ticket'] = Ticket::find_by_id($id);
			$this->theme_view = 'modal';
			$this->view_data['title'] = "Quantité";
			$this->view_data['form_action'] = 'ctickets/surface';
			$this->content_view = 'tickets/_surface';
		}	
	}
	*/
	//bluk
	function bulk($action)
	{
		helper('notification');
		if ($_POST['list'] != '') {
			$list = explode(",", $_POST['list']);
			switch ($action) {
				case 'close':

					$attr['closed'] = 1;
					$email_message = lang('messages_bulk_ticket_closed');
					$success_message = lang('messages_bulk_ticket_closed_success');
					break;
				default:
					redirect('ctickets');
					break;
			}
			foreach ($list as $value) {
				$ticket = $this->ticketModel->find_by_id($value);
				$ticket->update_attributes($attr);
				if (!$ticket) {
					session()->set_flashdata('message', 'error:' . lang('messages_save_ticket_error'));
				} else {
					session()->set_flashdata('message', 'success:' . $success_message);
				}
			}
			redirect('ctickets');
		} else {
			redirect('ctickets');
		}
	}

	//Supprimer un ticket
	public function deleteTicket(int $id)
	{
		// Find the ticket by ID
		$ticket = $this->ticketModel->find($id);

		// Check if the ticket exists
		if (!$ticket) {
			// Set flashdata message for ticket not found
			session()->setFlashdata('message', 'error:Attention: Le ticket n\'a pas été trouvé');
			return redirect()->to('ctickets');
		}

		// Delete associated articles first
		$this->db->table('ticket_has_articles')->where('ticket_id', $id)->delete();

		// Delete the ticket
		$this->ticketModel->delete($id);

		// Set success message
		session()->setFlashdata('message', 'success:Le ticket a été supprimé');

		// Redirect to the tickets page
		return redirect()->to('ctickets');
	}
}
