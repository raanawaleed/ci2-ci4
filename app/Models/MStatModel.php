<?php

namespace App\Models;

use CodeIgniter\Model;

class MStatModel extends Model
{


    // Get project name by ID
    public function projectName($id)
    {
        return $this->db->table('projects') // Use table() method
            ->select('name')
            ->where('id', $id)
            ->get()
            ->getRow(); // Retrieve a single row
    }

    // Get all tasks associated with a project
    public function tasksProject($id)
    {
        return $this->db->table('project_has_tasks') // Use table() method
            ->select('*')
            ->where('project_id', $id)
            ->get()
            ->getResult(); // Retrieve multiple rows
    }

    // Get all tasks from time_date_tasks table
    public function allTasks()
    {
        return $this->db->table('time_date_tasks') // Use table() method
            ->select('*')
            ->get()
            ->getResult(); // Retrieve multiple rows
    }

    // Count total hours spent on tasks associated with a project
    public function countHours($id)
    {
        return $this->db->table('project_has_tasks') // Use table() method
            ->selectSum('time_spent', 'total') // Use selectSum for summation
            ->where('project_id', $id)
            ->get()
            ->getRow(); // Retrieve a single row
    }
}
