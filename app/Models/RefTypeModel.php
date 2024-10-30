<?php

namespace App\Models;

use CodeIgniter\Model;

class RefTypeModel extends Model
{
  protected $table = 'ref_type';
  protected $primaryKey = 'id';
  protected $allowedFields = ['id_type', 'name', 'description'];

  public function getLastId()
  {
    return $this->selectMax('id')->first()['id'];
  }

  public function getRefTypeByName($name)
  {
    return $this->where('name', $name)->first();
  }
}
