<?php

namespace App\Models;

use CodeIgniter\Model;

class AvoirHasItemModel extends Model {

    protected $table = 'avoir_has_items';
    protected $primaryKey = 'id';

    /**
     * Get the maximum ID from the 'avoir_has_items' table.
     *
     * @return int|null
     */
    public function getLastId(): ?int
    {
        return $this->selectMax('id')->first()['id'] ?? null;
    }
	
} 