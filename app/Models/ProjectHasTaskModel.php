<?php

namespace App\Models;

use CodeIgniter\Model;

class ProjectHasTaskModel extends Model
{
    protected $table = 'project_has_tasks';  // Define the table name
    protected $primaryKey = 'id';  // Define the primary key

    protected $allowedFields = [
        'project_id',
        'name',
        'sector',
        'amount',
        'user_id',
        'intervenant_id',
        'status',
        'public',
        'datetime',
        'due_date',
        'description',
        'value',
        'priority',
        'time',
        'client_id',
        'created_by_client',
        'tracking',
        'time_spent',
        'milestone_id',
        'invoice_id',
        'milestone_order',
        'task_order',
        'progress',
        'created_at',
        'start_date'
    ]; // Define allowed fields for mass assignment

    // Note: Relationships like $belongs_to are removed as CodeIgniter 4 does not use this syntax. 
    // You can handle relationships using joins in your queries.

    // Get total spend grouped by project
    public function getTotalSpend()
    {
        return $this->db->table($this->table)
            ->select('project_id, SUM(time_spent) as total')
            ->groupBy('project_id')
            ->get()
            ->getResult();
    }

    /**
     * Get sum of payments grouped by Month for statistics.
     * 
     * @param int $projectID
     * @param string $from
     * @param string $to
     * @return array
     */
    public static function getDueTaskStats($projectID, $from, $to)
    {
        $builder = (new self())->builder();
        return $builder->select('due_date, count(id) AS tasksDue')
            ->where('due_date >=', $from)
            ->where('due_date <=', $to)
            ->where('project_id', $projectID)
            ->groupBy('SUBSTR(due_date, -5)')
            ->get()
            ->getResultArray();
    }

    public static function getStartTaskStats($projectID, $from, $to)
    {
        $builder = (new self())->builder();
        return $builder->select('start_date, count(id) AS tasksDue')
            ->where('start_date >=', $from)
            ->where('start_date <=', $to)
            ->where('project_id', $projectID)
            ->groupBy('SUBSTR(start_date, -5)')
            ->get()
            ->getResultArray();
    }

    public static function getClientTasks($projectID, $clientID)
    {
        $builder = (new self())->builder();
        return $builder->where('public', 1)
            ->where('project_id', $projectID)
            ->orderBy('task_order')
            ->get()
            ->getResultArray();
    }

    public function getUserTasks($userId)
    {
        return $this->where('status !=', 'done')
            ->where('user_id', $userId)
            ->orderBy('project_id', 'ASC')
            ->findAll();
    }

    public function countTasksByProjectId(int $projectId): int
    {
        return $this->where('project_id', $projectId)->countAllResults();
    }
}
