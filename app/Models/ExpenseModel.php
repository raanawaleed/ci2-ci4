<?php

namespace App\Models;

use CodeIgniter\Model;

class ExpenseModel extends Model
{
    protected $table = 'expenses';        // Define the table name
    protected $primaryKey = 'id';         // Define the primary key
    protected $useTimestamps = true;      // Enable timestamps if necessary
    protected $allowedFields = [
        'description',
        'type',
        'category',
        'date',
        'currency',
        'value',
        'vat',
        'reference',
        'project_id',
        'rebill',
        'invoice_id',
        'attachment',
        'attachment_description',
        'recurring',
        'recurring_until',
        'user_id'
    ];                                    // Define the fields allowed for mass assignment

    /**
     * Get the project related to this expense.
     *
     * @param int $projectId
     * @return array|null
     */
    public function getProject(int $projectId): ?array
    {
        return $this->db->table('projects')
            ->where('id', $projectId)
            ->get()
            ->getRowArray();
    }

    /**
     * Get the user related to this expense.
     *
     * @param int $userId
     * @return array|null
     */
    public function getUser(int $userId): ?array
    {
        return $this->db->table('users')
            ->where('id', $userId)
            ->get()
            ->getRowArray();
    }

    /**
     * Get the invoice related to this expense.
     *
     * @param int $invoiceId
     * @return array|null
     */
    public function getInvoice(int $invoiceId): ?array
    {
        return $this->db->table('invoices')
            ->where('id', $invoiceId)
            ->get()
            ->getRowArray();
    }

}