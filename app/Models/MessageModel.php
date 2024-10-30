<?php

namespace App\Models;

use CodeIgniter\Model;
class MessageModel extends Model
{
    protected $table = 'messages';  // Define the table
    protected $primaryKey = 'id';  // Define the primary key
    protected $allowedFields = ['id', 'project_id', 'media_id', 'from', 'text', 'datetime' ];
}
