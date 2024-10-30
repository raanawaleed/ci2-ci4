<?php

namespace App\Models;

use CodeIgniter\Model;

class SalarieModel extends Model
{
    protected $table = 'salaries';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'code',
        'nom',
        'prenom',
        'adresse1',
        'adresse2',
        'codepostal',
        'tel1',
        'tel2',
        'pays',
        'ville',
        'skype',
        'mail',
        'nombanque',
        'rib',
        'iban',
        'bic',
        'chef_famille',
        'salaire_brut',
        'nb_enfants',
        'nb_enfants_boursiers',
        'nb_enfants_handicape',
        'parents_charges',
        'droit_conge',
        'solde_conge_initiale',
        'date_debut_embauche',
        'echelon',
        'type_paiement',
        'type_contrat',
        'tauxhoraire',
        'categorie',
        'seraffectation',
        'etat',
        'numerocnss',
        'numerocin',
        'datedenaissance',
        'lieudenaissance',
        'datedelivrance',
        'situationfamiliale'
    ];

    public function idsal($ids)
    {
        return $this->where('id', $ids)
            ->select('seraffectation')
            ->first()->seraffectation ?? null;
    }

    // Récupérer info salarié
    public function getAll()
    {
        return $this->findAll();
    }

    // Get all salaries with MMS affectation
    public function getmmssalarie()
    {
        return $this->where(['seraffectation' => 'MMS', 'etat' => '1'])->findAll();
    }

    // Get all salaries with BIM2D affectation
    public function getBIM2Dsalarie()
    {
        return $this->where(['seraffectation' => 'BIM2D', 'etat' => '1'])->findAll();
    }

    // Get all salaries with BIM3D affectation
    public function getBIM3Dsalarie()
    {
        return $this->where(['seraffectation' => 'BIM3D', 'etat' => '1'])->findAll();
    }

    // Récupérer info salarié
    public function getIdSalarieByName($nom, $prenom)
    {
        return $this->select('id,droit_conge')
            ->where(['nom' => $nom, 'prenom' => $prenom])
            ->findAll();
    }

    // Récupérer info salarié
    public function getInfoSalarie($id)
    {
        return $this->find($id);
    }

    // Récupérer le signataire pour les attestations du salarié
    public function getSignataire()
    {
        return $this->db->table('core')->select('signataire')->get()->getResult();
    }

    // Récupérer info pour le tableau recap du salarie
    public function getInfoRecap()
    {
        return $this->db->query("
            SELECT salaries.id, code, nom, prenom, adresse1, r1.name AS fonction, 
                   numerocnss, numerocin, r2.name AS situation, salaire_brut 
            FROM salaries 
            JOIN ref_type_occurences r1 ON idfonction = r1.id 
            JOIN ref_type_occurences r2 ON situationfamiliale = r2.id
        ")->getResult();
    }

    // Récupérer le détail 1 d'un salarie
    public function getDetailSalarie($id)
    {
        return $this->select("code, nom, genre, datedenaissance, numerocnss, prenom, 
                              lieudenaissance, numerocin, datedelivrance, situationfamiliale, seraffectation")
            ->where('id', $id)
            ->findAll();
    }

    // Récupérer le détail 2 contact d'un salarie
    public function getDetailContact($id)
    {
        return $this->select("adresse1, adresse2, codepostal, tel1, tel2, pays, ville, skype, mail")
            ->where('id', $id)
            ->findAll();
    }

    // Récupérer le détail règlement d'un salarie
    public function getDetailReglement($id)
    {
        return $this->select("nombanque, rib, iban, bic")
            ->where('id', $id)
            ->findAll();
    }

    // Récupérer le détail paie d'un salarié
    public function getDetailPaie($id)
    {
        return $this->select("chef_famille, salaire_brut, nb_enfants, nb_enfants_boursiers, 
                              nb_enfants_handicape, parents_charges, droit_conge, 
                              solde_conge_initiale, date_debut_embauche, echelon, 
                              type_paiement, type_contrat, tauxhoraire, categorie")
            ->where('id', $id)
            ->findAll();
    }

    public function ajouterSaisieTickets($tickets, $tab_insert, $delete_first = true, $xuser = null, $mois = null, $annee = null)
    {
        $this->db->transStart();

        if ($delete_first && !is_null($mois) && !is_null($annee)) {
            $this->supprimerTempsTickets($this->table_saisie, $tickets, $xuser, $mois, $annee);
        }

        $this->db->table($this->table_saisie)->insertBatch($tab_insert);
        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            log_message('error', 'saisietemps_model: ajout des saisies des temps : (suppression: $delete_first) ' . $this->db->getLastQuery());
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

        $this->db->table($this->table_planification)->insertBatch($tab_insert);
        $this->db->table($this->table_saisie)->insertBatch($tab_insert);
        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            log_message('error', 'saisietemps_model: ajout des planification des temps : ' . $this->db->getLastQuery());
            return false;
        }
        return true;
    }

    function supprimerTempsTickets($table, $tickets, $user_id, $mois, $annee)
    {
        if ($table == '1') {
            $table = $this->table_saisie;
        } else if ($table == '0') {
            $table = $this->table_planification;
        }

        $this->db->table($table)
            ->where('utilisateur_id', $user_id)
            ->whereIn('ticket_id', $tickets)
            ->where("EXTRACT(YEAR FROM date)", $annee)
            ->where("EXTRACT(MONTH FROM date)", $mois)
            ->delete();
    }

    public function getValidationSaisieByUserAndDate($user_id, $mois, $annee)
    {
        return $this->db->table($this->table_saisie)
            ->select("validation")
            ->distinct()
            ->where('utilisateur_id', $user_id)
            ->where("EXTRACT(YEAR FROM date)", $annee)
            ->where("EXTRACT(MONTH FROM date)", $mois)
            ->get()
            ->getResult();
    }

    public function executeValidation($valider_users, $mois, $annee)
    {
        $this->db->transStart();
        foreach ($valider_users as $user_id => $value) {
            $this->db->table($this->table_saisie)
                ->set('validation', $value)
                ->where('utilisateur_id', $user_id)
                ->where("EXTRACT(YEAR FROM date)", $annee)
                ->where("EXTRACT(MONTH FROM date)", $mois)
                ->update();
        }
        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            log_message('error', 'saisietemps_model: executeValidation : ' . $this->db->getLastQuery());
            return false;
        }
        return true;
    }


    public function getLastId()
    {
        return $this->selectMax('id')->first()->id ?? null;
    }

    public function getcalendersalaries()
    {
        $idss = $this->user->salaries_id;
        $naturename = $this->user->nature;

        return $this->db->query("
            SELECT id, id_salarie, date, description, nature, etat 
            FROM calendar_salarie
            WHERE id_salarie = $idss OR nature = '$naturename'
            ORDER BY date DESC
        ")->getResult();
    }

    public function getcalendersalaries2()
    {
        return $this->db->table($this->table)
            ->select('*')
            ->where('etat', '1')
            ->where('seraffectation !=', 'NULL')
            ->get()
            ->getResult();
    }
}