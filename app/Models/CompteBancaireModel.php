<?php

namespace App\Models;

use CodeIgniter\Model;

class CompteBancaireModel extends Model
{

    protected $table = 'comptes_bancaires';

    protected $primaryKey = 'id';

    /**
     * Get a bank account by its ID.
     *
     * @param int $id
     * @return array|null
     */
    public function getCompteById(int $id): ?array
    {
        return $this->find($id);
    }

    /**
     * Get the last inserted bank account ID.
     *
     * @return int|null
     */
    public function getLastId(): ?int
    {
        return $this->selectMax('id')->first()['id'] ?? null;
    }
}