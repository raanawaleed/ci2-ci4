<?php

namespace App\Models;

use CodeIgniter\Model;

class EventModel extends Model
{
  protected $table = 'events';
  protected $primaryKey = 'id';

  /**
   * Get the user associated with the event.
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
   * Get event details by ID.
   *
   * @param int $eventId
   * @return array|null
   */
  public function getEvent(int $eventId): ?array
  {
    return $this->find($eventId);
  }

}