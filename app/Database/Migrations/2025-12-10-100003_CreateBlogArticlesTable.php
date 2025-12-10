<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Migration for blog_articles table - main content table
 */
class CreateBlogArticlesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'UUID',
                'default' => new \CodeIgniter\Database\RawSql('gen_random_uuid()'),
            ],
            'author_id' => [
                'type' => 'UUID',
                'null' => false,
            ],
            // Content
            'title' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => false,
            ],
            'slug' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => false,
            ],
            'excerpt' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'content' => [
                'type' => 'TEXT',
                'null' => false,
            ],
            // Featured image
            'featured_image_id' => [
                'type' => 'UUID',
                'null' => true,
            ],
            'featured_image_alt' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            // Status
            'status' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'default' => 'draft',
            ],
            'published_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
            ],
            // SEO
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
            'meta_keywords' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'canonical_url' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
                'null' => true,
            ],
            'robots' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'default' => 'index, follow',
            ],
            // Open Graph
            'og_title' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'og_description' => [
                'type' => 'VARCHAR',
                'constraint' => 200,
                'null' => true,
            ],
            'og_image' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
                'null' => true,
            ],
            // Twitter Cards
            'twitter_title' => [
                'type' => 'VARCHAR',
                'constraint' => 70,
                'null' => true,
            ],
            'twitter_description' => [
                'type' => 'VARCHAR',
                'constraint' => 200,
                'null' => true,
            ],
            'twitter_image' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
                'null' => true,
            ],
            // Options
            'allow_comments' => [
                'type' => 'BOOLEAN',
                'default' => true,
            ],
            'is_featured' => [
                'type' => 'BOOLEAN',
                'default' => false,
            ],
            'reading_time' => [
                'type' => 'INTEGER',
                'default' => 0,
            ],
            'word_count' => [
                'type' => 'INTEGER',
                'default' => 0,
            ],
            'view_count' => [
                'type' => 'INTEGER',
                'default' => 0,
            ],
            // Timestamps
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
        $this->forge->addForeignKey('author_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('featured_image_id', 'blog_media', 'id', 'SET NULL', 'SET NULL');
        $this->forge->createTable('blog_articles');

        // Add check constraint for status
        $this->db->query("ALTER TABLE blog_articles ADD CONSTRAINT chk_blog_articles_status CHECK (status IN ('draft', 'published', 'scheduled', 'archived'))");

        // Add indexes
        $this->db->query('CREATE INDEX idx_blog_articles_status ON blog_articles(status)');
        $this->db->query('CREATE INDEX idx_blog_articles_published ON blog_articles(published_at)');
        $this->db->query('CREATE INDEX idx_blog_articles_author ON blog_articles(author_id)');
        $this->db->query('CREATE INDEX idx_blog_articles_featured ON blog_articles(is_featured)');
        $this->db->query('CREATE INDEX idx_blog_articles_created ON blog_articles(created_at DESC)');
    }

    public function down()
    {
        $this->forge->dropTable('blog_articles', true);
    }
}
