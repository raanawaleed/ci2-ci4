<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSmtpConfTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'ID' => [
                'type' => 'INT',
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'useragent' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'protocol' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'mailpath' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'smtp_host' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'smtp_user' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'smtp_pass' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'smtp_port' => [
                'type' => 'VARCHAR',
                'constraint' => 10,
                'null' => true,
            ],
            'smtp_timeout' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
            ],
            'smtp_crypto' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'smtp_debug' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'wordwrap' => [
                'type' => 'VARCHAR',
                'constraint' => 10,
                'null' => true,
            ],
            'wrapchars' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
            ],
            'mailtype' => [
                'type' => 'VARCHAR',
                'constraint' => 10,
                'null' => true,
            ],
            'charset' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
            ],
            'validate' => [
                'type' => 'VARCHAR',
                'constraint' => 10,
                'null' => true,
            ],
            'priority' => [
                'type' => 'VARCHAR',
                'constraint' => 3,
                'null' => true,
            ],
            'crlf' => [
                'type' => 'VARCHAR',
                'constraint' => 10,
                'null' => true,
            ],
            'newline' => [
                'type' => 'VARCHAR',
                'constraint' => 10,
                'null' => true,
            ],
            'bcc_batch_mode' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
            ],
            'bcc_batch_size' => [
                'type' => 'VARCHAR',
                'constraint' => 10,
                'null' => true,
            ],
            'company_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
        ]);

        $this->forge->addKey('ID', true); // Primary key
        $this->forge->addForeignKey('company_id', 'companies', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('smtp_conf');
    }

    public function down()
    {
        $this->forge->dropTable('smtp_conf');
    }
}
