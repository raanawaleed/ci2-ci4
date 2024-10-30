<?php

namespace App\Models;

use CodeIgniter\Model;

class EstimateModel extends Model
{
  protected $table = ' invoices';
  protected $primaryKey = 'id';

  protected $allowedFields = [
    'reference',
    'company_id',
    'subject',
    'notes',
    'status',
    'currency',
    'issue_date',
    'creation_date',
    'due_date',
    'sent_date',
    'paid_date',
    'terms',
    'discount',
    'subscription_id',
    'project_id',
    'project_ref',
    'tax',
    'estimate',
    'estimate_accepted_date',
    'estimate_sent',
    'sum',
    'sumht',
    'second_tax',
    'estimate_reference',
    'paid',
    'outstanding',
    'estimate_num',
    'id_facture',
    'timbre_fiscal',
    'project_name',
    'project_surface',
    'calcul_heure',
    'delivery',
    'chef_projet_client',
    'chef_projet',
    'unite'
  ]; // Update with your table's fields

  /**
   * Get elements for Excel export.
   *
   * @return array
   */
  public function getElementForExcel(): array
  {
    return $this->select('estimate_num, company_id, subject, issue_date, currency, sum')
      ->orderBy('id', 'desc')
      ->findAll();
  }

  /**
   * Get invoices with `sumht` greater than 0.
   *
   * @return array
   */
  public function getDevisDocument(): array
  {
    return $this->where('sumht >', 0)
      ->orderBy('id', 'desc')
      ->findAll();
  }
  /**
   * Get all invoices.
   *
   * @return array
   */
  public function getDocument(): array
  {
    return $this->findAll();
  }

  /**
   * Get invoices with `sumht` equal to 0.
   *
   * @return array
   */
  public function getAttDocument(): array
  {
    return $this->where('sumht', 0)
      ->orderBy('id', 'desc')
      ->findAll();
  }

  /**
   * Get invoices that match 'MMS' in `project_name` or `subject`.
   *
   * @return array
   */
  public function getMms(): array
  {
    return $this->like('project_name', 'MMS')
      ->orLike('subject', 'MMS', 'both')
      ->orderBy('id', 'desc')
      ->findAll();
  }

  /**
   * Get invoices that match 'BIM2D' in `project_name` or `subject`.
   *
   * @return array
   */
  public function getBim2d(): array
  {
    return $this->like('project_name', 'BIM2D')
      ->orLike('subject', 'BIM2D', 'both')
      ->orderBy('id', 'desc')
      ->findAll();
  }

  /**
   * Get invoices that match 'BIM3D' in `project_name` or `subject`.
   *
   * @return array
   */
  public function getBim3d(): array
  {
    return $this->like('project_name', 'BIM3D')
      ->orLike('subject', 'BIM3D', 'both')
      ->orderBy('id', 'desc')
      ->findAll();
  }

  /**
   * Get invoices by project ID.
   *
   * @param int $id
   * @return array
   */
  public function getByIdProject(int $id): array
  {
    return $this->where('project_id', $id)
      ->findAll();
  }

  /**
   * Get project ID by invoice ID.
   *
   * @param int $ids
   * @return array|null
   */
  public function getById(int $ids): ?array
  {
    return $this->db->table('invoices')
      ->select('project_id')
      ->where('id', $ids)
      ->get()
      ->getRowArray();
  }

  /**
   * Get project name by project ID.
   *
   * @param int $idp
   * @return array|null
   */
  public function getByIdp(int $idp): ?array
  {
    return $this->db->table('projects')
      ->select('name')
      ->where('id', $idp)
      ->get()
      ->getRowArray();
  }

  /**
   * Get project reference by project ID.
   *
   * @param int $idp
   * @return array|null
   */
  public function getByRef(int $idp): ?array
  {
    return $this->db->table('projects')
      ->select('ref_projet as refp')
      ->where('id', $idp)
      ->get()
      ->getRowArray();
  }

}