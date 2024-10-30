<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateProjectHasFiles extends Migration
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
            'user_id' => [
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
            'type' => [
                'type' => 'VARCHAR',
                'constraint' => 80,
                'default' => '0',
                'null' => true,
                'collation' => 'utf8_general_ci',
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => 120,
                'default' => '0',
                'null' => true,
                'collation' => 'utf8_general_ci',
            ],
            'filename' => [
                'type' => 'VARCHAR',
                'constraint' => 150,
                'default' => '0',
                'null' => true,
                'collation' => 'utf8_general_ci',
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
                'collation' => 'utf8_general_ci',
            ],
            'savename' => [
                'type' => 'VARCHAR',
                'constraint' => 200,
                'default' => '0',
                'null' => true,
                'collation' => 'utf8_general_ci',
            ],
            'phase' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'default' => '0',
                'null' => true,
                'collation' => 'utf8_general_ci',
            ],
            'date' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'default' => '0',
                'null' => true,
                'collation' => 'utf8_general_ci',
            ],
            'download_counter' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('project_id', 'projects', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('client_id', 'clients', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('project_has_files');
    }

    public function down()
    {
        $this->forge->dropTable('project_has_files');
    }
}
