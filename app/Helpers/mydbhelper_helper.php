<?php

namespace App\Helpers;

use App\Models\ItemFamilyModel;
use App\Models\CompanyModel;
use App\Models\InvoiceModel;
use App\Models\RefTypeOccurencesModel;
use App\Models\RefTypeOccurrencesModel;

if (!function_exists('get_child')) {
  /**
   * Gets child items based on the parent ID.
   *
   * @param int $iditem The parent item ID.
   * @return array List of child items.
   */
  function get_child(int $iditem): array
  {
    $model = new ItemFamilyModel();
    return $model->where('parent', $iditem)->findAll();
  }
}

if (!function_exists('get_company')) {
  /**
   * Gets company details based on the company ID.
   *
   * @param int $iditem The company ID.
   * @return object|null Company details or null if not found.
   */
  function get_company(int $iditem): ?object
  {
    $model = new CompanyModel();
    return $model->find($iditem);
  }
}

if (!function_exists('ofset')) {
  /**
   * Returns the offset of digits in a string.
   *
   * @param string $text The input string.
   * @return int|false The length of the non-digit prefix or false.
   */
  function ofset(string $text)
  {
    preg_match('/^\D*(?=\d)/', $text, $m);
    return isset($m[0]) ? strlen($m[0]) : false;
  }
}

if (!function_exists('find_devis')) {
  /**
   * Finds an invoice by its ID.
   *
   * @param int $id The invoice ID.
   * @return object|null Invoice details or null if not found.
   */
  function find_devis(int $id): ?object
  {
    $model = new InvoiceModel();
    return $model->where('id_facture', $id)->first();
  }
}

// Returns the text string of a reference type.
if (!function_exists('get_type_txt')) {
  /**
   * Gets the name of a reference type occurrence by ID.
   *
   * @param int $id_type The reference type ID.
   * @return string The name of the reference type.
   */
  function get_type_txt(int $id_type): string
  {
    $model = new RefTypeOccurencesModel();
    $type = $model->find($id_type);

    return $type ? $type->name : "";
  }
}
