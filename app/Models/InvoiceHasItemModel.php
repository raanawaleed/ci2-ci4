<?php

namespace App\Models;

use CodeIgniter\Model;

class InvoiceHasItemModel extends Model
{
    protected $table = 'invoice_has_items';
    protected $primaryKey = 'id';
    protected $allowedFields = [ 'invoice_id', 'item_id', 'amount', 'name', 'value', 'description', 'type', 'tva', 'unit', 'discount', 'position']; // Example fields, adjust according to your table structure

    /**
     * Get the last inserted ID in the invoice_has_items table.
     *
     * @return int|null
     */
    public function getLastId(): ?int
    {
        return $this->selectMax('id')->get()->getRow()->id ?? null;
    }

}