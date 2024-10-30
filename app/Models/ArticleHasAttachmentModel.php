<?php

namespace App\Models;

use CodeIgniter\Model;

class ArticleHasAttachmentModel extends Model
{
    protected $table  = 'article_has_attachments';

    protected $primaryKey = 'id';

    static $belongs_to = array(
        array('TicketHasArticle', 'foreign_key' => 'article_id')
    );
}
