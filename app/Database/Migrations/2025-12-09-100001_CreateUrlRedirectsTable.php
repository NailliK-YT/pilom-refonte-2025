<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Migration pour créer la table url_redirects
 * 
 * Gère les redirections 301 pour le SEO et la maintenance des URLs.
 */
class CreateUrlRedirectsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'UUID',
                'default' => new \CodeIgniter\Database\RawSql('uuid_generate_v4()'),
            ],
            'old_url' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
                'null' => false,
                'comment' => 'Ancienne URL (chemin relatif)',
            ],
            'new_url' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
                'null' => false,
                'comment' => 'Nouvelle URL (chemin relatif ou absolu)',
            ],
            'redirect_code' => [
                'type' => 'SMALLINT',
                'default' => 301,
                'comment' => 'Code HTTP de redirection (301, 302, 307, 308)',
            ],
            'hits' => [
                'type' => 'INTEGER',
                'default' => 0,
                'comment' => 'Nombre de fois où la redirection a été utilisée',
            ],
            'is_active' => [
                'type' => 'BOOLEAN',
                'default' => true,
                'comment' => 'Statut de la redirection',
            ],
            'created_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
                'default' => new \CodeIgniter\Database\RawSql('CURRENT_TIMESTAMP'),
            ],
        ]);

        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey('old_url', 'idx_url_redirects_old_url');
        $this->forge->addKey('is_active', false, false, 'idx_url_redirects_active');

        $this->forge->createTable('url_redirects', true);

        // Ajouter un commentaire sur la table
        $this->db->query("COMMENT ON TABLE url_redirects IS 'Redirections 301 pour le SEO'");
    }

    public function down()
    {
        $this->forge->dropTable('url_redirects', true);
    }
}
