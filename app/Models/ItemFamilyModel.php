<?php

namespace App\Models;

use CodeIgniter\Model;

class ItemFamilyModel extends Model
{
	// protected $table = 'items_has_family_parent';
	protected $table = 'items_has_family';
	protected $primaryKey = 'id';  // Define primary key
	protected $allowedFields = ['libelle', 'parent', 'inactive', 'id_vcompanies'];

	// Get item by name
	public function getItemByName(string $name): object|null
	{
		return $this->where('libelle', $name)->first();
	}

	// Get item by id
	public function getItemById(int $id): object|null
	{
		return $this->find($id);  // Use `find()` to fetch by primary key
	}

	// Get child items by parent id
	public function getChildItemById(int $id): array
	{
		return $this->where('id_parent', $id)->findAll();
	}

	// Get all item types
	public function getAllItemTypes(): array
	{
		return $this->findAll();
	}

	// Get last ID
	public function getLastId(): int|null
	{
		$result = $this->selectMax('id')->first();  // Fetch the row with the max id
		return $result['id'] ?? null;  // Return the last ID or null if none exists
	}
}