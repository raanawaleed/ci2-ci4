<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSalariesTable extends Migration
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
            'code' => [
                'type' => 'VARCHAR',
                'constraint' => 25,
                'null' => true,
            ],
            'nom' => [
                'type' => 'VARCHAR',
                'constraint' => 30,
                'null' => true,
            ],
            'genre' => [
                'type' => 'VARCHAR',
                'constraint' => 11,
                'null' => true,
            ],
            'datedenaissance' => [
                'type' => 'VARCHAR',
                'constraint' => 250,
                'null' => true,
            ],
            'numerocnss' => [
                'type' => 'VARCHAR',
                'constraint' => 250,
                'null' => true,
            ],
            'prenom' => [
                'type' => 'VARCHAR',
                'constraint' => 30,
                'null' => true,
            ],
            'situationfamiliale' => [
                'type' => 'INT',
                'null' => true,
            ],
            'lieudenaissance' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'datedelivrance' => [
                'type' => 'VARCHAR',
                'constraint' => 250,
                'null' => true,
            ],
            'codetypesalari' => [
                'type' => 'INT',
                'null' => true,
            ],
            'adresse1' => [
                'type' => 'VARCHAR',
                'constraint' => 150,
                'null' => true,
            ],
            'adresse2' => [
                'type' => 'VARCHAR',
                'constraint' => 150,
                'null' => true,
            ],
            'codepostal' => [
                'type' => 'VARCHAR',
                'constraint' => 250,
                'null' => true,
            ],
            'ville' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'pays' => [
                'type' => 'VARCHAR',
                'constraint' => 250,
                'null' => true,
            ],
            'tel1' => [
                'type' => 'VARCHAR',
                'constraint' => 250,
                'null' => true,
            ],
            'tel2' => [
                'type' => 'VARCHAR',
                'constraint' => 250,
                'null' => true,
            ],
            'skype' => [
                'type' => 'VARCHAR',
                'constraint' => 200,
                'null' => true,
            ],
            'mail' => [
                'type' => 'VARCHAR',
                'constraint' => 200,
                'null' => true,
            ],
            'nombanque' => [
                'type' => 'VARCHAR',
                'constraint' => 150,
                'null' => true,
            ],
            'rib' => [
                'type' => 'VARCHAR',
                'constraint' => 250,
                'null' => true,
            ],
            'iban' => [
                'type' => 'VARCHAR',
                'constraint' => 250,
                'null' => true,
            ],
            'bic' => [
                'type' => 'VARCHAR',
                'constraint' => 250,
                'null' => true,
            ],
            'matricule' => [
                'type' => 'VARCHAR',
                'constraint' => 15,
                'null' => true,
            ],
            'type_contrat' => [
                'type' => 'INT',
                'null' => true,
            ],
            'date_debut_embauche' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'date_fin_embauche' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'etat' => [
                'type' => 'VARCHAR',
                'constraint' => 250,
                'null' => true,
                'default' => '1',
            ],
            'numerocin' => [
                'type' => 'VARCHAR',
                'constraint' => 250,
                'null' => true,
            ],
            'idfonction' => [
                'type' => 'VARCHAR',
                'constraint' => 250,
                'null' => true,
            ],
            'nb_enfants' => [
                'type' => 'INT',
                'null' => true,
            ],
            'nb_enfants_handicape' => [
                'type' => 'INT',
                'null' => true,
            ],
            'nb_enfants_boursiers' => [
                'type' => 'INT',
                'null' => true,
            ],
            'chef_famille' => [
                'type' => 'TINYINT',
                'null' => true,
            ],
            'salaire_brut' => [
                'type' => 'DOUBLE',
                'null' => true,
            ],
            'parents_charges' => [
                'type' => 'TINYINT',
                'null' => true,
            ],
            'categorie' => [
                'type' => 'INT',
                'null' => true,
            ],
            'echelon' => [
                'type' => 'INT',
                'null' => true,
            ],
            'mode_paiement' => [
                'type' => 'INT',
                'null' => true,
            ],
            'droit_conge' => [
                'type' => 'DOUBLE',
                'null' => true,
            ],
            'type_paiement' => [
                'type' => 'INT',
                'null' => true,
            ],
            'tauxhoraire' => [
                'type' => 'DOUBLE',
                'null' => true,
            ],
            'solde_conge_initiale' => [
                'type' => 'DOUBLE',
                'null' => true,
            ],
            'date_depart' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'seraffectation' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'file' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['active', 'inactive'],
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('salaries');
    }

    public function down()
    {
        $this->forge->dropTable('salaries');
    }
}
