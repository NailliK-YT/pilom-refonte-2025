<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateContactAttachmentsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true
            ],
            'contact_id' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => false,
            ],
            'filename' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => false,
            ],
            'original_name' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => false,
            ],
            'path' => [
                'type'       => 'VARCHAR',
                'constraint' => '500',
                'null'       => false,
            ],
            'mime_type' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
            ],
            'size' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('contact_id', 'contact', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('contact_attachments');
    }

    public function down()
    {
        $this->forge->dropTable('contact_attachments');
    }
}
