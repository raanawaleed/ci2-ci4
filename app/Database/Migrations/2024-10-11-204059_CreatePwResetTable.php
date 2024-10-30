<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePwResetTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => true,
                'unsigned' => true,
            ],
            'email' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => false,
            ],
            'timestamp' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
            'token' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => false,
            ],
            'user' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => false,
            ],
            'expiration' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('user', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('pw_reset');
    }

    public function down()
    {
        $this->forge->dropTable('pw_reset');
    }
}
