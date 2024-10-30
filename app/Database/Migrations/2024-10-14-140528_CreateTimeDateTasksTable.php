<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTimeDateTasksTable extends Migration
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
            'id_task' => [
                'type' => 'INT',
                'unsigned' => true,
                'null' => false,
            ],
            'time_task' => [
                'type' => 'INT',
                'null' => false,
            ],
            'date_task' => [
                'type' => 'DATE',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('time_date_tasks');
    }

    public function down()
    {
        $this->forge->dropTable('time_date_tasks');
    }
}
