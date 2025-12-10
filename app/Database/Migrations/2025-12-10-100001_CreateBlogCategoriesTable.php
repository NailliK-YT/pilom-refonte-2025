<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Migration for blog_categories table
 * Must be created BEFORE blog_articles for the pivot table
 */
class CreateBlogCategoriesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'UUID',
                'default' => new \CodeIgniter\Database\RawSql('gen_random_uuid()'),
            ],
            'parent_id' => [
                'type' => 'UUID',
                'null' => true,
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => false,
            ],
            'slug' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => false,
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'meta_title' => [
                'type' => 'VARCHAR',
                'constraint' => 70,
                'null' => true,
            ],
            'meta_description' => [
                'type' => 'VARCHAR',
                'constraint' => 160,
                'null' => true,
            ],
            'sort_order' => [
                'type' => 'INTEGER',
                'default' => 0,
            ],
            'is_active' => [
                'type' => 'BOOLEAN',
                'default' => true,
            ],
            'created_at' => [
                'type' => 'TIMESTAMP',
                'default' => new \CodeIgniter\Database\RawSql('CURRENT_TIMESTAMP'),
            ],
            'updated_at' => [
                'type' => 'TIMESTAMP',
                'default' => new \CodeIgniter\Database\RawSql('CURRENT_TIMESTAMP'),
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('slug');
        $this->forge->createTable('blog_categories');

        // Add self-referencing foreign key and indexes
        $this->db->query('ALTER TABLE blog_categories ADD CONSTRAINT fk_blog_categories_parent FOREIGN KEY (parent_id) REFERENCES blog_categories(id) ON DELETE SET NULL');
        $this->db->query('CREATE INDEX idx_blog_categories_slug ON blog_categories(slug)');
        $this->db->query('CREATE INDEX idx_blog_categories_parent ON blog_categories(parent_id)');
        $this->db->query('CREATE INDEX idx_blog_categories_active ON blog_categories(is_active)');

        // Insert default categories
        $this->db->query("INSERT INTO blog_categories (id, name, slug, description, sort_order) VALUES 
            (gen_random_uuid(), 'Gestion d''entreprise', 'gestion-entreprise', 'Articles sur la gestion et l''organisation de votre entreprise', 1),
            (gen_random_uuid(), 'Facturation', 'facturation', 'Conseils et bonnes pratiques pour la facturation', 2),
            (gen_random_uuid(), 'Conseils métier', 'conseils-metier', 'Astuces et conseils pour développer votre activité', 3)
        ");
    }

    public function down()
    {
        $this->forge->dropTable('blog_categories', true);
    }
}
