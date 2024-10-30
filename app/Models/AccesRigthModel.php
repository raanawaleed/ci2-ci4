<?php

namespace App\Models;

use CodeIgniter\Model;
class AccesRigthModel extends Model
{
    protected $table = 'access_rights';
    protected $primaryKey = 'id';

    protected $allowedFields = ['article_id'];
    
    static $belongs_to = array(
        array('TicketHasArticle', 'foreign_key' => 'article_id')
    );


    public function getArticle()
    {
        return $this->db->table('ticket_has_articles')
            ->where('id', $this->article_id) // Assuming 'article_id' is the foreign key
            ->get()
            ->getRow();
    }
}
