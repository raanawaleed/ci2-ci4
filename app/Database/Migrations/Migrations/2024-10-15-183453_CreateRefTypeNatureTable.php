<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateRefTypeNature extends Migration
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
            'id_category' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => false,
            ],
            'id_nature' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('ref_type_nature');
    }

    public function down()
    {
        $this->forge->dropTable('ref_type_nature');
    }
}
