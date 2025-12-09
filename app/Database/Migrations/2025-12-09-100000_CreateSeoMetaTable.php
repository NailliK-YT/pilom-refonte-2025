<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Migration pour créer la table seo_meta
 * 
 * Stocke les métadonnées SEO personnalisées pour chaque entité du site.
 */
class CreateSeoMetaTable extends Migration
{
    public function up()
    {
        // Vérifie si l'extension uuid-ossp est disponible
        $this->db->query("CREATE EXTENSION IF NOT EXISTS \"uuid-ossp\"");

        $this->forge->addField([
            'id' => [
                'type' => 'UUID',
                'default' => new \CodeIgniter\Database\RawSql('uuid_generate_v4()'),
            ],
            'entity_type' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => false,
                'comment' => 'Type d\'entité: page, product, category, etc.',
            ],
            'entity_id' => [
                'type' => 'UUID',
                'null' => false,
                'comment' => 'ID de l\'entité associée',
            ],
            'meta_title' => [
                'type' => 'VARCHAR',
                'constraint' => 70,
                'null' => true,
                'comment' => 'Meta title SEO (50-60 caractères recommandés)',
            ],
            'meta_description' => [
                'type' => 'VARCHAR',
                'constraint' => 170,
                'null' => true,
                'comment' => 'Meta description SEO (150-160 caractères recommandés)',
            ],
            'meta_keywords' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'Mots-clés SEO (séparés par virgules)',
            ],
            'og_title' => [
                'type' => 'VARCHAR',
                'constraint' => 70,
                'null' => true,
                'comment' => 'Titre Open Graph',
            ],
            'og_description' => [
                'type' => 'VARCHAR',
                'constraint' => 200,
                'null' => true,
                'comment' => 'Description Open Graph',
            ],
            'og_image' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'comment' => 'Image Open Graph (URL)',
            ],
            'twitter_title' => [
                'type' => 'VARCHAR',
                'constraint' => 70,
                'null' => true,
                'comment' => 'Titre Twitter Card',
            ],
            'twitter_description' => [
                'type' => 'VARCHAR',
                'constraint' => 200,
                'null' => true,
                'comment' => 'Description Twitter Card',
            ],
            'canonical_url' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'comment' => 'URL canonique personnalisée',
            ],
            'robots_directive' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'default' => 'index, follow',
                'comment' => 'Directive robots (index/noindex, follow/nofollow)',
            ],
            'created_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
                'default' => new \CodeIgniter\Database\RawSql('CURRENT_TIMESTAMP'),
            ],
            'updated_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
                'default' => new \CodeIgniter\Database\RawSql('CURRENT_TIMESTAMP'),
            ],
        ]);

        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey(['entity_type', 'entity_id'], 'idx_seo_meta_entity');
        $this->forge->addKey('entity_type', false, false, 'idx_seo_meta_type');

        $this->forge->createTable('seo_meta', true);

        // Ajouter un commentaire sur la table
        $this->db->query("COMMENT ON TABLE seo_meta IS 'Métadonnées SEO personnalisées par entité'");
    }

    public function down()
    {
        $this->forge->dropTable('seo_meta', true);
    }
}
