<?php

namespace App\Models;

use CodeIgniter\Model;

class ProjectHasInvoiceModel extends Model
{
  protected $table = 'project_has_invoices';  // Define the table name
  protected $primaryKey = 'id';  // Define the primary key

  // Define allowed fields for mass assignment
  protected $allowedFields = [
    'project_id',
    'invoice_id'
  ];

  // Note: Relationships like $belongs_to are removed as CodeIgniter 4 does not use this syntax. 
  // You can handle relationships using joins in your queries.

  // Get invoices by project ID
  public function getInvoicesByProjectId(int $projectId)
  {
    return $this->select('project_has_invoices.*, projects.name as project_name, companies.name as company_name')
      ->join('projects', 'projects.id = project_has_invoices.project_id')
      ->join('companies', 'companies.id = project_has_invoices.company_id')
      ->where('project_has_invoices.project_id', $projectId)
      ->findAll();
  }

  // Get all invoices with project and company details
  public function getAllInvoicesWithDetails()
  {
    return $this->select('project_has_invoices.*, projects.name as project_name, companies.name as company_name')
      ->join('projects', 'projects.id = project_has_invoices.project_id')
      ->join('companies', 'companies.id = project_has_invoices.company_id')
      ->findAll();
  }
}
