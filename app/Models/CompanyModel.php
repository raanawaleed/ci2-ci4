<?php

namespace App\Models;

use CodeIgniter\Model;

class CompanyModel extends Model
{

    protected $table = 'companies';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    protected $allowedFields = [
        'reference',
        'name',
        'phone',
        'mobile',
        'address',
        'zipcode',
        'city',
        'website',
        'email',
        'country',
        'vat',
        'timbre_fiscal',
        'guarantee',
        'tva',
        'note',
        'passager',
        'inactive'
    ]; // Update with your table's fields

    /**
     * Get all company references.
     *
     * @return array
     */
    public function getAllReference(): array
    {
        return $this->select('reference')
            ->findAll();
    }

    /**
     * Get a client's first name and last name by ID.
     *
     * @param int $id
     * @return array|null
     */
    public function getClientById(int $id): ?array
    {
        return $this->db->table('clients')
            ->select('firstname, lastname')
            ->where('id', $id)
            ->get()
            ->getRowArray();
    }

    /**
     * Get all active clients related to the company.
     *
     * @return array
     */
    public function getActiveClients(): array
    {
        return $this->db->table('clients')
            ->where('inactive !=', 1)
            ->get()
            ->getResultArray();
    }

    /**
     * Get all invoices related to the company.
     *
     * @return array
     */
    public function getInvoices(): array
    {
        return $this->db->table('invoices')
            ->where('company_id', $this->id)
            ->get()
            ->getResultArray();
    }

    /**
     * Get all projects related to the company.
     *
     * @return array
     */
    public function getProjects(): array
    {
        return $this->db->table('projects')
            ->where('company_id', $this->id)
            ->get()
            ->getResultArray();
    }

    /**
     * Get all subscriptions related to the company.
     *
     * @return array
     */
    public function getSubscriptions(): array
    {
        return $this->db->table('subscriptions')
            ->where('company_id', $this->id)
            ->get()
            ->getResultArray();
    }

    /**
     * Get the client that the company belongs to, if applicable.
     *
     * @return array|null
     */
    public function getParentClient(): ?array
    {
        return $this->db->table('clients')
            ->where('id', $this->client_id)
            ->where('inactive !=', 1)
            ->get()
            ->getRowArray();
    }

    /**
     * Get data for exporting non-passager companies to Excel.
     *
     * @return array
     */
    public function getForExport(): array
    {
        return $this->select('reference, name, phone, mobile, address, zipcode, city, 
                              website, email, country, vat, timbre_fiscal, guarantee, tva, note')
            ->where('passager IS NULL')
            ->findAll();
    }

    /**
     * Get data for exporting passager companies to Excel.
     *
     * @return array
     */
    public function getForExportPassagers(): array
    {
        return $this->select('reference, name, phone, mobile, address, zipcode, city, 
                              website, email, country, vat, timbre_fiscal, guarantee, tva, note')
            ->where('passager', 1)
            ->findAll();
    }
}