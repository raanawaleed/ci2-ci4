<?php

namespace App\Models;

use CodeIgniter\Model;

class ProjectHasWorkerModel extends Model
{
    protected $table = 'project_has_workers';  // Define the table name
    protected $primaryKey = 'id';  // Define the primary key

    protected $allowedFields = [
        'project_id',
        'intervenant_id',
        'user_id',
        'value'
    ]; // Define allowed fields for mass assignment

    // Note: Relationships like $belongs_to are removed as CodeIgniter 4 does not use this syntax. 
    // You can handle relationships using joins in your queries.

    // Get the count of done tasks for a specific project and user
    public function getDoneTasks(int $projectID, int $userID): int
    {
        return $this->db->table('project_has_tasks')
            ->where(['status' => 'done', 'project_id' => $projectID, 'user_id' => $userID])
            ->countAllResults();
    }

    // Get the count of tasks in progress for a specific project and user
    public function getTasksInProgress(int $projectID, int $userID): int
    {
        return $this->db->table('project_has_tasks')
            ->where(['status !=' => 'done', 'project_id' => $projectID, 'user_id' => $userID])
            ->countAllResults();
    }

    // Get all tasks for a specific project and user
    public function getAllTasksInProject(int $projectID, int $userID): array
    {
        return $this->db->table('project_has_tasks')
            ->where(['project_id' => $projectID, 'user_id' => $userID])
            ->get()
            ->getResultArray();
    }

    // Get the total time spent on tasks for a specific project and user
    public function getAllTasksTime(int $projectID, int $userID): string
    {
        $taskTime = $this->db->table('project_has_timesheets')
            ->selectSum('time', 'summary')
            ->where(['user_id' => $userID, 'project_id' => $projectID])
            ->get()
            ->getRow();

        $tracking = $taskTime->summary ?? 0;
        $tracking_hours = floor($tracking / 3600);
        $tracking_minutes = floor(($tracking % 3600) / 60);

        return sprintf('%d %s %d %s', $tracking_hours, lang('application.hours'), $tracking_minutes, lang('application.minutes'));
    }
}
