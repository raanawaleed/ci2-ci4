<?php

namespace App\Models;

use CodeIgniter\Model;

class SaisieTempsModel extends Model
{

    protected $table = 'saisie_temps';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'ticket_id',
        'utilisateur_id',
        'date',
        'heures_pointees',
        'created_by',
        'created_at',
        'validation',
        'autre_saisie',
        'type_ticket',
        'color',
        'bdate',
        'des',
        'rdate'
    ];


    public function getSaisieByUserAndDate($is_saisie, $user_id, $mois, $annee, $type_ticket, $total = false)
    {
        $table = $is_saisie ? $this->table_saisie : $this->table_planification;

        if ($total) {
            $this->select("date, SUM(REPLACE(SUBSTRING(SUBSTRING_INDEX(heures_pointees, '.', 1), LENGTH(SUBSTRING_INDEX(heures_pointees, '.', 1 - 1)) + 1), '.', '')) as nb_heures, 
                SUM(RPAD(REPLACE(SUBSTRING(SUBSTRING_INDEX(heures_pointees, '.', 2), LENGTH(SUBSTRING_INDEX(heures_pointees, '.', 2 - 1)) + 1), '.', ''), 2, '0')) as nb_minutes", false);
            $this->groupBy("date");
        } else {
            $this->join('users as u', 'created_by=u.id', 'inner');

            if ($type_ticket->alias == "P") {
                $this->select("$table.*, 
                    REPLACE(SUBSTRING(SUBSTRING_INDEX(heures_pointees, '.', 1), LENGTH(SUBSTRING_INDEX(heures_pointees, '.', 1 - 1)) + 1), '.', '') as nb_heures_pointees, 
                    RPAD(REPLACE(SUBSTRING(SUBSTRING_INDEX(heures_pointees, '.', 2), LENGTH(SUBSTRING_INDEX(heures_pointees, '.', 2 - 1)) + 1), '.', ''), 2, '0') as nb_minutes_pointees, 
                    CONCAT(firstname, ' ', lastname) as user_name, p.name as project_name, sp.name as sub_project_name, t.subject as ticket_name", false);
                $this->join('tickets as t', 'ticket_id=t.id AND type_ticket = ' . $type_ticket->id, 'inner');
                $this->join('projects as p', 't.project_id=p.id', 'inner');
                $this->join('project_has_sub_projects as sp', 't.sub_project_id=p.id', 'left');
            } elseif ($type_ticket->alias == "D") {
                $this->select("$table.*, 
                    REPLACE(SUBSTRING(SUBSTRING_INDEX(heures_pointees, '.', 1), LENGTH(SUBSTRING_INDEX(heures_pointees, '.', 1 - 1)) + 1), '.', '') as nb_heures_pointees, 
                    REPLACE(SUBSTRING(SUBSTRING_INDEX(heures_pointees, '.', 2), LENGTH(SUBSTRING_INDEX(heures_pointees, '.', 2 - 1)) + 1), '.', '') as nb_minutes_pointees, 
                    CONCAT(firstname, ' ', lastname) as user_name, NULL as project_name, NULL as sub_project_name, t.subject as ticket_name", false);
                $this->join('ticket_par_defaults as t', 'ticket_id=t.id AND type_ticket = ' . $type_ticket->id, 'inner');
            }
        }

        $this->from($table);
        $this->where('utilisateur_id', $user_id);
        $this->where("EXTRACT(YEAR FROM date) = $annee");
        $this->where("EXTRACT(MONTH FROM date) = $mois");

        return $this->get()->getResult();
    }

    public function ajouterSaisieTickets($tickets, $tab_insert, $delete_first = true, $xuser = null, $mois = null, $annee = null)
    {
        $this->db->transStart();
        if ($delete_first && !is_null($mois) && !is_null($annee)) {
            $this->supprimerTempsTickets($this->table_saisie, $tickets, $xuser, $mois, $annee);
        }

        $this->insertBatch($this->table_saisie, $tab_insert);
        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            log_message('error', 'saisietemps_model: ajout des saisies des temps : $this->table_saisie : (suppression: $delete_first) ' . $this->db->getLastQuery());
            return false;
        }
        return true;
    }

    public function ajouterPlanificationTickets($tickets, $tab_insert, $delete_first = true, $xuser = null, $mois = null, $annee = null)
    {
        $this->db->transStart();
        if ($delete_first && !is_null($mois) && !is_null($annee)) {
            $this->supprimerTempsTickets($this->table_planification, $tickets, $xuser, $mois, $annee);
            $this->supprimerTempsTickets($this->table_saisie, $tickets, $xuser, $mois, $annee);
        }

        $this->insertBatch($this->table_planification, $tab_insert);
        $this->insertBatch($this->table_saisie, $tab_insert);
        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            log_message('error', 'saisietemps_model: ajout des planification des temps : ' . $this->db->getLastQuery());
            return false;
        }
        return true;
    }

    public function supprimerTempsTickets($table, $tickets, $user_id, $mois, $annee)
    {
        $table = $table == '1' ? $this->table_saisie : $this->table_planification;

        $this->where('utilisateur_id', $user_id);
        $this->whereIn('ticket_id', $tickets);
        $this->where("EXTRACT(YEAR FROM date) = $annee");
        $this->where("EXTRACT(MONTH FROM date) = $mois");
        $this->delete($table, false);
    }

    public function getSaisieAllUsersByMonth($table, $mois, $annee)
    {
        $table = $table == '1' ? $this->table_saisie : $this->table_planification;

        $this->select("u.id, CONCAT(u.firstname, ' ', u.lastname) as user_name, SUM(heures_pointees) as nb_heures, RPAD(SUM(heures_pointees), 2, '0') as nb_minutes", false);
        $this->from('users as u');
        $this->notLike('u.status', 'deleted');
        $this->join($table . ' as t', 't.utilisateur_id=u.id AND EXTRACT(YEAR FROM t.date) = ' . $annee . ' AND EXTRACT(MONTH FROM t.date) = ' . $mois, 'left');
        $this->groupBy("u.id");

        $query = $this->get();
        $tab = $query->getResult();

        foreach ($tab as $key => $row) {
            $total_heures = $this->getTotalHeures($row->nb_heures, $row->nb_minutes);
            $tab[$key]->total = $total_heures . '.' . $this->getResteMinutes($row->nb_minutes);
            $tab[$key]->nb_days = floor($total_heures / 8);
            $tab[$key]->nb_days_mod = $total_heures % 8;
        }
        return $tab;
    }

    public function getValidationSaisieByUserAndDate($user_id, $mois, $annee)
    {
        $table = $this->table_saisie;

        $this->select("validation");
        $this->distinct();
        $this->from($table);
        $this->where('utilisateur_id', $user_id);
        $this->where("EXTRACT(YEAR FROM date) = $annee");
        $this->where("EXTRACT(MONTH FROM date) = $mois");
        $this->orderBy("id", "desc");
        return $this->get()->getResult();
    }

    public function executeValidation($valider_users, $mois, $annee)
    {
        $this->db->transStart();
        foreach ($valider_users as $user_id => $value) {
            $this->set('validation', $value, false);
            $this->where('utilisateur_id', $user_id);
            $this->where("EXTRACT(YEAR FROM date) = $annee");
            $this->where("EXTRACT(MONTH FROM date) = $mois");
            $this->update($this->table_saisie);
        }
        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            log_message('error', 'saisietemps_model: erreur lors de la validation des saisies : ' . $this->db->getLastQuery());
            return false;
        }
        return true;
    }

    public function getTotalHeures($heures, $minutes)
    {
        return (int)$heures + ((int)$minutes / 60);
    }

    public function getResteMinutes($minutes)
    {
        return (int)$minutes % 60;
    }

    public function countTicketByType($type_ticket)
    {
        $this->select('COUNT(*) as count');
        $this->where('type_ticket', $type_ticket);
        $query = $this->get();
        return $query->getRow()->count;
    }
}
