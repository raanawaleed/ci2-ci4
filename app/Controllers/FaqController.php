<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class FaqController extends BaseController
{
	function __construct()
	{

		if (!session()->get('user')) {
		} elseif (session()->get('user')) {
		} else {
			redirect('login');
		}
	}

	function index()
	{
		return view('blueline/support/faq');
	}
}
