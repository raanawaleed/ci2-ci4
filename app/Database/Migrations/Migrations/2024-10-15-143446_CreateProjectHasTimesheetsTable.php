<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateProjectHasTimesheets extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'BIGINT',
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
            'user_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'time' => [
                'type' => 'VARCHAR',
                'constraint' => 250,
                'default' => '0',
                'null' => true,
                'collation' => 'utf8_general_ci',
            ],
            'task_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'client_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'start' => [
                'type' => 'VARCHAR',
                'constraint' => 250,
                'default' => '0',
                'null' => true,
                'collation' => 'utf8_general_ci',
            ],
            'end' => [
                'type' => 'VARCHAR',
                'constraint' => 250,
                'default' => '0',
                'null' => true,
                'collation' => 'utf8_general_ci',
            ],
            'invoice_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
                'collation' => 'utf8_general_ci',
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('project_id', 'projects', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('task_id', 'project_has_tasks', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('client_id', 'clients', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('invoice_id', 'invoices', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('project_has_timesheets');
    }

    public function down()
    {
        $this->forge->dropTable('project_has_timesheets');
    }
}
