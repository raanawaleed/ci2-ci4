<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateProjectHasActivities extends Migration
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
            'project_id' => [
                'type' => 'INT',
                'constraint' => 20,
                'unsigned' => true,
                'null' => true,
            ],
            'user_id' => [
                'type' => 'INT',
                'constraint' => 20,
                'unsigned' => true,
                'null' => true,
            ],
            'client_id' => [
                'type' => 'INT',
                'constraint' => 20,
                'unsigned' => true,
                'null' => true,
            ],
            'datetime' => [
                'type' => 'VARCHAR',
                'constraint' => 250,
                'default' => '0',
                'null' => true,
                'collation' => 'utf8_general_ci',
            ],
            'subject' => [
                'type' => 'VARCHAR',
                'constraint' => 250,
                'default' => '0',
                'null' => true,
                'collation' => 'utf8_general_ci',
            ],
            'message' => [
                'type' => 'LONGTEXT',
                'null' => true,
                'collation' => 'utf8_general_ci',
            ],
            'type' => [
                'type' => 'VARCHAR',
                'constraint' => 250,
                'null' => true,
                'collation' => 'utf8_general_ci',
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('project_id', 'projects', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('client_id', 'clients', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('project_has_activities');
    }

    public function down()
    {
        $this->forge->dropTable('project_has_activities');
    }
}
