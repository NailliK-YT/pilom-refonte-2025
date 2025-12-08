<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePagesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'UUID',
                'null' => false,
            ],
            'slug' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => false,
                'unique' => true,
            ],
            'parent_id' => [
                'type' => 'UUID',
                'null' => true,
            ],
            'title' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => false,
            ],
            'meta_title' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
            ],
            'meta_description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'meta_keywords' => [
                'type' => 'VARCHAR',
                'constraint' => '500',
                'null' => true,
            ],
            'content' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'is_in_menu' => [
                'type' => 'BOOLEAN',
                'default' => true,
            ],
            'menu_order' => [
                'type' => 'INT',
                'default' => 0,
            ],
            'menu_label' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ],
            'is_in_footer' => [
                'type' => 'BOOLEAN',
                'default' => false,
            ],
            'footer_order' => [
                'type' => 'INT',
                'default' => 0,
            ],
            'is_active' => [
                'type' => 'BOOLEAN',
                'default' => true,
            ],
            'created_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('parent_id', 'pages', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('pages');

        // Create indexes for performance
        $this->db->query('CREATE INDEX idx_pages_slug ON pages(slug)');
        $this->db->query('CREATE INDEX idx_pages_parent_id ON pages(parent_id)');
        $this->db->query('CREATE INDEX idx_pages_is_in_menu ON pages(is_in_menu) WHERE is_in_menu = TRUE');
        $this->db->query('CREATE INDEX idx_pages_is_active ON pages(is_active) WHERE is_active = TRUE');

        // Create function for automatic updated_at
        $this->db->query("
            CREATE OR REPLACE FUNCTION update_pages_updated_at()
            RETURNS TRIGGER AS $$
            BEGIN
                NEW.updated_at = CURRENT_TIMESTAMP;
                RETURN NEW;
            END;
            $$ LANGUAGE plpgsql;
        ");

        // Create trigger
        $this->db->query("
            CREATE TRIGGER trigger_update_pages_updated_at
            BEFORE UPDATE ON pages
            FOR EACH ROW
            EXECUTE FUNCTION update_pages_updated_at();
        ");
    }

    public function down()
    {
        // Drop trigger and function
        $this->db->query('DROP TRIGGER IF EXISTS trigger_update_pages_updated_at ON pages');
        $this->db->query('DROP FUNCTION IF EXISTS update_pages_updated_at()');

        // Drop table (foreign keys will be dropped automatically)
        $this->forge->dropTable('pages', true);
    }
}
