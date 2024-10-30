<?php

namespace App\Models;

use CodeIgniter\Model;

class TicketModel extends Model
{
    protected $table = 'tickets';
    protected $primaryKey = 'id';
    protected $allowedFields = ['from', 'reference', 'type_id', 'etat_id', 'lock', 'subject', 'text', 'status', 'company_id', 'user_id', 'escalation_time', 'priority', 'created', 'updated', 'project_id', 'sub_project_id', 'collaborater_id', 'start', 'end', 'surface', 'longueur', 'new_created', 'deleted', 'closed'];

    // Column definitions for DataTables filtering and ordering
    protected $columnOrder = ['id', 'project_id', 'sub_project_id', 'subject', 'start', 'end', 'collaborater_id'];
    protected $columnSearch = ['projects.project_num', 'projects.type_projet', 'tickets.id', 'sub_project_id', 'projects.name', 'subject', 'users.firstname'];
    protected $order = ['tickets.id' => 'DESC'];

    public function getAll()
    {
        return $this->where('closed', 0)
            ->where('deleted', 0)
            ->findAll();
    }

    public function getTicketByTypeProjet($type_id)
    {
        return $this->select('tickets.*, projects.*')
            ->join('projects', 'tickets.project_id = projects.id', 'inner')
            ->where('projects.type_projet', $type_id)
            ->where('tickets.closed', 0)
            ->where('tickets.deleted', 0)
            ->findAll();
    }

    public function getAllTicketsByuser($id_user)
    {
        return $this->where('collaborater_id', $id_user)
            ->where('closed', 0)
            ->where('deleted', 0)
            ->findAll();
    }

    public function getAllTicketsByprj($id_prj)
    {
        return $this->where('project_id', $id_prj)
            ->where('closed', 0)
            ->where('deleted', 0)
            ->findAll();
    }

    public function getQtTicketsByprj($id_prj)
    {
        return $this->select('surface')
            ->where('project_id', $id_prj)
            ->where('closed', 0)
            ->where('deleted', 0)
            ->findAll();
    }

    public function getUserCreatedTicket($user_id)
    {
        return $this->select('users.firstname, users.lastname')
            ->join('users', 'users.id = tickets.user_id')
            ->findAll();
    }

    public function getTicketByIdProject($id)
    {
        return $this->select('surface')
            ->where('project_id', $id)
            ->findAll();
    }

    public function getTicketSubjectByIdProject($id)
    {
        return $this->select('tickets.*, users.firstname, users.lastname')
            ->join('users', 'tickets.collaborater_id = users.id', 'inner')
            ->where('project_id', $id)
            ->findAll();
    }

    // DataTables integration methods

    private function _get_datatables_tickets($postData)
    {
        $current_user = session()->get('user_id');

        if (in_array($current_user, [53, 37, 59, 1, 35, 52])) {
            $this->select('tickets.*, projects.type_projet as type, projects.name as project, users.firstname as collaborater_firstname, users.lastname as collaborater_lastname, tickets.start, tickets.end');
            $this->join('users', 'tickets.collaborater_id = users.id', 'left');
            $this->join('projects', 'tickets.project_id = projects.id', 'left');
            $this->where('tickets.closed', 0);
            $this->where('tickets.deleted', 0);
            $this->orderBy('tickets.end', 'desc');
        } else {
            $this->select('tickets.*, projects.name as project, projects.type_projet as type, users.firstname as collaborater_firstname, users.lastname as collaborater_lastname, tickets.start, tickets.end');
            $this->join('users', 'tickets.collaborater_id = users.id', 'left');
            $this->join('projects', 'tickets.project_id = projects.id', 'left');
            $this->where('tickets.collaborater_id', $current_user);
            $this->where('tickets.closed', 0);
            $this->where('tickets.deleted', 0);
            $this->orderBy('tickets.end', 'desc');
        }

        // Search functionality
        $i = 0;
        foreach ($this->columnSearch as $item) {
            if ($postData['search']['value']) {
                if ($i === 0) {
                    $this->like($item, $postData['search']['value']);
                } else {
                    $this->orLike($item, $postData['search']['value']);
                }
            }
            $i++;
        }

        if (isset($postData['order'])) {
            $this->orderBy($this->columnOrder[$postData['order']['0']['column']], $postData['order']['0']['dir']);
        } else {
            $this->orderBy(key($this->order), $this->order[key($this->order)]);
        }
    }

    public function getRows($postData)
    {
        $this->_get_datatables_tickets($postData);
        if ($postData['length'] != 1) {
            $this->limit($postData['length'], $postData['start']);
        }
        return $this->findAll();
    }

    public function countAll()
    {
        return $this->countAllResults();
    }

    public function countFiltered($postData)
    {
        $this->_get_datatables_tickets($postData);
        return $this->countAllResults();
    }

    // Period per ticket
    public function getPeriodPerTicket($id)
    {
        $sql = "SELECT SUM(REPLACE(s.heures_pointees, '.30', '.50')) as periode
                FROM saisie_temps s, tickets t 
                WHERE s.ticket_id = t.id AND s.ticket_id = ?";
        $query = $this->db->query($sql, [$id]);
        return $query->getRow();
    }

    public function getPeriodPerTickettt($ticket_id)
    {
        return $this->select("SUM(REPLACE(saisie_temps.heures_pointees, '.30', '.50')) as periode")
            ->join('tickets', 'saisie_temps.ticket_id = tickets.id')
            ->where('saisie_temps.ticket_id', $ticket_id)
            ->findAll();
    }
}
