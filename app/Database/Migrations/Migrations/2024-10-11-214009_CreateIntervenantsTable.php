<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateIntervenantsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => true,
                'unsigned' => true,
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => 250,
                'null' => true,
            ],
            'surname' => [
                'type' => 'VARCHAR',
                'constraint' => 250,
                'null' => true,
            ],
            'adress' => [
                'type' => 'VARCHAR',
                'constraint' => 250,
                'null' => true,
            ],
            'email' => [
                'type' => 'VARCHAR',
                'constraint' => 250,
                'null' => true,
            ],
            'value' => [
                'type' => 'VARCHAR',
                'constraint' => 250,
                'null' => true,
            ],
            'visible' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 1,
            ],
            'id_vcompanies' => [
                'type' => 'INT',
                'constraint' => 10,
                'unsigned' => true,
                'null' => false,
            ],
            'admin' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
                'default' => 0,
            ],
            'userpic' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('id_vcompanies', 'vcompanies', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('intervenants');
    }

    public function down()
    {
        $this->forge->dropTable('intervenants');
    }
}
