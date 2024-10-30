<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLeadStatusTable extends Migration
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
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => 250,
                'null' => true,
                'collation' => 'utf8_general_ci',
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
                'collation' => 'utf8_general_ci',
            ],
            'order' => [
                'type' => 'FLOAT',
                'null' => true,
                'default' => 0,
            ],
            'offset' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'null' => true,
                'default' => 0,
            ],
            'limit' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'null' => true,
                'default' => 50,
            ],
            'color' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
                'collation' => 'utf8_general_ci',
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('lead_status');
    }

    public function down()
    {
        $this->forge->dropTable('lead_status');
    }
}
