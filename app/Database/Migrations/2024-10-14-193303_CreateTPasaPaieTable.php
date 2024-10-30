<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTPasaPaie extends Migration
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
                'null' => false,
            ],
            'Paie_du' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
            'Paie_au' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
            'Ref_fiche' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'nb_jour_absence' => [
                'type' => 'DOUBLE',
                'null' => true,
            ],
            'nb_jour_presence' => [
                'type' => 'DOUBLE',
                'null' => false,
            ],
            'avance' => [
                'type' => 'DOUBLE',
                'null' => true,
            ],
            'salaire_base' => [
                'type' => 'DOUBLE',
                'null' => true,
            ],
            'salaire_brut' => [
                'type' => 'DOUBLE',
                'null' => true,
            ],
            'cotisation_cnss' => [
                'type' => 'DOUBLE',
                'null' => true,
            ],
            'salaire_imposable' => [
                'type' => 'DOUBLE',
                'null' => true,
            ],
            'base_calcul_irpp' => [
                'type' => 'DOUBLE',
                'null' => true,
            ],
            'impot_revenue' => [
                'type' => 'DOUBLE',
                'null' => true,
            ],
            'salaire_net' => [
                'type' => 'DOUBLE',
                'null' => true,
            ],
            'deduction_marie' => [
                'type' => 'DOUBLE',
                'null' => true,
            ],
            'deduction_nb_enfants' => [
                'type' => 'DOUBLE',
                'null' => true,
            ],
            'revenue_annuel' => [
                'type' => 'DOUBLE',
                'null' => true,
            ],
            'created_date' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
            'created_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => false,
            ],
            'update_date' => [
                'type' => 'TIMESTAMP',
                'null' => true,
            ],
            'updated_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
            'nb_cng_rst' => [
                'type' => 'DOUBLE',
                'null' => true,
            ],
            'redevance' => [
                'type' => 'DOUBLE',
                'null' => true,
            ],
            'mnt_remb' => [
                'type' => 'DOUBLE',
                'null' => true,
            ],
            'css' => [
                'type' => 'DOUBLE',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true); // Primary key
        $this->forge->addForeignKey('id_salary', 'salaries', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('tpasa_paie');
    }

    public function down()
    {
        $this->forge->dropTable('tpasa_paie');
    }
}
