<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

class ErrorController extends Controller
{
	protected $session;
	protected $user;

	public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
	{
		parent::initController($request, $response, $logger);

		$this->session = \Config\Services::session();
		$this->user = $this->session->get('user');

		if (!$this->user) {
			setcookie("lasturl", uri_string(), time() + 3600, '/');
			return redirect()->to('login');
		}
	}

	public function index()
	{
		return $this->error_404();
	}

	public function error_404()
	{
		$currentURL = current_url();
		$requestURI = service('request')->getUri();
		$message = "Oops! The page at '" . $requestURI . "' could not be found.";

		if (is_cli()) {
			return view('errors/cli/error_404', ['message' => $message]);
		}
		return view('errors/html/error_404', ['message' => $message]);
	}
}
