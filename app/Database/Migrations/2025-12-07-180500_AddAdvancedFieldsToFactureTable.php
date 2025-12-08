<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddAdvancedFieldsToFactureTable extends Migration
{
    public function up()
    {
        $fields = [
            'remise_type' => [
                'type'       => 'VARCHAR',
                'constraint' => '10', // percent, amount
                'null'       => true,
                'after'      => 'montant_ttc'
            ],
            'remise_value' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => 0,
                'after'      => 'remise_type'
            ],
            'acompte_montant' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => 0,
                'after'      => 'remise_value'
            ],
            'solde_restant' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => true,
                'after'      => 'acompte_montant'
            ],
            'penalite_retard_percent' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'default'    => 0,
                'after'      => 'solde_restant'
            ],
        ];

        $this->forge->addColumn('facture', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('facture', ['remise_type', 'remise_value', 'acompte_montant', 'solde_restant', 'penalite_retard_percent']);
    }
}
