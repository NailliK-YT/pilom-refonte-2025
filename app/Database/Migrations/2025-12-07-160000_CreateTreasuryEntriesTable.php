<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTreasuryEntriesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'UUID',
            ],
            'company_id' => [
                'type' => 'UUID',
            ],
            'type' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'comment' => 'entry, exit, transfer'
            ],
            'category' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'comment' => 'invoice, expense, payment, other'
            ],
            'amount' => [
                'type' => 'DECIMAL',
                'constraint' => '12,2',
            ],
            'balance_after' => [
                'type' => 'DECIMAL',
                'constraint' => '12,2',
                'null' => true,
            ],
            'reference_type' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'comment' => 'facture, depense, reglement, etc.'
            ],
            'reference_id' => [
                'type' => 'UUID',
                'null' => true,
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'transaction_date' => [
                'type' => 'DATE',
            ],
            'created_by' => [
                'type' => 'UUID',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
            ],
        ]);
        
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('company_id', 'companies', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('created_by', 'users', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('treasury_entries');
        
        // Create treasury_alerts table for threshold alerts
        $this->forge->addField([
            'id' => [
                'type' => 'UUID',
            ],
            'company_id' => [
                'type' => 'UUID',
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'threshold_type' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'comment' => 'below, above'
            ],
            'threshold_amount' => [
                'type' => 'DECIMAL',
                'constraint' => '12,2',
            ],
            'is_active' => [
                'type' => 'BOOLEAN',
                'default' => true,
            ],
            'last_triggered_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
            ],
        ]);
        
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('company_id', 'companies', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('treasury_alerts');
    }

    public function down()
    {
        $this->forge->dropTable('treasury_alerts', true);
        $this->forge->dropTable('treasury_entries', true);
    }
}
