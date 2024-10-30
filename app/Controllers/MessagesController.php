<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PrivateMessageModel; // Ensure the model name is correct

class MessagesController extends BaseController
{
	protected $messagesModel, $view_data;

	public function __construct()
	{
		// Initialize the messages model
		$this->messagesModel = new PrivateMessageModel();

		$access = FALSE;

		if (session()->get('client')) {
			return redirect('cprojects');
		} elseif (session()->get('user')) {
			foreach ($this->view_data['menu'] as $key => $value) {
				if ($value->link == "messages") {
					$access = TRUE;
				}
			}
			if (!$access) {
				return redirect('login');
			}
		} else {
			return redirect('login');
		}

		// Submenu for messages
		$this->view_data['submenu'] = array(
			lang('application_new_messages') => 'messages',
			lang('application_read_messages') => 'messages/filter/read',
		);
	}

	public function index()
	{
		return view('messages/all', $this->view_data);
	}

	public function messagelist($con = FALSE, $deleted = FALSE)
	{
		$max_value = 60;

		// Build conditions based on the deleted status
		$conditions = [
			'recipient' => session()->get('user_id'),
			'deleted' => ($deleted === "deleted") ? 1 : 0
		];

		// Query messages using the model
		$this->view_data["message"] = $this->messagesModel->where($conditions)->findAll($max_value, ($con !== FALSE ? $con : 0));

		if ($con == "filter" && $deleted == "read") {
			$this->view_data['filter'] = "read";
		}

		return view('messages/list', $this->view_data);
	}

	public function filter($condition = FALSE)
	{
		// Build filter conditions based on the selected condition
		if ($condition === 'read') {
			$this->view_data["message"] = $this->messagesModel->where([
				'recipient' => session()->get('user_id'),
				'status' => 'read',
				'deleted' => 0
			])->findAll();
		} else {
			$this->view_data["message"] = $this->messagesModel->where([
				'recipient' => session()->get('user_id'),
				'deleted' => 0,
			])->findAll();
		}

		// Set filter
		$this->view_data['filter'] = $condition;

		return view('messages/list', $this->view_data);
	}

	public function write($id = FALSE)
	{
		return view('messages/write', $this->view_data);
	}

	public function reply($id = FALSE, $dialogue_id = FALSE)
	{
		return view('messages/reply', $this->view_data);
	}
}
