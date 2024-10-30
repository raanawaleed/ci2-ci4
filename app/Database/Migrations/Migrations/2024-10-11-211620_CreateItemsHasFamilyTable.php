<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateItemsHasFamilyTable extends Migration
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
            'libelle' => [
                'type' => 'VARCHAR',
                'constraint' => 250,
                'null' => false,
            ],
            'parent' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
                'unsigned' => true,
            ],
            'inactive' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
            'id_vcompanies' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('parent', 'items_has_family', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('id_vcompanies', 'vcompanies', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('items_has_family');
    }

    public function down()
    {
        $this->forge->dropTable('items_has_family');
    }
}
