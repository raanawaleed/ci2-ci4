<?php

namespace App\Models;

use CodeIgniter\Model;

class FactureHasItemModel extends Model
{
    protected $table = 'facture_has_items';
    protected $primaryKey = 'id';

    protected $allowedFields = ['facture_id', 'item_id', 'amount', 'name', 'value', 'description', 'type', 'tva', 'unit', 'discount', 'position']; // Adjust according to your table fields

    /**
     * Get the last ID from facture_has_items.
     *
     * @return int|null
     */
    public function getLastId(): ?int
    {
        return $this->selectMax('id')
            ->first()['id'] ?? null;
    }

}