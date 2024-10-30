<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSaisieTemps extends Migration
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
            'ticket_id' => [
                'type' => 'BIGINT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => false,
            ],
            'utilisateur_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => false,
            ],
            'date' => [
                'type' => 'DATE',
                'null' => false,
            ],
            'heures_pointees' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => false,
                'collation' => 'latin1_swedish_ci',
            ],
            'created_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'validation' => [
                'type' => 'TINYINT',
                'constraint' => 4,
                'null' => true,
                'default' => 0,
            ],
            'autre_saisie' => [
                'type' => 'VARCHAR',
                'constraint' => 250,
                'null' => true,
                'collation' => 'latin1_swedish_ci',
            ],
            'type_ticket' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
            'color' => [
                'type' => 'TEXT',
                'collation' => 'latin1_swedish_ci',
                'null' => false,
            ],
            'bdate' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'collation' => 'latin1_swedish_ci',
                'null' => false,
            ],
            'des' => [
                'type' => 'TEXT',
                'collation' => 'latin1_swedish_ci',
                'null' => false,
            ],
            'rdate' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('ticket_id', 'tickets', 'id', 'CASCADE', 'CASCADE');
        // $this->forge->addForeignKey('utilisateur_id', 'tickets', 'id', 'CASCADE', 'CASCADE');

        $this->forge->createTable('saisie_temps');
    }

    public function down()
    {
        $this->forge->dropTable('saisie_temps');
    }
}
