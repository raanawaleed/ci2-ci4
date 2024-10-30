<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSettingDocumentRhTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_setting_rh' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'logo_fiche_paie' => [
                'type' => 'INT',
                'null' => false,
            ],
            'logo_virement_salaire' => [
                'type' => 'INT',
                'null' => false,
            ],
            'logo_journal_paie' => [
                'type' => 'INT',
                'null' => false,
            ],
            'logo_doc_adminis' => [
                'type' => 'INT',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id_setting_rh', true); // Primary key
        $this->forge->createTable('setting_document_rh');
    }

    public function down()
    {
        $this->forge->dropTable('setting_document_rh');
    }
}
