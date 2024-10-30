<?php

namespace App\Models;

use CodeIgniter\Model;

class TicketHasAttachmentModel extends Model
{
    protected $table = 'ticket_has_attachments';
    protected $primaryKey = 'id';
    protected $allowedFields = ['ticket_id', 'filename', 'savename'];

    // Method to retrieve the ticket associated with an attachment
    public function getTicketForAttachment($attachmentId)
    {
        return $this->db->table($this->table)
            ->select('ticket_has_attachments.*, tickets.*')
            ->join('tickets', 'tickets.id = ticket_has_attachments.ticket_id')
            ->where('ticket_has_attachments.id', $attachmentId)
            ->get()
            ->getRow();
    }
}
