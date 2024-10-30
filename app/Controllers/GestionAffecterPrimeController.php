<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Prime;
use App\Models\SalarieModel;

class GestionAffecterPrimeController extends BaseController
{
	//protected $primesModel;
	protected $salariesModel;
	protected $view_data = [];
	protected $theme_view, $db;

	public function __construct()
	{


		// Load models
		//$this->primesModel = new PrimesModel();
		$this->salariesModel = new SalarieModel();

		// Check user authentication
		if (session()->get('user')) {
			return redirect('login');
		}
	}

	public function index()
	{
		// Fetch data using models
		$this->view_data['salaries'] = $this->salariesModel->where('etat', 1)->orderBy('id', 'desc')->findAll();
		// $this->view_data['primes'] = $this->primesModel->where('id_companie', (int) $_SESSION['current_company'])->orderBy('id', 'desc')->findAll();
		//$this->view_data['azerty'] = $this->primesModel->orderBy('id', 'desc')->findAll();

		return view("blueline/rhpaie/gestionprimeaffecter");
	}

	public function delete(int $id)
	{
		// $this->primesModel->delete($id);
		return redirect()->back()->with('message', 'Prime deleted successfully');
	}

	public function update(int $id = null)
	{
		if ($this->request->getMethod() === 'post') {
			$data = $this->request->getPost();
			$data['id_companie'] = (int) $_SESSION['current_company'];
			$data['access'] = isset($data["access"]) ? implode(",", $data["access"]) : null;

			// Filter input to prevent XSS
			$data = array_map('htmlspecialchars', $data);

			// Update the record
			// $this->primesModel->update($id, $data);

			return redirect('gestionprime')->with('message', 'Prime updated successfully');
		} else {
			// Fetch item to edit
			// $this->view_data['item'] = $this->primesModel->find($id);
			$this->view_data['view'] = true;

			$this->theme_view = 'modal';
			$this->view_data['title'] = lang('application_editer_prime');
			$this->view_data['form_action'] = 'gestionaffecterprime/update/' . $id;
			view('blueline/rhpaie/updateprime');
		}
	}
}
