<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddUserCompanyForeignKey extends Migration
{
    public function up()
    {
        // Add foreign key constraint from users.company_id to companies.id
        // The company_id column already exists from CreateUsersTable migration
        $this->db->query('ALTER TABLE users ADD CONSTRAINT users_company_id_foreign FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE SET NULL ON UPDATE CASCADE');
    }

    public function down()
    {
        $this->forge->dropForeignKey('users', 'users_company_id_foreign');
    }
}
