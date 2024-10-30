<?php

namespace App\Models;

use CodeIgniter\Model;

class ItemsModel extends Model
{

    protected $table = 'items';  // Define the table
    protected $primaryKey = 'id';  // Define the primary key
    protected $allowedFields = ['name', 'description', 'id_family', 'value', 'tva', 'unit', 'inactive'];  // Fields allowed for mass assignment


    // Fetch all active items
    public function getAllItems(): array
    {
        return $this->where('inactive', 0)
            ->orderBy('id', 'DESC')
            ->findAll();
    }

    // Fetch item by name
    public function getByName(string $name): object|null
    {
        return $this->where('name', $name)->first();
    }

    // Fetch item by ID
    public function getById(int $id): object|null
    {
        return $this->find($id);
    }

    // Fetch TVA by name
    public function getTVAByName(string $name): object|null
    {
        return $this->where('name', $name)->first();
    }

    // Fetch all units (assuming it returns a single row)
    public function getAllUnit(): object|null
    {
        return $this->first();
    }

    // Get the last inserted item ID
    public function getLastId(): int|null
    {
        $result = $this->selectMax('id')->first();
        return $result['id'] ?? null;
    }

    // Fetch items for export
    public function getForExport(): array
    {
        return $this->select('items.name, items.description, items_has_family.libelle, items.value, (items.value * items.tva / 100) + items.value as ttc, items.tva, items.unit')
            ->join('items_has_family', 'items.id_family = items_has_family.id', 'left')
            ->findAll();
    }
}