<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Migration for blog_articles_tags pivot table
 */
class CreateBlogArticlesTagsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'article_id' => [
                'type' => 'UUID',
                'null' => false,
            ],
            'tag_id' => [
                'type' => 'UUID',
                'null' => false,
            ],
        ]);

        $this->forge->addKey(['article_id', 'tag_id'], true);
        $this->forge->addForeignKey('article_id', 'blog_articles', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('tag_id', 'blog_tags', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('blog_articles_tags');
    }

    public function down()
    {
        $this->forge->dropTable('blog_articles_tags', true);
    }
}
