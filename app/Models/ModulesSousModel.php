<?php

namespace App\Models;

use CodeIgniter\Model;

class ModulesSousModel extends Model
{

    protected $table = 'modules_sous';
    protected $primaryKey = 'id';  // Define the primary key
    protected $allowedFields = ['name', 'id_modules', 'link', 'type', 'icon', 'sort', 'actif'];  // Define allowed fields for mass assignment

    // Get all records
    public function getAll(): array
    {
        return $this->findAll();
    }

    // Get the last inserted ID
    public function getLastId(): int|null
    {
        return $this->selectMax('id')->first()['id'] ?? null;
    }

    // Get record by ID
    public function getById(int $id): object|null
    {
        return $this->find($id);
    }

    // Get records by name and active status
    public function getByName(string $name): array
    {
        return $this->where('name', $name)
            ->where('actif', 1)
            ->findAll();
    }

    // Get submenu items with join on modules table based on provided IDs
    public function getSubmenu(array $ids): array
    {
        return $this->select('modules.name as modulename, modules.id as module_id, ' . $this->table . '.*')
            ->join('modules', 'modules.id = ' . $this->table . '.id_modules')
            ->whereIn($this->table . '.id', $ids)
            ->findAll();
    }

    // Get the default screen for a user by their ID
    public function getUserScreen(int $id): object|null
    {
        return $this->where('id', $id)->first();
    }
}