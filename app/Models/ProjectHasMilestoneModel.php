<?php

namespace App\Models;

use CodeIgniter\Model;

class ProjectHasMilestoneModel extends Model
{
  protected $table = 'project_has_milestones';  // Define the table name
  protected $primaryKey = 'id';  // Define the primary key

  protected $allowedFields = [
    'project_id',
    'name',
    'description',
    'due_date',
    'orderindex',
    'start_date'
  ]; // Define allowed fields for mass assignment

  // Note: Relationships like $belongs_to are removed as CodeIgniter 4 does not use this syntax. 
  // You can handle relationships using joins in your queries.

  // Get tasks associated with a milestone
  public function getTasksForMilestone(int $milestoneId)
  {
    return $this->db->table('project_has_tasks')
      ->where('milestone_id', $milestoneId)
      ->orderBy('milestone_order')
      ->get()
      ->getResult();
  }

  // Get project associated with the milestone
  public function getProjectForMilestone(int $milestoneId)
  {
    return $this->select('projects.*')
      ->join('projects', 'projects.id = project_has_milestones.project_id')
      ->where('project_has_milestones.id', $milestoneId)
      ->first();
  }
}
