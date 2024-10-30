<?php

namespace App\Models;

use CodeIgniter\Model;

class VCompanieModel extends Model
{
    protected $table = 'companies'; // Define the table name
    protected $primaryKey = 'id'; // Define the primary key
    protected $allowedFields = [
        'reference',
        'name',
        'client_id',
        'phone',
        'mobile',
        'address',
        'zipcode',
        'city',
        'inactive',
        'website',
        'country',
        'vat',
        'note',
        'province',
        'picture',
        'cnss'
    ]; // Define allowed fields for mass assignment

    // Retrieve the name of all companies
    public function getCompany()
    {
        return $this->select('name')->findAll(); // Use the model's method to get company names
    }
}
