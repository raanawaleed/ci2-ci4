<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePrimeAffecterTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_prime' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'annee' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
            'moins' => [
                'type' => 'VARCHAR',
                'constraint' => 250,
                'null' => true,
                'collation' => 'latin1_swedish_ci',
            ],
            'id_salary' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id_prime', true);
        $this->forge->addForeignKey('id_salary', 'salaries', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('prime_affecter');
    }

    public function down()
    {
        $this->forge->dropTable('prime_affecter');
    }
}
