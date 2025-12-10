<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Migration for blog_article_versions table - version history
 */
class CreateBlogArticleVersionsTable extends Migration
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
            'author_id' => [
                'type' => 'UUID',
                'null' => false,
            ],
            // Snapshot of content
            'title' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => false,
            ],
            'content' => [
                'type' => 'TEXT',
                'null' => false,
            ],
            'excerpt' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            // Version metadata
            'version_number' => [
                'type' => 'INTEGER',
                'null' => false,
            ],
            'change_summary' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
                'null' => true,
            ],
            'created_at' => [
                'type' => 'TIMESTAMP',
                'default' => new \CodeIgniter\Database\RawSql('CURRENT_TIMESTAMP'),
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('article_id', 'blog_articles', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('author_id', 'users', 'id', 'CASCADE', 'SET NULL');
        $this->forge->createTable('blog_article_versions');

        $this->db->query('CREATE INDEX idx_blog_versions_article ON blog_article_versions(article_id)');
        $this->db->query('CREATE INDEX idx_blog_versions_number ON blog_article_versions(article_id, version_number DESC)');
    }

    public function down()
    {
        $this->forge->dropTable('blog_article_versions', true);
    }
}
