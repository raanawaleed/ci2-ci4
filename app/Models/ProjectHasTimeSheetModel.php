<?php

namespace App\Models;

use CodeIgniter\Model;

class ProjectHasTimeSheetModel extends Model
{
  protected $table = 'project_has_timesheets';  // Define the table name
  protected $primaryKey = 'id';  // Define the primary key

  protected $allowedFields = [
    'project_id',
    'user_id',
    'time',
    'task_id',
    'client_id',
    'start',
    'end',
    'invoice_id',
    'description'
  ]; // Define allowed fields for mass assignment

  // Note: Relationships like $belongs_to are removed as CodeIgniter 4 does not use this syntax. 
  // You can handle relationships using joins in your queries.

  // Example method to get timesheets by project ID
  public function getTimesheetsByProjectId(int $projectId): array
  {
    return $this->where('project_id', $projectId)->findAll();
  }

  // Example method to get total time spent on a task
  public function getTotalTimeForTask(int $taskId): float
  {
    return $this->where('task_id', $taskId)
      ->selectSum('time')
      ->first()['time'] ?? 0.0;
  }

  // Example method to get timesheets for a specific user
  public function getTimesheetsByUserId(int $userId): array
  {
    return $this->where('user_id', $userId)->findAll();
  }
}
