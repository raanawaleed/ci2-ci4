<?php

namespace App\Models;

use CodeIgniter\Model;

class ClientModel extends Model
{

	protected $table = 'clients';
	protected $primaryKey = 'id';
	protected $password;
	protected $hashed_password;

	//protected $allowedFields = ['field1', 'field2'];


	/**
	 * Automatically hash the password before saving the model.
	 *
	 * @return void
	 */
	protected function beforeSave(): void
	{
		if ($this->password) {
			$this->hashed_password = $this->hashPassword($this->password);
		}
	}

	/**
	 * Set and hash a new password for the client.
	 *
	 * @param string $plaintext
	 * @return void
	 */
	public function setPassword(string $plaintext): void
	{
		$this->hashed_password = $this->hashPassword($plaintext);
	}

	/**
	 * Hash the provided password using SHA-256 with a salt.
	 *
	 * @param string $password
	 * @return string
	 */
	private function hashPassword(string $password): string
	{
		$salt = bin2hex(random_bytes(32)); // Generate a 64-character hexadecimal salt
		$hash = hash('sha256', $salt . $password);

		return $salt . $hash;
	}

	/**
	 * Validate the provided password against the stored hash.
	 *
	 * @param string $password
	 * @return bool
	 */
	public function validatePassword(string $password): bool
	{
		$salt = substr($this->hashed_password, 0, 64); // Extract the salt from the stored hash
		$storedHash = substr($this->hashed_password, 64); // Extract the hash value

		// Generate the hash from the provided password and salt
		$passwordHash = hash('sha256', $salt . $password);

		return $passwordHash === $storedHash;
	}

	/**
	 * Get all projects related to the client.
	 *
	 * @return array
	 */
	public function getProjects(): array
	{
		return $this->db->table('projects')
			->where('client_id', $this->id)
			->get()
			->getResultArray();
	}

	/**
	 * Get all invoices related to the client.
	 *
	 * @return array
	 */
	public function getInvoices(): array
	{
		return $this->db->table('invoices')
			->where('client_id', $this->id)
			->get()
			->getResultArray();
	}

	/**
	 * Get the company related to the client.
	 *
	 * @return array|null
	 */
	public function getCompany(): ?array
	{
		return $this->db->table('companies')
			->where('id', $this->company_id)
			->get()
			->getRowArray();
	}

	/**
	 * Get all clients from the table.
	 *
	 * @return array
	 */
	public function getAll(): array
	{
		return $this->findAll();
	}

	/**
	 * Get a client by its ID.
	 *
	 * @param int $id
	 * @return array|null
	 */
	public function getUserById(int $id): ?array
	{
		return $this->find($id);
	}
}
