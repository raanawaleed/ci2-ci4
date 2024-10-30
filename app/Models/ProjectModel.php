<?php

namespace App\Models;

use CodeIgniter\Model;

class ProjectModel extends Model
{
  protected $table = 'projects';
  protected $primaryKey = 'id';

  protected $allowedFields = [
    'reference',
    'name',
    'description',
    'start',
    'end',
    'delivery',
    'progress',
    'tracking',
    'time_spent',
    'datetime',
    'sticky',
    'company_id',
    'note',
    'progress_calc',
    'hide_tasks',
    'enable_client_tasks',
    'project_num',
    'creation_date',
    'type_projet',
    'nature_projet',
    'ref_projet',
    'etat_projet',
    'chef_projet_id',
    'chef_projet_client_id',
    'sub_client_id',
    'surface',
    'longueur',
    'date_relance_1',
    'date_relance_2',
    'date_relance_3'
  ];

  public function overdueByDate(array $compArray, string $date): array
  {
    $builder = $this->builder();
    $builder->select('*')
      ->where('progress !=', 100)
      ->where('end <', $date);

    if (!empty($compArray)) {
      $builder->groupStart()
        ->whereIn('company_id', $compArray)
        ->groupEnd();
    }

    return $builder->orderBy('end')
      ->get()
      ->getResultArray();
  }

  public static function getAllTasksTime($projectID): string
  {
    $timesheetsModel = new ProjectHasTimeSheetModel();

    $tracking = $timesheetsModel->where('project_id', $projectID)->sum('time');

    // Convert total time from seconds to hours and minutes
    $trackingHours = floor($tracking / 3600);
    $trackingMinutes = floor(($tracking % 3600) / 60);

    // Use the language helper for translations
    return sprintf('%d %s %d %s', $trackingHours, lang('application.hours'), $trackingMinutes, lang('application.minutes'));
  }

  public function getTypeProjetById($id_type)
  {
    return $this->db->table('ref_type_occurences')
      ->select('name')
      ->where('id', $id_type)
      ->get()
      ->getRow();
  }

  public function getTicketsByProject($id_projet)
  {
    return $this->db->table('tickets')
      ->select('tickets.id, tickets.subject, users.firstname, users.lastname')
      ->join('users', 'tickets.collaborater_id = users.id')
      ->where('project_id', $id_projet)
      ->get()
      ->getResult();
  }

  public function getAll()
  {
    return $this->db->table('projects')
      ->select('projects.*, ref_type_occurences.name as txt_type_projet, companies.name as txt_client_name')
      ->join('ref_type_occurences', 'projects.type_projet = ref_type_occurences.id', 'left')
      ->join('companies', 'projects.company_id = companies.id', 'left')
      ->where('progress <', 100)
      ->orderBy('id', 'DESC')
      ->get()
      ->getResult();
  }

  public function calculateHours($id)
  {
    return $this->db->query("SELECT SUM(REPLACE(s.heures_pointees, '.30', '.50')) as periode
            FROM saisie_temps s, tickets t 
            WHERE s.ticket_id = t.id AND t.project_id = $id")
      ->getRow();
  }

  public function getProjectRef($id)
  {
    return $this->db->table('projects')
      ->where('id', $id)
      ->get()
      ->getRow();
  }

  public function getProjectClientName($id)
  {
    return $this->db->query("SELECT CONCAT(clients.firstname, ' ', clients.lastname) as name_client 
            FROM clients, projects 
            WHERE projects.sub_client_id = clients.id and projects.id = $id")
      ->getRow();
  }

  public function getNextProjectReference()
    {
        // Logic to retrieve the next project reference
        return $this->db->table($this->table)
            ->select('MAX(project_reference) as project_reference')
            ->get()
            ->getRow();
    }

    public function getCoreSettings()
    {
        // Logic to retrieve core settings
        return $this->db->table('settings')
            ->where('id_vcompanies', session()->get('current_company'))
            ->get()
            ->getRow();
    }
    
}
