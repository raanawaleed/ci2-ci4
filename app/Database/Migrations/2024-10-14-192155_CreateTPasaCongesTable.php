<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTPasaConges extends Migration
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
            'date_debut' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'nbr_jour' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
            'motif' => [
                'type' => 'VARCHAR',
                'constraint' => 250,
                'null' => true,
            ],
            'statut' => [
                'type' => 'VARCHAR',
                'constraint' => 200,
                'null' => true,
            ],
            'date_fin' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true); // Primary key
        $this->forge->addForeignKey('id_salary', 'salaries', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('t_pasa_conges');
    }

    public function down()
    {
        $this->forge->dropTable('t_pasa_conges');
    }
}
