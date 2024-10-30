<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAvoirs extends Migration
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
                'null' => true,
            ],
            'company_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'subject' => [
                'type' => 'TEXT',
                'null' => true,
                'collation' => 'utf8_general_ci',
            ],
            'notes' => [
                'type' => 'TEXT',
                'null' => true,
                'collation' => 'utf8_general_ci',
            ],
            'status' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
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
            'creation_date' => [
                'type' => 'DATE',
                'null' => true,
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
                'null' => true,
                'default' => '0',
                'collation' => 'utf8_general_ci',
            ],
            'deduction' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => false,
                'default' => 0,
            ],
            'subscription_id' => [
                'type' => 'INT',
                'unsigned' => true,
            ],
            'project_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'project_ref' => [
                'type' => 'VARCHAR',
                'constraint' => 250,
                'null' => true,
                'collation' => 'utf8_general_ci',
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
                'null' => true,
                'default' => 0,
            ],
            'estimate_accepted_date' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'default' => '0',
                'collation' => 'utf8_general_ci',
            ],
            'estimate_sent' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'default' => '0',
                'collation' => 'utf8_general_ci',
            ],
            'sum' => [
                'type' => 'FLOAT',
                'null' => true,
                'default' => 0,
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
                'type' => 'VARCHAR',
                'constraint' => 250,
                'null' => true,
                'default' => '0',
                'collation' => 'utf8_general_ci',
            ],
            'estimate_num' => [
                'type' => 'VARCHAR',
                'constraint' => 200,
                'null' => true,
                'collation' => 'utf8_general_ci',
            ],
            'sumht' => [
                'type' => 'FLOAT',
                'null' => true,
            ],
            'avoir_num' => [
                'type' => 'VARCHAR',
                'constraint' => 250,
                'null' => true,
                'collation' => 'utf8_general_ci',
            ],
            'timbre_fiscal' => [
                'type' => 'VARCHAR',
                'constraint' => 250,
                'null' => true,
                'collation' => 'utf8_general_ci',
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('company_id', 'companies', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('subscription_id', 'subscriptions', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('project_id', 'projects', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('avoirs');
    }

    public function down()
    {
        $this->forge->dropTable('avoirs');
    }
}
