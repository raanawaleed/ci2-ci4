<?php
namespace App\Controllers;

use App\Controllers\BaseController;

use App\Models\InvoiceHasItemModel;
use App\Models\FactureHasItemModel;

use CodeIgniter\Database\Exceptions\DatabaseException;

class ApiController extends BaseController
{
	protected $invoiceItemsModel;
	protected $factureItemsModel;
	function __construct()
	{
		$this->invoiceItemsModel = new InvoiceHasItemModel();
		$this->factureItemsModel = new FactureHasItemModel();

		// Assuming $this->user is defined somewhere in your BaseController or manually.
		if (!session()->get('user')) {
			redirect('login');
		}
	}

	public function sortEstimates()
	{
		$items = $this->request->getVar('items');
		$itemsArray = explode(",", $items);

		$data = [];
		foreach ($itemsArray as $counter => $item) {
			$data[] = [
				'id' => $item,
				'position' => $counter + 1,
			];
		}

		if (!empty($data)) {
			$this->invoiceItemsModel->updateBatch($data, 'id');
		}

		return $this->response->setJSON(['status' => 'success']);
	}

	public function sortFactures()
	{
		$items = $this->request->getVar('items');
		$itemsArray = explode(",", $items);

		$data = [];
		foreach ($itemsArray as $counter => $item) {
			$data[] = [
				'id' => $item,
				'position' => $counter + 1,
			];
		}

		if (!empty($data)) {
			$this->factureItemsModel->updateBatch($data, 'id');
		}

		return $this->response->setJSON(['status' => 'success']);
	}
}