<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateFacture extends Migration
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
            'avoir_date' => [
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
            'deduction' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => false,
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
                'type' => 'VARCHAR',
                'constraint' => 250,
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
                'type' => 'VARCHAR',
                'constraint' => 250,
                'null' => true,
                'collation' => 'utf8_general_ci',
            ],
            'outstanding' => [
                'type' => 'VARCHAR',
                'constraint' => 250,
                'default' => '0',
                'null' => true,
            ],
            'timbre_fiscal' => [
                'type' => 'FLOAT',
                'null' => true,
            ],
            'estimate_num' => [
                'type' => 'VARCHAR',
                'constraint' => 200,
                'null' => true,
                'collation' => 'utf8_general_ci',
            ],
            'sumht' => [
                'type' => 'VARCHAR',
                'constraint' => 250,
                'null' => true,
                'collation' => 'utf8_general_ci',
            ],
            'project_name' => [
                'type' => 'TEXT',
                'null' => false,
                'collation' => 'utf8_general_ci',
            ],
            'project_surface' => [
                'type' => 'FLOAT',
                'null' => false,
            ],
            'calcul_heure' => [
                'type' => 'FLOAT',
                'null' => false,
            ],
            'delivery' => [
                'type' => 'TEXT',
                'null' => false,
                'collation' => 'utf8_general_ci',
            ],
            'chef_projet_client' => [
                'type' => 'TEXT',
                'null' => false,
                'collation' => 'utf8_general_ci',
            ],
            'chef_projet' => [
                'type' => 'TEXT',
                'null' => false,
                'collation' => 'utf8_general_ci',
            ],
            'unite' => [
                'type' => 'TEXT',
                'null' => false,
                'collation' => 'utf8_general_ci',
            ],
            'project_ref' => [
                'type' => 'VARCHAR',
                'constraint' => 250,
                'null' => true,
                'collation' => 'utf8_general_ci',
            ],
        ]);

        $this->forge->addKey('id', true); // Primary key
        $this->forge->addForeignKey('company_id', 'companies', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('project_id', 'projects', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('subscription_id', 'subscriptions', 'id', 'CASCADE', 'CASCADE');

        $this->forge->createTable('facture');
    }

    public function down()
    {
        $this->forge->dropTable('facture');
    }
}
