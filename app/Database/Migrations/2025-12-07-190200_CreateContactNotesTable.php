<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateContactNotesTable extends Migration
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
            'user_id' => [
                'type' => 'UUID',
                'null' => false,
            ],
            'content' => [
                'type' => 'TEXT',
                'null' => false,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('contact_id', 'contact', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('contact_notes');
    }

    public function down()
    {
        $this->forge->dropTable('contact_notes');
    }
}
