<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePrivateMessagesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 10,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'status' => [
                'type' => 'VARCHAR',
                'constraint' => 150,
                'null' => false,
                'collation' => 'utf8_general_ci',
            ],
            'sender' => [
                'type' => 'VARCHAR',
                'constraint' => 250,
                'null' => false,
                'collation' => 'utf8_general_ci',
            ],
            'recipient' => [
                'type' => 'VARCHAR',
                'constraint' => 250,
                'null' => false,
                'collation' => 'utf8_general_ci',
            ],
            'subject' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'collation' => 'utf8_general_ci',
            ],
            'message' => [
                'type' => 'TEXT',
                'null' => true,
                'collation' => 'utf8_general_ci',
            ],
            'time' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => false,
                'collation' => 'utf8_general_ci',
            ],
            'conversation' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'collation' => 'utf8_general_ci',
            ],
            'deleted' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
                'default' => 0,
            ],
            'attachment' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'collation' => 'utf8_general_ci',
            ],
            'attachment_link' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'collation' => 'utf8_general_ci',
            ],
            'receiver_delete' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
                'default' => 0,
            ],
            'new_created' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
                'default' => 0,
            ],
            'company_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('company_id', 'companies', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('privatemessages');
    }

    public function down()
    {
        $this->forge->dropTable('privatemessages');
    }
}
