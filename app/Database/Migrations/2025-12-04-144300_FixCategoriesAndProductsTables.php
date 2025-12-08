<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class FixCategoriesAndProductsTables extends Migration
{
    public function up()
    {
        // Supprimer les anciennes tables si elles existent
        $this->db->query('DROP TABLE IF EXISTS price_tiers CASCADE');
        $this->db->query('DROP TABLE IF EXISTS products CASCADE');
        $this->db->query('DROP TABLE IF EXISTS categories CASCADE');

        // Créer categories avec la bonne structure (incluant company_id)
        $this->db->query('CREATE TABLE categories (
            id UUID PRIMARY KEY,
            company_id UUID NOT NULL,
            name VARCHAR(255) NOT NULL,
            description TEXT,
            parent_id UUID,
            created_at TIMESTAMP,
            updated_at TIMESTAMP,
            FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE ON UPDATE CASCADE
        )');

        // Créer products avec la bonne structure (incluant company_id)
        $this->db->query('CREATE TABLE products (
            id UUID PRIMARY KEY,
            company_id UUID NOT NULL,
            name VARCHAR(255) NOT NULL,
            description TEXT,
            reference VARCHAR(100) NOT NULL UNIQUE,
            price_ht DECIMAL(10,2) NOT NULL,
            tva_id UUID NOT NULL,
            category_id UUID NOT NULL,
            image_path VARCHAR(500),
            is_archived BOOLEAN DEFAULT FALSE,
            created_at TIMESTAMP,
            updated_at TIMESTAMP,
            FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE ON UPDATE CASCADE,
            FOREIGN KEY (tva_id) REFERENCES tva_rates(id) ON DELETE RESTRICT ON UPDATE CASCADE,
            FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE RESTRICT ON UPDATE CASCADE
        )');

        // Créer price_tiers avec la bonne structure
        $this->db->query('CREATE TABLE price_tiers (
            id UUID PRIMARY KEY,
            product_id UUID NOT NULL,
            min_quantity INTEGER NOT NULL,
            price_ht DECIMAL(10,2) NOT NULL,
            created_at TIMESTAMP,
            updated_at TIMESTAMP,
            FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE ON UPDATE CASCADE
        )');
    }

    public function down()
    {
        $this->db->query('DROP TABLE IF EXISTS price_tiers CASCADE');
        $this->db->query('DROP TABLE IF EXISTS products CASCADE');
        $this->db->query('DROP TABLE IF EXISTS categories CASCADE');
    }
}
