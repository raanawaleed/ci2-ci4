<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateProjectHasTasks extends Migration
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
            'project_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => 250,
                'default' => '0',
                'null' => true,
                'collation' => 'utf8_general_ci',
            ],
            'sector' => [
                'type' => 'VARCHAR',
                'constraint' => 250,
                'null' => true,
                'collation' => 'utf8_general_ci',
            ],
            'amount' => [
                'type' => 'VARCHAR',
                'constraint' => 250,
                'null' => true,
                'collation' => 'utf8_general_ci',
            ],
            'user_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'intervenant_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'status' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'default' => '0',
                'null' => true,
                'collation' => 'utf8_general_ci',
            ],
            'public' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
                'null' => true,
            ],
            'datetime' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
            'due_date' => [
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
            'value' => [
                'type' => 'FLOAT',
                'default' => 0,
                'null' => false,
            ],
            'priority' => [
                'type' => 'SMALLINT',
                'constraint' => 6,
                'default' => 0,
                'null' => true,
            ],
            'time' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
                'null' => true,
            ],
            'client_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'created_by_client' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
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
            'milestone_id' => [
                'type' => 'BIGINT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'invoice_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'milestone_order' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
            'task_order' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
            'progress' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
                'null' => true,
            ],
            'created_at' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'collation' => 'utf8_general_ci',
            ],
            'start_date' => [
                'type' => 'VARCHAR',
                'constraint' => 250,
                'null' => true,
                'collation' => 'utf8_general_ci',
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('project_id', 'projects', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('intervenant_id', 'intervenants', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('client_id', 'clients', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('milestone_id', 'project_has_milestones', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('invoice_id', 'invoices', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('project_has_tasks');
    }

    public function down()
    {
        $this->forge->dropTable('project_has_tasks');
    }
}
