<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateItemUnitsTable extends Migration
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
            'description' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => false,
            ],
            'value' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('item_units');
    }

    public function down()
    {
        $this->forge->dropTable('item_units');
    }
}
