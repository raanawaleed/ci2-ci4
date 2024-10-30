<?php

namespace App\Models;

use CodeIgniter\Model;

class ProjectHasActivityModel extends Model
{
  protected $table = 'project_has_activities';  // Define the table name
  protected $primaryKey = 'id';  // Define the primary key

  // Define allowed fields for mass assignment
  protected $allowedFields = [
    'project_id',
    'user_id',
    'client_id',
    'datetime',
    'subject',
    'message',
    'type'
  ];

  // Get all activities
  public function getAll(): array
  {
    return $this->findAll();
  }

  // Get an activity by its ID
  public function getById(int $id): object|null
  {
    return $this->find($id);
  }

  // Get activities related to a specific project
  public function getByProjectId(int $project_id): array
  {
    return $this->where('project_id', $project_id)
      ->findAll();
  }

  // Get activities related to a specific user
  public function getByUserId(int $user_id): array
  {
    return $this->where('user_id', $user_id)
      ->findAll();
  }

  // Get activities related to a specific client
  public function getByClientId(int $client_id): array
  {
    return $this->where('client_id', $client_id)
      ->findAll();
  }
}
