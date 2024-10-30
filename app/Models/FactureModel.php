<?php

namespace App\Models;

use CodeIgniter\Model;

class FactureModel extends Model
{

    protected $table = 'facture';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'reference',
        'company_id',
        'subject',
        'notes',
        'status',
        'currency',
        'issue_date',
        'creation_date',
        'sent_date',
        'paid_date',
        'avoir_date',
        'terms',
        'discount',
        'deduction',
        'subscription_id',
        'project_id',
        'tax',
        'estimate',
        'estimate_accepted_date',
        'estimate_sent',
        'sum',
        'second_tax',
        'estimate_reference',
        'paid',
        'outstanding',
        'timbre_fiscal',
        'estimate_num',
        'sumht',
        'project_name',
        'project_surface',
        'calcul_heure',
        'delivery',
        'chef_projet_client',
        'chef_projet',
        'unite',
        'project_ref'
    ]; // Define your allowed fields here

    public function getLastId(): ?int
    {
        return $this->selectMax('id')
            ->first()['id'] ?? null;
    }

    public function getByCompany(int $id): array
    {
        return $this->where('company_id', $id)
            ->findAll();
    }

    public function getById(int $id): ?array
    {
        return $this->where('id', $id)
            ->first();
    }

    public function getAll(): array
    {
        return $this->orderBy('id', 'desc')
            ->findAll();
    }

    public function getElementForExcel(): array
    {
        return $this->select('estimate_num, company_id, subject, issue_date, currency, sumht, sum, status')
            ->orderBy('id', 'desc')
            ->findAll();
    }

    public function getLastInvoice(): ?array
    {
        return $this->orderBy('id', 'DESC')
            ->first();
    }

    public function getBySubscriptionId(int $id): array
    {
        return $this->where('subscription_id', $id)
            ->findAll();
    }

    public function getByIdProject(int $id): array
    {
        return $this->where('project_id', $id)
            ->findAll();
    }
}