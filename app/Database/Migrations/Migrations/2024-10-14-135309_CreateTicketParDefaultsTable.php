<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTicketParDefaultsTable extends Migration
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
            'code' => [
                'type' => 'VARCHAR',
                'constraint' => 250,
                'null' => true,
            ],
            'subject' => [
                'type' => 'VARCHAR',
                'constraint' => 250,
                'null' => true,
            ],
            'status' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'default' => '0',
                'null' => true,
            ],
            'ordre' => [
                'type' => 'INT',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('ticket_par_defaults');
    }

    public function down()
    {
        $this->forge->dropTable('ticket_par_defaults');
    }
}
