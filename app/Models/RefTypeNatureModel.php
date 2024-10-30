<?php

namespace App\Models;

use CodeIgniter\Model;

class RefTypeNatureModel extends Model
{
	protected $table = 'ref_type_nature';
	protected $primaryKey = 'id';
	protected $allowedFields = ['id_category', 'id_nature'];

	public function getNatureByCat($id)
	{
		return $this->select('id_nature')
			->where('id_category', $id)
			->findAll();
	}
}
