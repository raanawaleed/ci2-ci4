<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePlanification extends Migration
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
                'null' => false,
            ],
            'date' => [
                'type' => 'DATE',
                'null' => false,
            ],
            'heures_pointees' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => false,
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
            'autre_saisie' => [
                'type' => 'VARCHAR',
                'constraint' => 250,
                'null' => true,
                'collation' => 'latin1_swedish_ci',
            ],
            'type_ticket' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => false,
            ],
        ]);

        $this->forge->addKey('id', true);
        // $this->forge->addForeignKey('utilisateur_id', 'projects', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('ticket_id', 'tickets', 'id', 'CASCADE', 'CASCADE');
        // $this->forge->addForeignKey('project_id', 'projects', 'id', 'CASCADE', 'CASCADE');

        $this->forge->createTable('planification');
    }

    public function down()
    {
        $this->forge->dropTable('planification');
    }
}
