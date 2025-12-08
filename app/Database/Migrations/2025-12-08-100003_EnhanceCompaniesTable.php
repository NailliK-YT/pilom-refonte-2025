<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Enhances the companies table with additional business and subscription fields
 */
class EnhanceCompaniesTable extends Migration
{
    public function up()
    {
        // Add new fields to companies table
        $fields = [
            'siret' => [
                'type' => 'VARCHAR',
                'constraint' => 14,
                'null' => true,
                'after' => 'name'
            ],
            'siren' => [
                'type' => 'VARCHAR',
                'constraint' => 9,
                'null' => true,
                'after' => 'siret'
            ],
            'address' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'siren'
            ],
            'city' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
                'after' => 'address'
            ],
            'postal_code' => [
                'type' => 'VARCHAR',
                'constraint' => 10,
                'null' => true,
                'after' => 'city'
            ],
            'country' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'default' => 'France',
                'null' => false,
                'after' => 'postal_code'
            ],
            'logo' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'after' => 'country'
            ],
            'subscription_plan' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'default' => 'free',
                'null' => false,
                'after' => 'logo'
            ],
            'subscription_expires_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
                'after' => 'subscription_plan'
            ],
            'max_users' => [
                'type' => 'INT',
                'unsigned' => true,
                'default' => 1,
                'null' => false,
                'after' => 'subscription_expires_at'
            ],
        ];

        $this->forge->addColumn('companies', $fields);

        // Add index on subscription_plan for reporting
        $this->db->query('CREATE INDEX idx_companies_subscription ON companies(subscription_plan)');
    }

    public function down()
    {
        // Drop added columns
        $this->forge->dropColumn('companies', [
            'siret',
            'siren',
            'address',
            'city',
            'postal_code',
            'country',
            'logo',
            'subscription_plan',
            'subscription_expires_at',
            'max_users'
        ]);
    }
}
