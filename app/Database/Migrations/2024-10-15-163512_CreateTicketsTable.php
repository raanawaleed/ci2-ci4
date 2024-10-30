<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTickets extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'from' => [
                'type' => 'VARCHAR',
                'constraint' => 250,
                'null' => true,
                'default' => '0',
                'collation' => 'utf8_general_ci',
            ],
            'reference' => [
                'type' => 'VARCHAR',
                'constraint' => 250,
                'null' => true,
                'default' => '0',
                'collation' => 'utf8_general_ci',
            ],
            'type_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'default' => 1,
            ],
            'etat_id' => [
                'type' => 'SMALLINT',
                'constraint' => 6,
                'null' => true,
            ],
            'lock' => [
                'type' => 'SMALLINT',
                'constraint' => 6,
                'null' => true,
                'default' => 0,
            ],
            'subject' => [
                'type' => 'VARCHAR',
                'constraint' => 250,
                'null' => true,
                'default' => '0',
                'collation' => 'utf8_general_ci',
            ],
            'text' => [
                'type' => 'TEXT',
                'null' => true,
                'collation' => 'utf8_general_ci',
            ],
            'status' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'default' => '0',
                'collation' => 'utf8_general_ci',
            ],
            'company_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'user_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'escalation_time' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
                'default' => 0,
            ],
            'priority' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'default' => '0',
                'collation' => 'utf8_general_ci',
            ],
            'created' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
                'default' => 0,
            ],
            'updated' => [
                'type' => 'TINYINT',
                'constraint' => 4,
                'null' => true,
                'default' => 0,
            ],
            'project_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'sub_project_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'collaborater_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
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
            'surface' => [
                'type' => 'FLOAT',
                'null' => true,
            ],
            'longueur' => [
                'type' => 'FLOAT',
                'null' => true,
            ],
            'new_created' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => false,
                'default' => 0,
            ],
            'deleted' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'null' => false,
                'default' => 0,
            ],
            'closed' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'null' => true,
                'default' => 0,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('project_id', 'projects', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('sub_project_id', 'project_has_sub_projects', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('collaborater_id', 'project_has_workers', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('type_id', 'ref_type', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('company_id', 'companies', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');

        $this->forge->createTable('tickets');
    }

    public function down()
    {
        $this->forge->dropTable('tickets');
    }
}
