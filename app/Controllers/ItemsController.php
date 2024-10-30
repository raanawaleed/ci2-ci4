<?php

namespace App\Controllers;

use App\Models\ItemFamilyModel;
use App\Models\ItemsModel;
use App\Models\RefTypeOccurencesModel;

class ItemsController extends BaseController
{
	protected ItemFamilyModel $itemFamily;
	protected ItemsModel $item;
	protected RefTypeOccurencesModel $referentiels, $db;
	protected $view_data = [];

	public function __construct()
	{
		$this->itemFamily = new ItemFamilyModel();
		$this->item = new ItemsModel();
		$this->referentiels = new RefTypeOccurencesModel();
		$this->loadDatabase();
		$this->checkAccess();


		$this->view_data['submenu'] = [
			lang('application_all_items') => 'items'
		];
	}
	private function loadDatabase()
	{
		// Assuming database is already set in the configuration
		$this->db = \Config\Database::connect();
	}

	private function checkAccess()
	{
		if (session()->get('client')) {
			return redirect()->to('cprojects');
		} elseif (session()->get('user')) {
			$access = $this->hasAccessToItems();

			if (!$access) {
				return redirect()->to('login');
			}
		} else {
			return redirect()->to('login');
		}
	}

	private function hasAccessToItems(): bool
	{
		foreach ($this->view_data['menu'] as $value) {
			if ($value->link === "items") {
				return true;
			}
		}

		foreach ($this->view_data['submenuRight'] as $value) {
			if ($value->link === "items") {
				return true;
			}
		}

		return false;
	}

	public function index(): string
	{
		$items = $this->item->getAllItems();
		foreach ($items as $item) {
			$item->type = $this->itemFamily->find($item->id_family)->libelle;
		}

		$this->view_data['items'] = $items;
		return view('invoices/items', $this->view_data);
	}

	public function convert(array $data, int $index = 0): array
	{
		$output = array_filter($data, fn($item) => $item->parent === $index);

		return array_map(function ($item) use ($data) {
			return [
				'id' => $item->id,
				'libelle' => $item->libelle,
				'children' => $this->convert($data, $item->id)
			];
		}, $output);
	}

	public function copy(int $id = null): string
	{
		if ($id === null) {
			return redirect()->back()->with('error', 'Invalid item ID.');
		}

		$source = $this->item->find($id);
		if (!$source) {
			return redirect()->back()->with('error', 'Item not found.');
		}

		$copiedData = $source->toArray();
		unset($copiedData['id']);
		$newItem = $this->item->insert($copiedData);

		if (!$newItem) {
			return redirect()->back()->with('error', 'Failed to copy item.');
		}

		$this->view_data['items'] = $this->item->orderBy('id', 'desc')->first();
		$this->view_data['type'] = trim($this->itemFamily->find($this->view_data['items']->id_family)->libelle);

		$families = $this->itemFamily->where('inactive', 0)->findAll();
		$this->view_data['families'] = $this->convert($families);

		$this->view_data['item_units'] = $this->db->table('item_units')->get()->getResult();
		$this->view_data['tva'] = $this->referentiels->where(['id_type' => 9, 'visible' => 1])->findAll();

		$this->view_data['title'] = lang('application_edit_item');
		$this->view_data['form_action'] = 'items/update_items';

		return view('invoices/_items', $this->view_data);
	}

	public function famille(): string
	{
		$items = $this->db->table('items_has_family')->where('inactive', 0)->get()->getResult();
		$this->view_data['items'] = $this->convert($items);

		return view('invoices/items_family', $this->view_data);
	}

