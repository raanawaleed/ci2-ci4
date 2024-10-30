<?php

namespace App\Models;

use CodeIgniter\Model;

class PrivatemessageModel extends Model
{
    protected $table = 'privatemessages';
    protected $primaryKey = 'id';  // Define the primary key
    protected $allowedFields = [
        'status',
        'sender',
        'recipient',
        'subject',
        'message',
        'time',
        'conversation',
        'deleted',
        'attachment',
        'attachment_link',
        'receiver_delete',
        'new_created',
        'company_id'
    ];

    public function getRecentMessages($userId)
    {
        return $this->select('privatemessages.id, privatemessages.status, privatemessages.subject, privatemessages.message, privatemessages.time, privatemessages.recipient, clients.userpic as userpic_c, users.userpic as userpic_u, users.email as email_u, clients.email as email_c, CONCAT(users.firstname," ", users.lastname) as sender_u, CONCAT(clients.firstname," ", clients.lastname) as sender_c')
            ->join('clients', 'CONCAT("c", clients.id) = privatemessages.sender', 'left')
            ->join('users', 'CONCAT("u", users.id) = privatemessages.sender', 'left')
            ->where('privatemessages.recipient', 'u' . $userId)
            ->where('privatemessages.status !=', 'deleted')
            ->orderBy('privatemessages.time', 'DESC')
            ->limit(6)
            ->findAll();
    }
}
