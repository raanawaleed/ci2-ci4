<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSubscriptionsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'reference' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'company_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'status' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'currency' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
            ],
            'issue_date' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
            ],
            'end_date' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
            ],
            'frequency' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
            ],
            'next_payment' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
            ],
            'terms' => [
                'type' => 'MEDIUMTEXT',
                'null' => true,
            ],
            'discount' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'default' => '0',
                'null' => true,
            ],
            'subscribed' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'default' => '0',
                'null' => true,
            ],
            'second_tax' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'subscription_num' => [
                'type' => 'VARCHAR',
                'constraint' => 250,
                'null' => false,
            ],
            'id_vcompanies' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => false,
            ],
            'creation_date' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('company_id', 'companies', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_vcompanies', 'vcompanies', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('subscriptions');
    }

    public function down()
    {
        $this->forge->dropTable('subscriptions');
    }
}
