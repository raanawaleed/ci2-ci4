<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateFactureHasItems extends Migration
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
            'facture_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'item_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'amount' => [
                'type' => 'CHAR',
                'constraint' => 11,
                'null' => true,
                'collation' => 'utf8_general_ci',
            ],
            'name' => [
                'type' => 'TEXT',
                'null' => true,
                'collation' => 'utf8_general_ci',
            ],
            'value' => [
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
            'type' => [
                'type' => 'VARCHAR',
                'constraint' => 250,
                'null' => true,
                'collation' => 'utf8_general_ci',
            ],
            'tva' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
            'unit' => [
                'type' => 'VARCHAR',
                'constraint' => 10,
                'null' => true,
                'collation' => 'utf8_general_ci',
            ],
            'discount' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
                'null' => true,
            ],
            'position' => [
                'type' => 'INT',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('facture_id', 'facture', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('item_id', 'items', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('facture_has_items');
    }

    public function down()
    {
        $this->forge->dropTable('facture_has_items');
    }
}
