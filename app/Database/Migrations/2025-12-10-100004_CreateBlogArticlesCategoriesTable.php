<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Migration for blog_articles_categories pivot table
 */
class CreateBlogArticlesCategoriesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'article_id' => [
                'type' => 'UUID',
                'null' => false,
            ],
            'category_id' => [
                'type' => 'UUID',
                'null' => false,
            ],
        ]);

        $this->forge->addKey(['article_id', 'category_id'], true);
        $this->forge->addForeignKey('article_id', 'blog_articles', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('category_id', 'blog_categories', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('blog_articles_categories');
    }

    public function down()
    {
        $this->forge->dropTable('blog_articles_categories', true);
    }
}
