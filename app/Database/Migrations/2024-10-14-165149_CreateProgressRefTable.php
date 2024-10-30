<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateProgressRefTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'ref' => [
                'type' => 'DECIMAL',
                'constraint' => '3,0',
                'unsigned' => true,
                'auto_increment' => false,
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'collation' => 'latin1_swedish_ci',
            ],
        ]);

        $this->forge->addKey('ref', true);
        $this->forge->createTable('progress_ref');
    }

    public function down()
    {
        $this->forge->dropTable('progress_ref');
    }
}
