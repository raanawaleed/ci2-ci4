<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateFactureHasPayments extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'BIGINT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'facture_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,

            ],
            'amount' => [
                'type' => 'VARCHAR',
                'constraint' => 250,
                'null' => true,
                'collation' => 'utf8_general_ci',
            ],
            'date' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
                'collation' => 'utf8_general_ci',
            ],
            'type' => [
                'type' => 'VARCHAR',
                'constraint' => 250,
                'null' => true,
                'collation' => 'utf8_general_ci',
            ],
            'notes' => [
                'type' => 'TEXT',
                'null' => true,
                'collation' => 'utf8_general_ci',
            ],
            'num_cheque' => [
                'type' => 'VARCHAR',
                'constraint' => 250,
                'null' => true,
                'collation' => 'utf8_general_ci',
            ],
            'banque_cheque' => [
                'type' => 'VARCHAR',
                'constraint' => 250,
                'null' => true,
                'collation' => 'utf8_general_ci',
            ],
            'user_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'id_compteBancaire' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('facture_id', 'facture', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_compteBancaire', 'comptesbancaires', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('facture_has_payments');
    }

    public function down()
    {
        $this->forge->dropTable('facture_has_payments');
    }
}
