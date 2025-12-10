<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Migration for blog_comments table with threading support
 */
class CreateBlogCommentsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'UUID',
                'default' => new \CodeIgniter\Database\RawSql('gen_random_uuid()'),
            ],
            'article_id' => [
                'type' => 'UUID',
                'null' => false,
            ],
            'parent_id' => [
                'type' => 'UUID',
                'null' => true,
            ],
            // Author info (can be guest or logged user)
            'user_id' => [
                'type' => 'UUID',
                'null' => true,
            ],
            'author_name' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => false,
            ],
            'author_email' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => false,
            ],
            'author_website' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            // Content
            'content' => [
                'type' => 'TEXT',
                'null' => false,
            ],
            // Moderation
            'status' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'default' => 'pending',
            ],
            'moderated_by' => [
                'type' => 'UUID',
                'null' => true,
            ],
            'moderated_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
            ],
            // Anti-spam tracking
            'ip_address' => [
                'type' => 'VARCHAR',
                'constraint' => 45,
                'null' => true,
            ],
            'user_agent' => [
                'type' => 'TEXT',
                'null' => true,
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
        $this->forge->addForeignKey('article_id', 'blog_articles', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'SET NULL', 'SET NULL');
        $this->forge->addForeignKey('moderated_by', 'users', 'id', 'SET NULL', 'SET NULL');
        $this->forge->createTable('blog_comments');

        // Self-referencing FK for threading
        $this->db->query('ALTER TABLE blog_comments ADD CONSTRAINT fk_blog_comments_parent FOREIGN KEY (parent_id) REFERENCES blog_comments(id) ON DELETE CASCADE');

        // Add check constraint for status
        $this->db->query("ALTER TABLE blog_comments ADD CONSTRAINT chk_blog_comments_status CHECK (status IN ('pending', 'approved', 'spam', 'trash'))");

        // Add indexes
        $this->db->query('CREATE INDEX idx_blog_comments_article ON blog_comments(article_id)');
        $this->db->query('CREATE INDEX idx_blog_comments_status ON blog_comments(status)');
        $this->db->query('CREATE INDEX idx_blog_comments_parent ON blog_comments(parent_id)');
        $this->db->query('CREATE INDEX idx_blog_comments_created ON blog_comments(created_at DESC)');
    }

    public function down()
    {
        $this->forge->dropTable('blog_comments', true);
    }
}
