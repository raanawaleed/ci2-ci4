<?php
namespace App\Controllers;
use App\Controllers\BaseController;
class ErrorController extends BaseController
{
	protected $session;
	protected $user;
	public function __construct()
	{
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
		// Get the incoming request URI
		$requestURI = service('request')->getUri();
		// Define a dynamic message
		$message = "Oops! The page at '" . $requestURI . "' could not be found.";
		if (is_cli()) {
			return view('errors/cli/error_404', ['message' => $message]);
		}
		return view('errors/html/error_404', ['message' => $message]);
	}
}