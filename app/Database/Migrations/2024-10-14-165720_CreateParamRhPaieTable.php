<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateParamRhPaieTable extends Migration
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
            'taux_cnss' => [
                'type' => 'FLOAT',
                'null' => true,
                'default' => 9.18,
            ],
            'prime_marie' => [
                'type' => 'FLOAT',
                'null' => true,
                'default' => 150,
            ],
            'prime_zero_enfant' => [
                'type' => 'FLOAT',
                'null' => true,
                'default' => 0,
            ],
            'prime_un_enfant' => [
                'type' => 'FLOAT',
                'null' => true,
                'default' => 90,
            ],
            'prime_deux_enfant' => [
                'type' => 'FLOAT',
                'null' => true,
                'default' => 165,
            ],
            'prime_troix_enfant' => [
                'type' => 'FLOAT',
                'null' => true,
                'default' => 225,
            ],
            'prime_quatre_plus_enfant' => [
                'type' => 'FLOAT',
                'null' => true,
                'default' => 270,
            ],
            'revenu_annuel1' => [
                'type' => 'FLOAT',
                'null' => true,
                'default' => 0,
            ],
            'revenu_annuel2' => [
                'type' => 'FLOAT',
                'null' => true,
                'default' => 525,
            ],
            'revenu_annuel3' => [
                'type' => 'FLOAT',
                'null' => true,
                'default' => 1000,
            ],
            'revenu_annuel4' => [
                'type' => 'FLOAT',
                'null' => true,
                'default' => 2500,
            ],
            'revenu_annuel5' => [
                'type' => 'FLOAT',
                'null' => true,
                'default' => 9000,
            ],
            'impot1' => [
                'type' => 'FLOAT',
                'null' => true,
                'default' => 20,
            ],
            'impot2' => [
                'type' => 'FLOAT',
                'null' => true,
                'default' => 25,
            ],
            'impot3' => [
                'type' => 'FLOAT',
                'null' => true,
                'default' => 30,
            ],
            'impot4' => [
                'type' => 'FLOAT',
                'null' => true,
                'default' => 35,
            ],
            'taux1' => [
                'type' => 'FLOAT',
                'null' => true,
                'default' => 25.25,
            ],
            'taux2' => [
                'type' => 'FLOAT',
                'null' => true,
                'default' => 0.5,
            ],
            'droit_nombre_jour_conge' => [
                'type' => 'FLOAT',
                'null' => true,
                'default' => 1.75,
            ],
            'nombre_moins' => [
                'type' => 'FLOAT',
                'null' => true,
                'default' => 12,
            ],
            'enfant_handicape' => [
                'type' => 'FLOAT',
                'null' => true,
                'default' => 100,
            ],
            'enfant_boursier_25' => [
                'type' => 'FLOAT',
                'null' => true,
                'default' => 100,
            ],
            'parents_a_charges' => [
                'type' => 'FLOAT',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('param_rh_paie');
    }

    public function down()
    {
        $this->forge->dropTable('param_rh_paie');
    }
}
