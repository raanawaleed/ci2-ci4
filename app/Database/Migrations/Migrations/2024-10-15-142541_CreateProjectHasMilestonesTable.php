<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateProjectHasMilestones extends Migration
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
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'collation' => 'utf8_general_ci',
            ],
            'description' => [
                'type' => 'LONGTEXT',
                'null' => true,
                'collation' => 'utf8_general_ci',
            ],
            'due_date' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'collation' => 'utf8_general_ci',
            ],
            'orderindex' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
            'start_date' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'collation' => 'utf8_general_ci',
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('project_id', 'projects', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('project_has_milestones');
    }

    public function down()
    {
        $this->forge->dropTable('project_has_milestones');
    }
}
