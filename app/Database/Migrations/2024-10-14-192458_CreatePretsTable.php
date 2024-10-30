<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePretsTable extends Migration
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
            'id_salary' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'type_pret' => [
                'type' => 'VARCHAR',
                'constraint' => 250,
                'null' => true,
                'collation' => 'latin1_swedish_ci',
            ],
            'remboursement' => [
                'type' => 'VARCHAR',
                'constraint' => 250,
                'null' => true,
                'collation' => 'latin1_swedish_ci',
            ],
            'date_pret' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'duree' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
            'montant' => [
                'type' => 'DOUBLE',
                'null' => true,
            ],
            'montant_remb' => [
                'type' => 'DOUBLE',
                'null' => true,
            ],
            'date_debut_remboursement' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'interet' => [
                'type' => 'DOUBLE',
                'null' => true,
            ],
            'montant_remboursement_moins' => [
                'type' => 'DOUBLE',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('id_salary', 'salaries', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('prets');
    }

    public function down()
    {
        $this->forge->dropTable('prets');
    }
}
