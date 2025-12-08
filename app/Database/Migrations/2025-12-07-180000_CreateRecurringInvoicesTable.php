<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateRecurringInvoicesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true
            ],
            'contact_id' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => false,
            ],
            'frequency' => [
                'type'       => 'VARCHAR',
                'constraint' => '20', // monthly, quarterly, yearly, weekly
                'null'       => false,
            ],
            'start_date' => [
                'type' => 'DATE',
                'null' => false,
            ],
            'end_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'next_run_date' => [
                'type' => 'DATE',
                'null' => false,
            ],
            'montant_ht' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => false,
            ],
            'montant_tva' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => false,
            ],
            'montant_ttc' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => false,
            ],
            'status' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
                'default'    => 'active', // active, paused, cancelled
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('contact_id', 'contact', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('recurring_invoices');
    }

    public function down()
    {
        $this->forge->dropTable('recurring_invoices');
    }
}
