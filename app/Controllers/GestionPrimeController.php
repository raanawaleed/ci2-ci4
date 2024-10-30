<?php

namespace App\Controllers;

use App\Models\PrimeModel;
use App\Models\SalarieModel;

class GestionPrimeController extends BaseController
{
	protected $primeModel;
	protected $salarieModel;

	public function __construct()
	{
		// Load models
		$this->primeModel = new PrimeModel();
		$this->salarieModel = new SalarieModel();

		// Check user authentication
		if (!session()->has('user_id')) {
			return redirect('login');
		}
	}

	public function index()
	{
		$data['primes'] = $this->primeModel->orderBy('id', 'desc')->findAll();
		return view("rhpaie/gestionprime", $data);
	}

	public function create()
	{
		if ($this->request->getMethod() === 'post') {
			$data = $this->request->getPost();

			// Sanitize input data
			$data['access'] = isset($data['access']) ? implode(",", $data['access']) : null;
			$data['id_companie'] = (int) session()->get('current_company');

			// Insert the data
			$this->primeModel->insert($data);
			return redirect('gestionprime');
		} else {
			$data['title'] = lang('application_add_prime');
			$data['form_action'] = 'gestionprime/create';
			return view('rhpaie/addprime', $data);
		}
	}

	public function delete($id = null)
	{
		if ($id) {
			$this->primeModel->delete($id);
		}
		return redirect('gestionprime');
	}

	public function update($id = null)
	{
		if ($this->request->getMethod() === 'post') {
			$data = $this->request->getPost();

			// Sanitize input data
			$data['access'] = isset($data['access']) ? implode(",", $data['access']) : null;
			$data['id_companie'] = (int) session()->get('current_company');

			// Update the data
			$this->primeModel->update($id, $data);
			return redirect('gestionprime');
		} else {
			$data['item'] = $this->primeModel->find($id);
			$data['view'] = "true";
			$data['title'] = lang('application_editer_prime');
			$data['form_action'] = 'gestionprime/update/' . $id;
			return view('rhpaie/updateprime', $data);
		}
	}

	public function affecterprime($id = null)
	{
		if ($this->request->getMethod() === 'post') {
			$data = $this->request->getPost();
			$annee = $data['annee'] ?? null;
			$months = array_filter(array_keys($data), fn($key) => in_array($key, [
				'Janvier',
				'Fevrier',
				'Mars',
				'Avril',
				'Mai',
				'Juin',
				'Juillet',
				'Aout',
				'Septembre',
				'Octobre',
				'Novembre',
				'Decembre'
			]));
			$users = array_filter(array_keys($data), 'is_numeric');

			// Insert each combination of user and month
			foreach ($months as $month) {
				foreach ($users as $userId) {
					$this->primeModel->insert([
						'id_prime' => $id,
						'id_companie' => (int) session()->get('current_company'),
						'annee' => (int) $annee,
						'moins' => $month,
						'id_salarie' => (int) $userId,
					]);
				}
			}
			return redirect('gestionprime');
		} else {
			$data['mindate'] = 2000;
			$data['datenow'] = date('Y');
			$data['salaries'] = $this->salarieModel->where('etat', 1)->orderBy('id', 'desc')->findAll();
			$data['item'] = $this->primeModel->find($id);
			$data['view'] = "true";
			$data['title'] = lang('application_affecter_prime');
			$data['form_action'] = 'gestionprime/affecterprime/' . $id;
			return view('rhpaie/affecterprime', $data);
		}
	}
}
