<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTicketHasArticles extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'ticket_id' => [
                'type' => 'BIGINT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,

            ],
            'from' => [
                'type' => 'VARCHAR',
                'constraint' => 250,
                'null' => false,
                'default' => '0',
                'collation' => 'utf8_general_ci',
            ],
            'reply_to' => [
                'type' => 'VARCHAR',
                'constraint' => 250,
                'null' => true,
                'default' => '0',
                'collation' => 'utf8_general_ci',
            ],
            'to' => [
                'type' => 'VARCHAR',
                'constraint' => 250,
                'null' => true,
                'default' => '0',
                'collation' => 'utf8_general_ci',
            ],
            'cc' => [
                'type' => 'VARCHAR',
                'constraint' => 250,
                'null' => true,
                'default' => '0',
                'collation' => 'utf8_general_ci',
            ],
            'subject' => [
                'type' => 'VARCHAR',
                'constraint' => 250,
                'null' => true,
                'default' => '0',
                'collation' => 'utf8_general_ci',
            ],
            'message' => [
                'type' => 'TEXT',
                'null' => true,
                'collation' => 'utf8_general_ci',
            ],
            'datetime' => [
                'type' => 'VARCHAR',
                'constraint' => 250,
                'null' => true,
                'collation' => 'utf8_general_ci',
            ],
            'internal' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('ticket_id', 'tickets', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('ticket_has_articles');
    }

    public function down()
    {
        $this->forge->dropTable('ticket_has_articles');
    }
}
