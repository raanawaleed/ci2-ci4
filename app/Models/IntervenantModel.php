<?php

namespace App\Models;

use CodeIgniter\Model;

class IntervenantModel extends Model
{
	protected $table = 'intervenants'; // Adjust the table name as per your database
	protected $primaryKey = 'id';
	protected $allowedFields = [ 'name', 'surname', 'adress', 'email', 'value', 'visible', 'id_vcompanies', 'admin', 'userpic']; // Example fields, adjust based on your table structure

	/**
	 * Get all workers related to a project.
	 *
	 * @param int $projectId
	 * @return array
	 */
	public function getWorkersByProject(int $projectId): array
	{
		return $this->db->table('project_has_workers')
			->where('project_id', $projectId)
			->get()
			->getResultArray();
	}

	/**
	 * Get all tasks related to a project.
	 *
	 * @param int $projectId
	 * @return array
	 */
	public function getTasksByProject(int $projectId): array
	{
		return $this->db->table('project_has_tasks')
			->where('project_id', $projectId)
			->get()
			->getResultArray();
	}
}