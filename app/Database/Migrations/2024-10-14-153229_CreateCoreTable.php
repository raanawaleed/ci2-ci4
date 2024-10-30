<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCoreTable extends Migration
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
            'version' => [
                'type' => 'CHAR',
                'constraint' => 10,
                'default' => '0',
            ],
            'domain' => [
                'type' => 'VARCHAR',
                'constraint' => 65,
                'null' => true,
            ],
            'email' => [
                'type' => 'VARCHAR',
                'constraint' => 80,
                'null' => true,
            ],
            'company' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'tax' => [
                'type' => 'VARCHAR',
                'constraint' => 5,
                'null' => true,
            ],
            'currency' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
            ],
            'autobackup' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
            'cronjob' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
            'last_cronjob' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
            'last_autobackup' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
            'invoice_terms' => [
                'type' => 'MEDIUMTEXT',
                'null' => true,
            ],
            'company_reference' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
            'project_reference' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
            'invoice_reference' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
            'subscription_reference' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
            'ticket_reference' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
            'date_format' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
            ],
            'date_time_format' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
            ],
            'invoice_mail_subject' => [
                'type' => 'VARCHAR',
                'constraint' => 150,
                'null' => true,
            ],
            'pw_reset_mail_subject' => [
                'type' => 'VARCHAR',
                'constraint' => 150,
                'null' => true,
            ],
            'pw_reset_link_mail_subject' => [
                'type' => 'VARCHAR',
                'constraint' => 150,
                'null' => true,
            ],
            'credentials_mail_subject' => [
                'type' => 'VARCHAR',
                'constraint' => 150,
                'null' => true,
            ],
            'notification_mail_subject' => [
                'type' => 'VARCHAR',
                'constraint' => 150,
                'null' => true,
            ],
            'language' => [
                'type' => 'VARCHAR',
                'constraint' => 150,
                'null' => true,
            ],
            'invoice_address' => [
                'type' => 'VARCHAR',
                'constraint' => 200,
                'null' => true,
            ],
            'invoice_city' => [
                'type' => 'VARCHAR',
                'constraint' => 200,
                'null' => true,
            ],
            'invoice_contact' => [
                'type' => 'VARCHAR',
                'constraint' => 200,
                'null' => true,
            ],
            'invoice_tel' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'subscription_mail_subject' => [
                'type' => 'VARCHAR',
                'constraint' => 250,
                'null' => true,
            ],
            'logo' => [
                'type' => 'VARCHAR',
                'constraint' => 150,
                'null' => true,
            ],
            'template' => [
                'type' => 'VARCHAR',
                'constraint' => 200,
                'default' => 'blueline',
                'null' => true,
            ],
            'paypal' => [
                'type' => 'VARCHAR',
                'constraint' => 5,
                'default' => '1',
                'null' => true,
            ],
            'paypal_currency' => [
                'type' => 'VARCHAR',
                'constraint' => 200,
                'default' => 'EUR',
                'null' => true,
            ],
            'paypal_account' => [
                'type' => 'VARCHAR',
                'constraint' => 200,
                'null' => true,
            ],
            'invoice_logo' => [
                'type' => 'VARCHAR',
                'constraint' => 150,
                'default' => 'assets/blueline/img/invoice_logo.png',
                'null' => true,
            ],
            'pc' => [
                'type' => 'VARCHAR',
                'constraint' => 150,
                'null' => true,
            ],
            'vat' => [
                'type' => 'VARCHAR',
                'constraint' => 150,
                'null' => true,
            ],
            'ticket_email' => [
                'type' => 'VARCHAR',
                'constraint' => 250,
                'null' => true,
            ],
            'ticket_default_owner' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => '1',
                'null' => true,
            ],
            'ticket_default_queue' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => '1',
                'null' => true,
            ],
            'ticket_default_type' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => '1',
                'null' => true,
            ],
            'ticket_default_status' => [
                'type' => 'VARCHAR',
                'constraint' => 200,
                'default' => 'new',
                'null' => true,
            ],
            'ticket_config_host' => [
                'type' => 'VARCHAR',
                'constraint' => 250,
                'null' => true,
            ],
            'ticket_config_login' => [
                'type' => 'VARCHAR',
                'constraint' => 250,
                'null' => true,
            ],
            'ticket_config_pass' => [
                'type' => 'VARCHAR',
                'constraint' => 250,
                'null' => true,
            ],
            'ticket_config_port' => [
                'type' => 'VARCHAR',
                'constraint' => 250,
                'null' => true,
            ],
            'ticket_config_ssl' => [
                'type' => 'VARCHAR',
                'constraint' => 250,
                'null' => true,
            ],
            'ticket_config_email' => [
                'type' => 'VARCHAR',
                'constraint' => 250,
                'null' => true,
            ],
            'ticket_config_flags' => [
                'type' => 'VARCHAR',
                'constraint' => 250,
                'default' => '/notls',
                'null' => true,
            ],
            'ticket_config_search' => [
                'type' => 'VARCHAR',
                'constraint' => 250,
                'default' => 'UNSEEN',
                'null' => true,
            ],
            'ticket_config_timestamp' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => '0',
                'null' => true,
            ],
            'ticket_config_mailbox' => [
                'type' => 'VARCHAR',
                'constraint' => 250,
                'null' => true,
            ],
            'ticket_config_delete' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => '0',
                'null' => true,
            ],
            'ticket_config_active' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => '0',
                'null' => true,
            ],
            'ticket_config_imap' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => '1',
                'null' => true,
            ],
            'stripe' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => '0',
                'null' => true,
            ],
            'stripe_key' => [
                'type' => 'VARCHAR',
                'constraint' => 250,
                'null' => true,
            ],
            'stripe_p_key' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'stripe_currency' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'default' => 'USD',
                'null' => true,
            ],
            'bank_transfer' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => '0',
                'null' => true,
            ],
            'bank_transfer_text' => [
                'type' => 'LONGTEXT',
                'null' => true,
            ],
            'estimate_terms' => [
                'type' => 'LONGTEXT',
                'null' => true,
            ],
            'estimate_prefix' => [
                'type' => 'VARCHAR',
                'constraint' => 250,
                'default' => 'EST',
                'null' => true,
            ],
            'estimate_pdf_template' => [
                'type' => 'VARCHAR',
                'constraint' => 250,
                'default' => 'templates/estimate/blueline',
                'null' => true,
            ],
            'compteBancaire_pdf_template' => [
                'type' => 'VARCHAR',
                'constraint' => 250,
                'default' => 'templates/compteBancaire/blueline',
                'null' => false,
            ],
            'invoice_pdf_template' => [
                'type' => 'VARCHAR',
                'constraint' => 250,
                'default' => 'templates/invoice/blueline',
                'null' => true,
            ],
            'second_tax' => [
                'type' => 'VARCHAR',
                'constraint' => 5,
                'null' => true,
            ],
            'estimate_mail_subject' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'default' => 'New Estimate #{estimate_id}',
                'null' => true,
            ],
            'money_format' => [
                'type' => 'INT',
                'constraint' => 10,
                'unsigned' => true,
                'default' => '1',
            ],
            'money_currency_position' => [
                'type' => 'INT',
                'constraint' => 10,
                'unsigned' => true,
                'default' => '1',
            ],
            'pdf_font' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'default' => 'NotoSans',
                'null' => true,
            ],
            'pdf_path' => [
                'type' => 'INT',
                'constraint' => 10,
                'unsigned' => true,
                'default' => '1',
            ],
            'registration' => [
                'type' => 'INT',
                'constraint' => 10,
                'unsigned' => true,
                'default' => '0',
            ],
            'authorize_api_login_id' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'authorize_api_transaction_key' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'authorize_net' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
            'authorize_currency' => [
                'type' => 'VARCHAR',
                'constraint' => 30,
                'null' => true,
            ],
            'invoice_prefix' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'company_prefix' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'quotation_prefix' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'project_prefix' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'subscription_prefix' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'calendar_google_api_key' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'calendar_google_event_address' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'default_client_modules' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'estimate_reference' => [
                'type' => 'INT',
                'constraint' => 11,
            ],
            'login_background' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'default' => 'field.jpg',
                'null' => true,
            ],
            'custom_colors' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => '1',
                'null' => true,
            ],
            'top_bar_background' => [
                'type' => 'VARCHAR',
                'constraint' => 60,
                'null' => true,
            ],
            'top_bar_color' => [
                'type' => 'VARCHAR',
                'constraint' => 60,
                'null' => true,
            ],
            'body_background' => [
                'type' => 'VARCHAR',
                'constraint' => 60,
                'null' => true,
            ],
            'menu_background' => [
                'type' => 'VARCHAR',
                'constraint' => 60,
                'null' => true,
            ],
            'menu_color' => [
                'type' => 'VARCHAR',
                'constraint' => 60,
                'null' => true,
            ],
            'primary_color' => [
                'type' => 'VARCHAR',
                'constraint' => 60,
                'null' => true,
            ],
            'twocheckout_seller_id' => [
                'type' => 'VARCHAR',
                'constraint' => 250,
                'null' => true,
            ],
            'twocheckout_publishable_key' => [
                'type' => 'VARCHAR',
                'constraint' => 250,
                'null' => true,
            ],
            'twocheckout_private_key' => [
                'type' => 'VARCHAR',
                'constraint' => 250,
                'null' => true,
            ],
            'twocheckout' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => '0',
                'null' => true,
            ],
            'twocheckout_currency' => [
                'type' => 'VARCHAR',
                'constraint' => 250,
                'null' => true,
            ],
            'login_logo' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'login_style' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'default' => 'left',
                'null' => true,
            ],
            'token_registrar' => [
                'type' => 'VARCHAR',
                'constraint' => 128,
                'null' => true,
            ],
            'token_key' => [
                'type' => 'VARCHAR',
                'constraint' => 128,
                'null' => true,
            ],
            'jarvis_url' => [
                'type' => 'VARCHAR',
                'constraint' => 128,
                'default' => 'None',
                'null' => false,
            ],
            'echeance' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
            'commande_prefix' => [
                'type' => 'VARCHAR',
                'constraint' => 250,
                'null' => true,
            ],
            'livraison_prefix' => [
                'type' => 'VARCHAR',
                'constraint' => 250,
                'null' => true,
            ],
            'display_logo_facture' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => '1',
                'null' => true,
            ],
            'money_display' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => '2',
                'null' => true,
            ],
            'display_logo_devis' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => '1',
                'null' => false,
            ],
            'display_logo_commande' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => '1',
                'null' => false,
            ],
            'display_logo_livraison' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => '1',
                'null' => false,
            ],
            'id_vcompanies' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'commande_terms' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
                'null' => true,
            ],
            'commande_reference' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => '0',
                'null' => false,
            ],
            'livraison_reference' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => '0',
                'null' => false,
            ],
            'timbre_fiscal' => [
                'type' => 'FLOAT',
                'default' => '0',
                'null' => false,
            ],
            'chiffre' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
            'compteBancaire' => [
                'type' => 'VARCHAR',
                'constraint' => 250,
                'null' => true,
            ],
            'notes' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'notes_facture' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'avoir_prefix' => [
                'type' => 'VARCHAR',
                'constraint' => 250,
                'null' => true,
            ],
            'avoir_reference' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => '0',
                'null' => false,
            ],
            'notes_avoir' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'display_logo_avoir' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => '1',
                'null' => false,
            ],
            'avoir_pdf_template' => [
                'type' => 'VARCHAR',
                'constraint' => 250,
                'default' => 'templates/avoir/blueline',
                'null' => false,
            ],
            'default_template' => [
                'type' => 'VARCHAR',
                'constraint' => 250,
                'null' => true,
            ],
            'valid_to' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'valid_from' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'number_users' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => '1',
                'null' => false,
            ],
            'companies' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => '1',
                'null' => false,
            ],
            'signataire' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'default' => 'None',
                'null' => false,
            ],
            'email_notification' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true); // Primary key
        $this->forge->addForeignKey('id_vcompanies', 'vcompanies', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('core');
    }

    public function down()
    {
        $this->forge->dropTable('core');
    }
}
