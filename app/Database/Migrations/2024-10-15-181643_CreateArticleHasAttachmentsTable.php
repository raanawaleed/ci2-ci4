<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateArticleHasAttachments extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'BIGINT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'article_id' => [
                'type' => 'BIGINT',
                'constraint' => 11,
                'null' => true,
                'unsigned' => true,
            ],
            'filename' => [
                'type' => 'VARCHAR',
                'constraint' => 250,
                'null' => true,
                'default' => '0',
                'collation' => 'utf8_general_ci',
            ],
            'savename' => [
                'type' => 'VARCHAR',
                'constraint' => 250,
                'null' => true,
                'collation' => 'utf8_general_ci',
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('article_id', 'ticket_has_articles', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('article_has_attachments');
    }

    public function down()
    {
        $this->forge->dropTable('article_has_attachments');
    }
}
