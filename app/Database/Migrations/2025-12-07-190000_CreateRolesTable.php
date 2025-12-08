<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateRolesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => false,
            ],
            'description' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
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
        $this->forge->addUniqueKey('name');
        $this->forge->createTable('roles');

        // Insert default roles
        $this->db->table('roles')->insertBatch([
            ['name' => 'admin', 'description' => 'Administrateur avec accès complet', 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'user', 'description' => 'Utilisateur standard', 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'comptable', 'description' => 'Accès aux fonctions comptables', 'created_at' => date('Y-m-d H:i:s')],
        ]);
    }

    public function down()
    {
        $this->forge->dropTable('roles');
    }
}
