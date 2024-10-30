<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePaiesTable extends Migration
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
            'chefdefamille' => [
                'type' => 'VARCHAR',
                'constraint' => 250,
                'null' => true,
                'collation' => 'latin1_swedish_ci',
            ],
            'salaire_base' => [
                'type' => 'DOUBLE',
                'null' => true,
            ],
            'nbr_enfant_handicapes' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
            'parnets_a_charges' => [
                'type' => 'VARCHAR',
                'constraint' => 250,
                'null' => true,
                'collation' => 'latin1_swedish_ci',
            ],
            'droit_conge' => [
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
            'paienonconge' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
            'mode_paiement' => [
                'type' => 'VARCHAR',
                'constraint' => 250,
                'null' => true,
                'collation' => 'latin1_swedish_ci',
            ],
            'dateembauche' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'echelon' => [
                'type' => 'VARCHAR',
                'constraint' => 250,
                'null' => true,
                'collation' => 'latin1_swedish_ci',
            ],
            'nbr_enfant' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
            'nbr_enfant_boursier' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
            'soldecongeinitial' => [
                'type' => 'DOUBLE',
                'null' => true,
            ],
            'typepaiment' => [
                'type' => 'VARCHAR',
                'constraint' => 250,
                'null' => true,
                'collation' => 'latin1_swedish_ci',
            ],
            'type_contart' => [
                'type' => 'VARCHAR',
                'constraint' => 250,
                'null' => true,
                'collation' => 'latin1_swedish_ci',
            ],
            'datedepart' => [
                'type' => 'VARCHAR',
                'constraint' => 250,
                'null' => true,
                'collation' => 'latin1_swedish_ci',
            ],
            'categorie' => [
                'type' => 'VARCHAR',
                'constraint' => 250,
                'null' => true,
                'collation' => 'latin1_swedish_ci',
            ],
            'id_company' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'nb_jour_presence' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
            'salaire_brut' => [
                'type' => 'DOUBLE',
                'null' => true,
            ],
            'cnss' => [
                'type' => 'VARCHAR',
                'constraint' => 250,
                'null' => true,
                'collation' => 'latin1_swedish_ci',
            ],
            'salaire_impossable' => [
                'type' => 'DOUBLE',
                'null' => true,
            ],
            'salaire_annuel' => [
                'type' => 'DOUBLE',
                'null' => true,
            ],
            'salaire_net' => [
                'type' => 'DOUBLE',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('id_salary', 'salaries', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_company', 'companies', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('paies');
    }

    public function down()
    {
        $this->forge->dropTable('paies');
    }
}
