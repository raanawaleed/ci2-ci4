<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCommande extends Migration
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
            'reference' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'company_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => false,
                'unsigned' => true,
            ],
            'status' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'collation' => 'utf8_general_ci',
            ],
            'currency' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
                'collation' => 'utf8_general_ci',
            ],
            'issue_date' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
                'collation' => 'utf8_general_ci',
            ],
            'due_date' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
                'collation' => 'utf8_general_ci',
            ],
            'sent_date' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
                'collation' => 'utf8_general_ci',
            ],
            'paid_date' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
                'collation' => 'utf8_general_ci',
            ],
            'terms' => [
                'type' => 'MEDIUMTEXT',
                'null' => true,
                'collation' => 'utf8_general_ci',
            ],
            'discount' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'default' => '0',
                'null' => true,
            ],
            'subscription_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'project_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'tax' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'collation' => 'utf8_general_ci',
            ],
            'estimate' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
                'null' => true,
            ],
            'estimate_status' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'default' => '0',
                'null' => true,
            ],
            'estimate_accepted_date' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'default' => '0',
                'null' => true,
            ],
            'estimate_sent' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'default' => '0',
                'null' => true,
            ],
            'sum' => [
                'type' => 'FLOAT',
                'default' => '0',
                'null' => true,
            ],
            'second_tax' => [
                'type' => 'VARCHAR',
                'constraint' => 5,
                'null' => true,
                'collation' => 'utf8_general_ci',
            ],
            'estimate_reference' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'collation' => 'utf8_general_ci',
            ],
            'paid' => [
                'type' => 'FLOAT',
                'null' => true,
            ],
            'outstanding' => [
                'type' => 'FLOAT',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true); // Primary key
        $this->forge->addForeignKey('company_id', 'companies', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('subscription_id', 'subscriptions', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('project_id', 'projects', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('commande');
    }

    public function down()
    {
        $this->forge->dropTable('commande');
    }
}
