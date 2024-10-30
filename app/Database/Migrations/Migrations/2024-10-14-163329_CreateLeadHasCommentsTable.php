<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLeadHasCommentsTable extends Migration
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
            'attachment' => [
                'type' => 'VARCHAR',
                'constraint' => 250,
                'null' => true,
                'collation' => 'utf8_general_ci',
            ],
            'attachment_link' => [
                'type' => 'VARCHAR',
                'constraint' => 250,
                'null' => true,
                'collation' => 'utf8_general_ci',
            ],
            'datetime' => [
                'type' => 'VARCHAR',
                'constraint' => 250,
                'null' => true,
                'collation' => 'utf8_general_ci',
            ],
            'message' => [
                'type' => 'TEXT',
                'null' => true,
                'collation' => 'utf8_general_ci',
            ],
            'user_id' => [
                'type' => 'INT',
                'constraint' => 20,
                'null' => true,
                'unsigned' => true,
                'default' => 0,
            ],
            'lead_id' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => true,
                'null' => false,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('lead_id', 'leads', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('lead_has_comments');
    }

    public function down()
    {
        $this->forge->dropTable('lead_has_comments');
    }
}
