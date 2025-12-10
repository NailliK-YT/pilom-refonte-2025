<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Migration for blog_newsletter_subscribers table
 * Note: Email sending not implemented - this stores subscribers for future use
 */
class CreateBlogNewsletterSubscribersTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'UUID',
                'default' => new \CodeIgniter\Database\RawSql('gen_random_uuid()'),
            ],
            'email' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => false,
            ],
            // Confirmation (for future double opt-in)
            'is_confirmed' => [
                'type' => 'BOOLEAN',
                'default' => true, // Default to true since no email confirmation yet
            ],
            'confirmation_token' => [
                'type' => 'VARCHAR',
                'constraint' => 64,
                'null' => true,
            ],
            'confirmed_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
            ],
            // Unsubscribe
            'is_active' => [
                'type' => 'BOOLEAN',
                'default' => true,
            ],
            'unsubscribed_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
            ],
            'unsubscribe_token' => [
                'type' => 'VARCHAR',
                'constraint' => 64,
                'null' => true,
            ],
            // Tracking
            'ip_address' => [
                'type' => 'VARCHAR',
                'constraint' => 45,
                'null' => true,
            ],
            'source' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'created_at' => [
                'type' => 'TIMESTAMP',
                'default' => new \CodeIgniter\Database\RawSql('CURRENT_TIMESTAMP'),
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('email');
        $this->forge->createTable('blog_newsletter_subscribers');

        $this->db->query('CREATE INDEX idx_newsletter_active ON blog_newsletter_subscribers(is_active, is_confirmed)');
    }

    public function down()
    {
        $this->forge->dropTable('blog_newsletter_subscribers', true);
    }
}