	public function create_family_items(): string
	{
		if ($this->request->getMethod() === 'post') {
			$data = $this->request->getPost();
			$data['inactive'] = 0;
			$data['id'] = $this->itemFamily->getLastId() + 1;

			if ($data['parent'] !== 0) {
				$parent = $this->itemFamily->find($data['parent']);
				$data['parent'] = $parent->id ?? 0;
			}

			$success = $this->db->table('items_has_family')->insert($data);

			if ($success) {
				session()->setFlashdata('message', 'success:' . lang('messages_create_item_family_success'));
			} else {
				session()->setFlashdata('message', 'error:' . lang('messages_create_item_family_error'));
			}

			return redirect()->to('items/famille');
		}

		$this->view_data['title'] = lang('application_create_family_item');

		$families = $this->db->table('items_has_family')->where('inactive', 0)->get()->getResult();
		$this->view_data['families'] = $this->convert($families);

		return view('invoices/_items_family', $this->view_data);
	}

	public function getchildByName(string $name)
	{
		$parent = $this->itemFamily->where('libelle', $name)->first();
		$children = $this->itemFamily->getChildItemById($parent->id);

		$output = array_reduce($children, fn($carry, $child) => $carry . '_' . $child->libelle, '');

		return $this->response->setJSON($output ?: 'NoChild');
	}

	public function delete_family_items(int $id)
	{
		$this->db->table('items_has_family_parent')->update(['inactive' => 1], ['id' => $id]);
		$this->db->table('items_has_family')->update(['inactive' => 1], ['parent' => $id]);

		$family = $this->db->table('items_has_family_parent')->select('libelle')->where('id', $id)->get()->getFirstRow();
		if ($family) {
			$this->db->table('items')->update(['inactive' => 1], ['type' => $family->libelle]);
		}

		session()->setFlashdata('message', 'success:' . lang('messages_delete_item_success'));
		return redirect()->to('items');
	}

	public function update_family_items(int $id): string
	{
		if ($this->request->getMethod() === 'post') {
			$data = $this->request->getPost();
			$data = array_map('htmlspecialchars', $data);

			$this->db->table('items_has_family')->update($data, ['id' => $data['id']]);

			session()->setFlashdata('message', 'success:' . lang('messages_save_item_success'));
			return redirect()->to('items/famille');
		}

		$this->view_data['items'] = $this->db->table('items_has_family')->where('id', $id)->get()->getFirstRow();
		$this->view_data['type'] = $this->itemFamily->find($this->view_data['items']->parent)->libelle ?? '';

		$families = $this->db->table('items_has_family')->where('inactive', 0)->get()->getResult();
		$this->view_data['families'] = $this->convert($families);

		$this->view_data['title'] = lang('application_edit_item');
		$this->view_data['form_action'] = 'items/update_family_items/' . $id;

		return view('invoices/_items_family', $this->view_data);
	}

	public function create_items(): string
	{
		if ($this->request->getMethod() === 'post') {
			$data = $this->request->getPost();
			$data['inactive'] = 0;
			$data = array_map('htmlspecialchars', $data);

			$data['tva'] = explode('%', $data['tva'])[0];

			$lastItem = $this->item->orderBy('id', 'desc')->first();
			$data['id'] = $lastItem ? $lastItem->id + 1 : 1;

			$this->db->table('items')->insert($data);

			session()->setFlashdata('message', 'success:' . lang('messages_create_item_success'));
			return redirect()->to('items');
		}

		$this->view_data['title'] = lang('application_create_item');

		$families = $this->db->table('items_has_family')->where('inactive', 0)->get()->getResult();
		$this->view_data['families'] = $this->convert($families);

		$this->view_data['item_units'] = $this->db->table('item_units')->get()->getResult();
		$this->view_data['tva'] = $this->referentiels->where(['id_type' => 9, 'visible' => 1])->findAll();

		return view('invoices/_items', $this->view_data);
	}

	public function update_items(int $id = null)
	{
		if ($id === null || $this->request->getMethod() !== 'post') {
			return redirect()->back()->with('error', 'Invalid request.');
		}

		$data = $this->request->getPost();
		$data = array_map('htmlspecialchars', $data);
		unset($data['file']);

		$this->db->table('items')->update($data, ['id' => $id]);

		session()->setFlashdata('message', 'success:' . lang('messages_save_item_success'));
		return redirect()->to('items');
	}
}
