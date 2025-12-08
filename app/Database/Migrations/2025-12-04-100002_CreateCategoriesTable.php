<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCategoriesTable extends Migration
{
    public function up()
    {
        $this->db->simpleQuery('CREATE TABLE categories (
            id UUID PRIMARY KEY,
            company_id UUID NOT NULL,
            name VARCHAR(255) NOT NULL,
            description TEXT,
            parent_id UUID,
            created_at TIMESTAMP,
            updated_at TIMESTAMP,
            FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE ON UPDATE CASCADE
        )');
    }

    public function down()
    {
        $this->db->simpleQuery('DROP TABLE IF EXISTS categories');
    }
}
