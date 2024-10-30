<?php

namespace App\Models;

use CodeIgniter\Model;

class ModuleModel extends Model
{
    protected $table = 'modules';  // Define the table
    protected $primaryKey = 'id';  // Define the primary key
    protected $allowedFields = ['id', 'name', 'link', 'type', 'icon', 'sort', 'actif', 'default_module'];
}
