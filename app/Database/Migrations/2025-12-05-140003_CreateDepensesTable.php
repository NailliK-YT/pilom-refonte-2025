<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDepensesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'UUID',
                'null' => false,
            ],
            'company_id' => [
                'type' => 'UUID',
                'null' => false,
            ],
            'user_id' => [
                'type' => 'UUID',
                'null' => false,
                'comment' => 'User who created the expense',
            ],
            'date' => [
                'type' => 'DATE',
                'null' => false,
            ],
            'montant_ht' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => false,
            ],
            'montant_ttc' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => false,
            ],
            'tva_id' => [
                'type' => 'UUID',
                'null' => false,
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => false,
            ],
            'categorie_id' => [
                'type' => 'UUID',
                'null' => false,
            ],
            'fournisseur_id' => [
                'type' => 'UUID',
                'null' => true,
            ],
            'justificatif_path' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
                'null' => true,
            ],
            'statut' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => false,
                'default' => 'brouillon',
            ],
            'recurrent' => [
                'type' => 'BOOLEAN',
                'null' => false,
                'default' => false,
            ],
            'frequence_id' => [
                'type' => 'UUID',
                'null' => true,
            ],
            'methode_paiement' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => false,
            ],
            'created_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
            ],
            'deleted_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('company_id');
        $this->forge->addKey('user_id');
        $this->forge->addKey('date');
        $this->forge->addKey('categorie_id');
        $this->forge->addKey('fournisseur_id');
        $this->forge->addKey('statut');
        $this->forge->addKey('deleted_at');

        $this->forge->addForeignKey('company_id', 'companies', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('tva_id', 'tva_rates', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->addForeignKey('categorie_id', 'categories_depenses', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->addForeignKey('fournisseur_id', 'fournisseurs', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('frequence_id', 'frequences', 'id', 'SET NULL', 'CASCADE');

        $this->forge->createTable('depenses');

        // Add constraints for enum values and amounts
        $this->db->query("ALTER TABLE depenses ADD CONSTRAINT check_statut CHECK (statut IN ('brouillon', 'valide', 'archive'))");
        $this->db->query("ALTER TABLE depenses ADD CONSTRAINT check_methode_paiement CHECK (methode_paiement IN ('especes', 'cheque', 'virement', 'cb'))");
        $this->db->query('ALTER TABLE depenses ADD CONSTRAINT check_montants_positive CHECK (montant_ht >= 0 AND montant_ttc >= 0)');
    }

    public function down()
    {
        $this->forge->dropTable('depenses');
    }
}
