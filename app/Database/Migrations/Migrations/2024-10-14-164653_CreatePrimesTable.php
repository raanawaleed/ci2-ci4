<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePrimesTable extends Migration
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
            'description' => [
                'type' => 'VARCHAR',
                'constraint' => 200,
                'null' => true,
                'collation' => 'latin1_swedish_ci',
            ],
            'valeur' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
            'cotisable' => [
                'type' => 'VARCHAR',
                'constraint' => 200,
                'null' => true,
                'collation' => 'latin1_swedish_ci',
            ],
            'Imposable' => [
                'type' => 'VARCHAR',
                'constraint' => 200,
                'null' => true,
                'collation' => 'latin1_swedish_ci',
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('primes');
    }

    public function down()
    {
        $this->forge->dropTable('primes');
    }
}
