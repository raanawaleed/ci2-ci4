<?php

namespace App\Models;

use CodeIgniter\Model;

class ProjectHasSubProjectModel extends Model
{
	protected $table = 'project_has_sub_projects';  // Define the table name
	protected $primaryKey = 'id';  // Define the primary key

	protected $allowedFields = [
		'project_id',
		'code',
		'name',
		'description',
		'create_tickets',
		'created_by',
		'created_at',
		'updated_by',
		'updated_at'
	]; // Define allowed fields for mass assignment

	// Note: Relationships like $belongs_to are removed as CodeIgniter 4 does not use this syntax. 
	// You can handle relationships using joins in your queries.

	// Get project associated with the sub-project
	public function getProjectForSubProject(int $subProjectId)
	{
		return $this->select('projects.*')
			->join('projects', 'projects.id = project_has_sub_projects.project_id')
			->where('project_has_sub_projects.id', $subProjectId)
			->first();
	}
}
