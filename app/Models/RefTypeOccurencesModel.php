<?php

namespace App\Models;

use CodeIgniter\Model;

class RefTypeOccurencesModel extends Model
{
    protected $table = 'ref_type_occurences';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'id_type_occ',
        'id_type',
        'name',
        'ordre',
        'description',
        'created_date',
        'update_date',
        'updated_by',
        'visible',
        'link'
    ];

    /**
     * Get the last ID from the table
     */
    public function getLastId()
    {
        return $this->selectMax('id')
            ->first()['id'];
    }

    /**
     * Get the name of the occurrence by ID
     */
    public function getOccNameById($id_type_occ)
    {
        return $this->select('name')
            ->where('id_type_occ', $id_type_occ)
            ->first();
    }

    /**
     * Get referentials by type ID
     */
    public function getReferentielsByIdType($id)
    {
        return $this->select('id, id_type_occ, name, description')
            ->where('id_type', $id)
            ->where('visible', 1)
            ->orderBy('id', 'desc')
            ->findAll();
    }

    /**
     * Get referential by ID
     */
    public function getReferentielsById($id)
    {
        return $this->find($id);
    }

    /**
     * Get referentials by type ID and optionally by name
     */
    public function getReferentiels($idType, $name = null)
    {
        $this->where('id_type', $idType)
            ->where('visible', 1);

        if (!is_null($name)) {
            $this->where('name', $name);
        }

        return $this->first();
    }

    /**
     * Get natures by category ID
     */
    public function getNature($id)
    {
        $sql = "SELECT * FROM ref_type_occurences WHERE id_type_occ IN ($id)";
        return $this->db->query($sql)->getResult();
    }

    /**
     * Get all referentials by type code and visibility
     */
    public function getAllReferentielsByCodeType($code, $visible = true, $return_array = false)
    {
        $this->select("{$this->table}.*")
            ->join('ref_type', 'id_type = ref_type.id', 'inner')
            ->where('ref_type.name', $code);

        if ($visible) {
            $this->where('visible', 1);
        }

        $query = $this->get();

        if ($return_array) {
            $array = [];
            foreach ($query->getResult() as $row) {
                $array[$row->id] = $row;
            }
            return $array;
        }

        return $query->getResult();
    }

    /**
     * Get referentials by type ID with optional visibility
     */
    public function getTabReferentielsByIdType($id, $visible = true)
    {
        $this->where('id_type', $id);

        if ($visible) {
            $this->where('visible', 1);
        }

        $query = $this->findAll();
        $tab = [];

        foreach ($query as $value) {
            $tab[$value['id']] = $value;
        }

        return $tab;
    }

    /**
     * Update referential by ID
     */
    public function updateReferentielById($tab, $id)
    {
        return $this->update($id, $tab);
    }

    /**
     * Update referential by type ID
     */
    public function updateReferentielByTypeId($tab, $id_type)
    {
        return $this->where('id_type', $id_type)
            ->set($tab)
            ->update();
    }

    /**
     * Get accepted leave records
     */
    public function getaccept()
    {
        return $this->db->table('t_pasa_conges')
            ->where('statut', 28)
            ->orderBy('id', 'desc')
            ->get()
            ->getResult();
    }

    /**
     * Get pending leave records
     */
    public function getattent()
    {
        return $this->db->table('t_pasa_conges')
            ->where('statut', 162)
            ->orderBy('id', 'desc')
            ->get()
            ->getResult();
    }
}
