<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateModulesSousTable extends Migration
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
            'id_modules' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => false,
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => 250,
                'null' => false,
                'collation' => 'latin1_swedish_ci',
            ],
            'link' => [
                'type' => 'VARCHAR',
                'constraint' => 250,
                'null' => false,
                'collation' => 'latin1_swedish_ci',
            ],
            'type' => [
                'type' => 'VARCHAR',
                'constraint' => 250,
                'null' => false,
                'collation' => 'latin1_swedish_ci',
            ],
            'icon' => [
                'type' => 'VARCHAR',
                'constraint' => 250,
                'null' => false,
                'collation' => 'latin1_swedish_ci',
            ],
            'sort' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => false,
            ],
            'actif' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('id_modules', 'modules', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('modules_sous');
    }

    public function down()
    {
        $this->forge->dropTable('modules_sous');
    }
}
