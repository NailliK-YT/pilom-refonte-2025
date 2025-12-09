<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCompanyIdToTvaRates extends Migration
{
    public function up()
    {
        // First, add the column as nullable
        //$fields = [
        //    'company_id' => [
        //        'type' => 'UUID',
        //        'null' => true,
        //        'after' => 'id'
        //    ]
        //];
//
        //// Add the column
        //$this->forge->addColumn('tva_rates', $fields);
//
        //// Get the first company to use as default
        //$db = \Config\Database::connect();
        //$company = $db->table('companies')->get()->getFirstRow();
//
        //if ($company) {
        //    // Update existing records with the company_id
        //    $db->table('tva_rates')
        //        ->set(['company_id' => $company->id])
        //        ->update();
        //}
//
        //// Now alter the column to NOT NULL
        //$this->db->query('ALTER TABLE tva_rates ALTER COLUMN company_id SET NOT NULL');
//
        //// Add foreign key constraint
        //$this->db->query('ALTER TABLE tva_rates ADD CONSTRAINT fk_tva_rates_company FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE ON UPDATE CASCADE');
    }

    public function down()
    {
        // Drop foreign key first
        //$this->db->query('ALTER TABLE tva_rates DROP CONSTRAINT IF EXISTS fk_tva_rates_company');
//
        //// Drop column
        //$this->forge->dropColumn('tva_rates', 'company_id');
    }
}
