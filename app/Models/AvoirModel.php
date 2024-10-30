<?php

namespace App\Models;

use CodeIgniter\Model;
class AvoirModel extends Model
{

    protected $table = 'avoirs';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    /**
     * Get the maximum ID from the 'avoirs' table.
     *
     * @return int|null
     */
    public function getLastId(): ?int
    {
        return $this->selectMax('id')->first()['id'] ?? null;
    }

    /**
     * Get a record by its ID.
     *
     * @param int $id
     * @return array|null
     */
    public function getById(int $id): ?array
    {
        return $this->find($id);
    }

    /**
     * Get the last record from the 'avoirs' table.
     *
     * @return array|null
     */
    public function getLastAvoir(): ?array
    {
        return $this->orderBy('id', 'DESC')->first();
    }

}