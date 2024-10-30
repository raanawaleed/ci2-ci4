<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class SupportController extends BaseController
{
	public function __construct()
	{
		if (!isset($this->client) && !isset($this->user)) {
			redirect()->to('login')->send();
			exit(); // Ensure script execution stops after redirection
		}
	}


	public function index(): void
	{
		$this->content_view = 'support/user_guide';
	}
}