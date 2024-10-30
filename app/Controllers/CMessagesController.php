<?php

namespace App\Controllers;

use App\Models\PrivateMessageModel;
use App\Controllers\BaseController;
use App\Models\ClientModel;
use App\Models\UserModel;
use CodeIgniter\I18n\Time;

class CMessagesController extends BaseController
{
	protected array $view_data = [];
	protected PrivateMessageModel $privateMessageModel;
	protected UserModel $userModel;
	protected ClientModel $clientModel;
	protected $db, $upload;

	public function __construct()
	{
		$this->privateMessageModel = new PrivateMessageModel();
		$this->userModel = new UserModel();
		$this->clientModel = new ClientModel();
		$this->session = session();
		$this->loadDatabase();
		$this->upload = \Config\Services::upload();

		// Check session and access
		if (!session()->get('client') || !$this->checkAccess('cmessages')) {
			return redirect()->to('login');
		}

		// Prepare submenu
		$this->view_data['submenu'] = [
			lang('application_new_messages') => 'cmessages',
			lang('application_read_messages') => 'cmessages/filter/read',
		];
	}
	private function loadDatabase()
	{
		// Assuming database is already set in the configuration
		$this->db = \Config\Database::connect();
	}

	private function checkAccess(string $link): bool
	{
		foreach ($this->view_data['menu'] as $menuItem) {
			if ($menuItem->link === $link) {
				return true;
			}
		}
		return false;
	}

	public function index()
	{
		return view('messages/client/all', $this->view_data);
	}

	public function messagelist(int $con = 0, bool $deleted = false)
	{
		$max_value = 60;
		$this->privateMessageModel->select('privatemessages.*, clients.userpic as userpic_c, users.userpic as userpic_u, 
            users.email as email_u, clients.email as email_c, 
            CONCAT(users.firstname, " ", users.lastname) as sender_u, 
            CONCAT(clients.firstname, " ", clients.lastname) as sender_c')
			->join('clients', 'CONCAT("c", clients.id) = privatemessages.sender', 'left')
			->join('users', 'CONCAT("u", users.id) = privatemessages.sender', 'left')
			->where('privatemessages.recipient', "c{$this->client->id}");

		if ($deleted) {
			$this->privateMessageModel->where('privatemessages.status', 'deleted')
				->orWhere('privatemessages.deleted', 1);
		} else {
			$this->privateMessageModel->where('privatemessages.status !=', 'deleted')
				->where('privatemessages.deleted', 0);
		}

		// Pagination
		$this->privateMessageModel->groupBy('privatemessages.id')
			->orderBy('privatemessages.time', 'DESC')
			->limit($max_value, $con);

		// Execute the query
		$messages = $this->privateMessageModel->findAll();
		$messageCount = $this->privateMessageModel->countAllResults(false);

		// Prepare view data
		$this->view_data['message'] = array_filter($messages);
		$this->view_data['message_rows'] = $messageCount;
		if ($deleted) {
			$this->view_data['deleted'] = "/{$deleted}";
		}
		$this->view_data['message_list_page_next'] = $con + $max_value;
		$this->view_data['message_list_page_prev'] = max(0, $con - $max_value); // Prevent negative pagination
		$this->view_data['filter'] = false;

		return view('messages/client/list', $this->view_data);
	}

	public function filter(string $condition = 'new', int $con = 0)
	{
		$max_value = 60;
		$clientId = "c" . session()->get('client')->id;

		$conditions = [
			'read' => 'privatemessages.recipient = :clientId AND (privatemessages.`status` = "Replied" OR privatemessages.`status` = "Read")',
			'sent' => 'privatemessages.sender = :clientId',
			'marked' => 'privatemessages.recipient = :clientId AND privatemessages.`status` = "Marked"',
			'deleted' => 'privatemessages.recipient = :clientId AND (privatemessages.status = "deleted" OR privatemessages.deleted = 1)',
			'new' => 'privatemessages.recipient = :clientId AND privatemessages.`status` = "New"',
		];

		$currentCondition = $conditions[strtolower($condition)] ?? $conditions['new'];

		// Prepare SQL statements
		$sql = "SELECT privatemessages.* FROM privatemessages WHERE {$currentCondition} ORDER BY privatemessages.`time` DESC LIMIT {$con}, {$max_value}";
		$query = $this->db->query($sql, ['clientId' => $clientId]);

		$this->view_data["message"] = array_filter($query->getResult());
		$this->view_data["message_rows"] = $query->getNumRows();
		$this->view_data["message_list_page_next"] = $con + $max_value;
		$this->view_data["message_list_page_prev"] = max(0, $con - $max_value);

		// Load view
		return view('messages/client/list', $this->view_data);
	}

	public function write($ajax = false)
	{
		if ($this->request->getMethod() === 'post') {
			$postData = $this->request->getPost();
			$message = htmlspecialchars($postData['message'] ?? '');
			$receiverArt = substr($postData['recipient'] ?? '', 0, 1);
			$receiverId = substr($postData['recipient'] ?? '', 1);
			$receiverEmail = '';

			if ($receiverArt === 'u') {
				$receiver = $this->userModel->find($receiverId);
				$receiverEmail = $receiver->email ?? '';
			}

			$postData['time'] = Time::now()->toDateTimeString();
			$postData['sender'] = 'c' . session()->get('client')->id;
			$postData['status'] = 'New';

			// File upload handling
			if ($this->upload->do_upload('userfile')) {
				$uploadData = $this->upload->getData();
				$postData['attachment'] = $uploadData['orig_name'];
				$postData['attachment_link'] = $uploadData['file_name'];
			} else {
				$error = $this->upload->display_errors('', ' ');
				if ($error !== "You did not select a file to upload.") {
					log_message('error', $error);
				}
			}

			// Handle previous message if exists
			if (isset($postData['previousmessage'])) {
				$status = $this->privateMessageModel->find($postData['previousmessage']);
				if ($receiverEmail === session()->get('client')->email) {
					$receiverArt = substr($status->recipient, 0, 1);
					$receiverId = substr($status->recipient, 1);
					$postData['recipient'] = $status->recipient;
					if ($receiverArt === 'u') {
						$receiver = $this->userModel->find($receiverId);
						$receiverEmail = $receiver->email ?? '';
					}
				}
				$status->status = 'Replied';
				$status->save();
				unset($postData['previousmessage']);
			}

			// Create the message
			if (!$this->privateMessageModel->create($postData)) {
				session()->setFlashdata('message', 'error:' . lang('messages_write_message_error'));
			} else {
				session()->setFlashdata('message', 'success:' . lang('messages_write_message_success'));
				send_notification(
					$receiverEmail,
					lang('application_notification_new_message_subject'),
					lang('application_notification_new_message') . '<br><hr>' . $postData['message'] . '<hr>'
				);
			}

			// Redirect or AJAX response
			if ($ajax !== 'reply') {
				return redirect()->to('cmessages');
			}
		} else {
			// Load modal view
			$this->view_data['users'] = $this->userModel->findAll();
			return view('messages/client/write', $this->view_data);
		}
	}

	public function delete(int $id)
	{
		if ($this->privateMessageModel->delete($id)) {
			session()->setFlashdata('message', 'success:' . lang('messages_message_deleted'));
		} else {
			session()->setFlashdata('message', 'error:' . lang('messages_message_delete_error'));
		}
		return redirect()->to('cmessages');
	}
}
