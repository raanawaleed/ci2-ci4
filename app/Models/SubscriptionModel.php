<?php

namespace App\Models;

use CodeIgniter\Model;

class SubscriptionModel extends Model
{
	protected $table = 'subscriptions'; // Your table name
	protected $primaryKey = 'id';
	protected $allowedFields = [
		'reference',
		'company_id',
		'status',
		'currency',
		'issue_date',
		'end_date',
		'frequency',
		'next_payment',
		'terms',
		'discount',
		'subscribed',
		'second_tax',
		'subscription_num',
		'id_vcompanies',
		'creation_date'
	];

	// Example of relationships in CI4 (handle with query methods or entity if needed)
	// You can handle relationships using joins in your queries.

	// Function to fetch new invoices with outstanding payments
	public function newInvoiceOutstanding($comp_array, $date)
	{
		$builder = $this->db->table($this->table);
		$builder->select('*')
			->where('status !=', 'Inactive')
			->where('end_date >', $date)
			->where('next_payment <=', $date)
			->orderBy('next_payment');

		return $builder->get()->getResult();
	}
}
