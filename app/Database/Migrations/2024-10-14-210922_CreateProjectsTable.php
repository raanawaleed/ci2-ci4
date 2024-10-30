<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateProjects extends Migration
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
            'reference' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => 250,
                'null' => true,
                'collation' => 'utf8_general_ci',
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
                'collation' => 'utf8_general_ci',
            ],
            'start' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
                'collation' => 'utf8_general_ci',
            ],
            'end' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
                'collation' => 'utf8_general_ci',
            ],
            'delivery' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
                'collation' => 'utf8_general_ci',
            ],
            'progress' => [
                'type' => 'DECIMAL',
                'constraint' => '3,0',
                'null' => true,
            ],
            'tracking' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
            'time_spent' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
            'datetime' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
            'sticky' => [
                'type' => 'ENUM',
                'constraint' => ['1', '0'],
                'default' => '0',
                'null' => true,
            ],
            'company_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'note' => [
                'type' => 'LONGTEXT',
                'null' => true,
                'collation' => 'utf8_general_ci',
            ],
            'progress_calc' => [
                'type' => 'TINYINT',
                'constraint' => 4,
                'default' => 0,
                'null' => true,
            ],
            'hide_tasks' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
                'null' => true,
            ],
            'enable_client_tasks' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
                'null' => true,
            ],
            'project_num' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
                'collation' => 'utf8_general_ci',
            ],
            'creation_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'type_projet' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
                'null' => false,
            ],
            'nature_projet' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
                'null' => false,
            ],
            'ref_projet' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'collation' => 'utf8_general_ci',
            ],
            'etat_projet' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
            'chef_projet_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'chef_projet_client_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'sub_client_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'surface' => [
                'type' => 'FLOAT',
                'null' => true,
            ],
            'longueur' => [
                'type' => 'FLOAT',
                'null' => true,
            ],
            'date_relance_1' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
                'collation' => 'utf8_general_ci',
            ],
            'date_relance_2' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
                'collation' => 'utf8_general_ci',
            ],
            'date_relance_3' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
                'collation' => 'utf8_general_ci',
            ],
        ]);

        $this->forge->addKey('id', true); // Primary key
        $this->forge->addForeignKey('company_id', 'companies', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('projects');
    }

    public function down()
    {
        $this->forge->dropTable('projects');
    }
}
