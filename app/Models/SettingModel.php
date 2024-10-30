<?php

namespace App\Models;

use CodeIgniter\Model;

class SettingModel extends Model
{
    protected $table = 'setting_document_rh'; // You can modify this based on your actual table
    protected $primaryKey = 'id_setting_rh';

    protected $allowedFields = [
        'logo_fiche_paie',
        'logo_virement_salaire',
        'logo_journal_paie',
        'logo_doc_adminis'
    ];

    public function getDataById($id, $table)
    {
        return $this->db->table($table)
            ->where('id', $id)
            ->get()
            ->getRow();
    }

    public function addData($data, $table)
    {
        $this->db->table($table)->insert($data);
        return $this->db->insertID();
    }

    public function addBatchData($data, $table)
    {
        $this->db->table($table)->insertBatch($data);
    }

    public function updatDataById($data, $id, $table)
    {
        $this->db->table($table)
            ->where('id', $id)
            ->update($data);
    }

    public function getCompanySettings($conditions)
    {
        return $this->where($conditions)->first();
    }
}
