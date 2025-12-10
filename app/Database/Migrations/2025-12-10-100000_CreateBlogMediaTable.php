<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Migration for blog_media table
 * Must be created BEFORE blog_articles as articles reference media
 */
class CreateBlogMediaTable extends Migration
{
    public function up()
    {
        // Ensure UUID extension exists
        $this->db->query('CREATE EXTENSION IF NOT EXISTS "pgcrypto"');

        $this->forge->addField([
            'id' => [
                'type' => 'UUID',
                'default' => new \CodeIgniter\Database\RawSql('gen_random_uuid()'),
            ],
            'uploaded_by' => [
                'type' => 'UUID',
                'null' => false,
            ],
            'filename' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => false,
            ],
            'original_filename' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => false,
            ],
            'file_path' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
                'null' => false,
            ],
            'file_type' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => false,
            ],
            'mime_type' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => false,
            ],
            'file_size' => [
                'type' => 'INTEGER',
                'null' => false,
            ],
            'width' => [
                'type' => 'INTEGER',
                'null' => true,
            ],
            'height' => [
                'type' => 'INTEGER',
                'null' => true,
            ],
            'thumbnail_path' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
                'null' => true,
            ],
            'medium_path' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
                'null' => true,
            ],
            'webp_path' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
                'null' => true,
            ],
            'alt_text' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'caption' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'title' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
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
        $this->forge->addForeignKey('uploaded_by', 'users', 'id', 'CASCADE', 'SET NULL');
        $this->forge->createTable('blog_media');

        // Add indexes
        $this->db->query('CREATE INDEX idx_blog_media_type ON blog_media(file_type)');
        $this->db->query('CREATE INDEX idx_blog_media_uploaded ON blog_media(uploaded_by)');
    }

    public function down()
    {
        $this->forge->dropTable('blog_media', true);
    }
}
