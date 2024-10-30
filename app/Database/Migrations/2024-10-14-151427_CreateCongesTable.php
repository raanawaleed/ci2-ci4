<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCongesTable extends Migration
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
            'id_salarie' => [
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
                'null' => true,
            ],
            'motif' => [
                'type' => 'VARCHAR',
                'constraint' => 250,
                'null' => true,
            ],
            'document' => [
                'type' => 'VARCHAR',
                'constraint' => 250,
                'null' => true,
            ],
            'statut' => [
                'type' => 'VARCHAR',
                'constraint' => 200,
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('id_salarie', 'salaries', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('conges');
    }

    public function down()
    {
        $this->forge->dropTable('conges');
    }
}
