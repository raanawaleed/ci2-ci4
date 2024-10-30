<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateVersionTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => true,
            ],
            'db_version' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
            'revision' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
            'last_update' => [
                'type' => 'DATE',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('version');
    }

    public function down()
    {
        $this->forge->dropTable('version');
    }
}
