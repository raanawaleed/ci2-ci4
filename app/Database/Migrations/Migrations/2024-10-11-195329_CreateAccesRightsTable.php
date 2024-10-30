<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAccesRightsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => true,
            ],
            'menu' => [
                'type' => 'VARCHAR',
                'constraint' => 250,
                'null' => true,
            ],
            'submenu' => [
                'type' => 'VARCHAR',
                'constraint' => 250,
                'null' => true,
            ],
            'user_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'company_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('company_id', 'companies', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('accesrights');
    }

    public function down()
    {

        $this->forge->dropTable('accesrights');
    }
}
