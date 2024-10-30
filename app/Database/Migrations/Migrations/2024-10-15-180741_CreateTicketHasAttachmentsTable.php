<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTicketHasAttachments extends Migration
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
            'ticket_id' => [
                'type' => 'BIGINT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
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
        $this->forge->addForeignKey('ticket_id', 'tickets', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('ticket_has_attachments');
    }

    public function down()
    {
        $this->forge->dropTable('ticket_has_attachments');
    }
}
