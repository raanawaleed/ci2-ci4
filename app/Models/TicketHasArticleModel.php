<?php

namespace App\Models;

use CodeIgniter\Model;

class TicketHasArticleModel extends Model
{
  protected $table = 'ticket_has_articles'; // Your table name
  protected $primaryKey = 'id'; // Assuming 'id' as the primary key
  protected $allowedFields = [
    'ticket_id',
    'from',
    'reply_to',
    'to',
    'cc',
    'subject',
    'message',
    'datetime',
    'internal'
  ];

  // Relationships can be managed through query joins in CodeIgniter 4
  // You can handle them with manual queries as needed.

  public function getAttachmentsByArticle($articleId)
  {
    return $this->db->table('article_has_attachments')
      ->where('ticket_has_article_id', $articleId)
      ->get()
      ->getResult();
  }
  public function getTicketForArticle($articleId)
  {
    return $this->db->table('ticket_has_articles')
      ->select('ticket_has_articles.*, tickets.*')
      ->join('tickets', 'tickets.id = ticket_has_articles.ticket_id')
      ->where('ticket_has_articles.id', $articleId)
      ->get()
      ->getRow();
  }

  public function getClientForArticle($fromEmail)
  {
    return $this->db->table('clients')
      ->where('email', $fromEmail)
      ->get()
      ->getRow();
  }
}
