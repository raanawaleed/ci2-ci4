<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateClientsTable extends Migration
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
            'company_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'firstname' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'lastname' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'email' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'phone' => [
                'type' => 'VARCHAR',
                'constraint' => 15,
            ],
            'mobile' => [
                'type' => 'VARCHAR',
                'constraint' => 15,
            ],
            'address' => [
                'type' => 'TEXT',
            ],
            'zipcode' => [
                'type' => 'VARCHAR',
                'constraint' => 10,
            ],
            'userpic' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'city' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'hashed_password' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'inactive' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
            'access' => [
                'type' => 'INT',
                'constraint' => 11,
            ],
            'last_active' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'last_login' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'timbre_fiscal' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('company_id', 'companies', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('clients');
    }

    public function down()
    {
        $this->forge->dropTable('clients');
    }
}
