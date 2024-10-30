<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateModulesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => 250,
                'null' => true,
                'collation' => 'utf8_general_ci',
            ],
            'link' => [
                'type' => 'VARCHAR',
                'constraint' => 250,
                'null' => true,
                'collation' => 'utf8_general_ci',
            ],
            'type' => [
                'type' => 'VARCHAR',
                'constraint' => 250,
                'null' => true,
                'collation' => 'utf8_general_ci',
            ],
            'icon' => [
                'type' => 'VARCHAR',
                'constraint' => 150,
                'null' => true,
                'collation' => 'utf8_general_ci',
            ],
            'sort' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
            'actif' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
            'default_module' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'null' => true,
                'default' => 0,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('modules');
    }

    public function down()
    {
        $this->forge->dropTable('modules');
    }
}
