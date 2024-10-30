<?php

namespace App\Models;

use CodeIgniter\Model;

class ProjectHasFileModel extends Model
{
    protected $table = 'project_has_files';  // Define the table name
    protected $primaryKey = 'id';  // Define the primary key

    // Define allowed fields for mass assignment
    protected $allowedFields = [
        'project_id',
        'user_id',
        'client_id',
        'type',
        'name',
        'filename',
        'description',
        'savename',
        'phase',
        'date',
        'download_counter'
    ];

    // Get all project files
    public function getAll(): array
    {
        return $this->findAll();
    }

    // Get a project file by its ID
    public function getById(int $id): object|null
    {
        return $this->find($id);
    }

    // Get files related to a specific project
    public function getByProjectId(int $project_id): array
    {
        return $this->where('project_id', $project_id)
            ->findAll();
    }

    // Get files related to a specific user
    public function getByUserId(int $user_id): array
    {
        return $this->where('user_id', $user_id)
            ->findAll();
    }

    // Get files related to a specific client
    public function getByClientId(int $client_id): array
    {
        return $this->where('client_id', $client_id)
            ->findAll();
    }

    // Increment the download counter for a file
    public function incrementDownloadCounter(int $id): bool
    {
        return $this->set('download_counter', 'download_counter + 1', false)
            ->where('id', $id)
            ->update();
    }
}
