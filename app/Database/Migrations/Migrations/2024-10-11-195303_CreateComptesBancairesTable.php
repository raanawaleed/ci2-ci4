<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateComptesBancairesTable extends Migration
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
            'RIB' => [
                'type' => 'VARCHAR',
                'constraint' => 34,
                'null' => true,
            ],
            'nom' => [
                'type' => 'VARCHAR',
                'constraint' => 250,
                'null' => true,
            ],
            'BIC' => [
                'type' => 'VARCHAR',
                'constraint' => 11,
                'null' => true,
            ],
            'IBAN' => [
                'type' => 'VARCHAR',
                'constraint' => 34,
                'null' => true,
            ],
            'adr_tit_com' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'nom_tit_com' => [
                'type' => 'VARCHAR',
                'constraint' => 250,
                'null' => true,
            ],
            'derni_cheque' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
            ],
            'contact1' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'contact2' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'adr_banque' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'code_pl' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
            ],
            'num_emetteur' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
            ],
            'date_cloture' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'created_date' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'created_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
                'unsigned' => true,
            ],
            'update_date' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
                'unsigned' => true,
            ],
            'visible' => [
                'type' => 'BOOLEAN',
                'default' => true,
            ],
            'company_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('company_id', 'companies', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('comptesbancaires');
    }

    public function down()
    {
        $this->forge->dropTable('comptesbancaires');
    }
}
