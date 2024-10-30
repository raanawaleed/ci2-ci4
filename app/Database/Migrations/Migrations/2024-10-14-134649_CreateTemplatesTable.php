<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTemplatesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'unsigned' => true,
                'constraint' => 11,
                'auto_increment' => true,
            ],
            'type' => [
                'type' => 'VARCHAR',
                'constraint' => 250,
                'null' => true,
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => 250,
                'null' => true,
            ],
            'text' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'updated' => [
                'type' => 'TIMESTAMP',
                'null' => true,
                'default' => null,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('templates');
    }

    public function down()
    {
        $this->forge->dropTable('templates');
    }
}
