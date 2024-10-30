<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateRefTypeOccurences extends Migration
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
            'id_type_occ' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'id_type' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'ordre' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
            'description' => [
                'type' => 'VARCHAR',
                'constraint' => 250,
                'null' => true,
            ],
            'created_date' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'update_date' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
            'visible' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
            'link' => [
                'type' => 'VARCHAR',
                'constraint' => 250,
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('id_type', 'ref_type', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('ref_type_occurences');
    }

    public function down()
    {
        $this->forge->dropTable('ref_type_occurences');
    }
}
