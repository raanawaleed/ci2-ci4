<?php

namespace App\Models;

use CodeIgniter\Model;

class InvoiceHasPaymentModel extends Model
{
  protected $table = 'invoice_has_payments';
  protected $primaryKey = 'id';
  protected $allowedFields = ['invoice_id', 'reference', 'amount', 'date', 'type', 'notes', 'user_id']; // Adjust as per your table fields

  /**
   * Get the last payment record ID.
   *
   * @return int|null
   */
  public function getLastId(): ?int
  {
    return $this->selectMax('id')->get()->getRow()->id ?? null;
  }

  /**
   * Get all payments for a specific invoice ID.
   *
   * @param int $invoiceId
   * @return array
   */
  public function getPaymentsByInvoiceId(int $invoiceId): array
  {
    return $this->where('invoice_id', $invoiceId)->findAll();
  }

  /**
   * Get all payments made by a specific user.
   *
   * @param int $userId
   * @return array
   */
  public function getPaymentsByUserId(int $userId): array
  {
    return $this->where('user_id', $userId)->findAll();
  }
}