<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateHolidaysTable extends Migration
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
            'date' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => false,
            ],
            'reason' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => false,
            ],
            'bdate' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('tbl_holidays');
    }

    public function down()
    {
        $this->forge->dropTable('tbl_holidays');
    }
}
