<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run()
    {
        $company = $this->db->table('companies')->get()->getFirstRow();
        
        // Récupérer une catégorie et un taux de TVA
        $category = $this->db->table('categories')
            ->where('parent_id IS NOT NULL')
            ->where('company_id', $company->id ?? null)
            ->orderBy('id', 'ASC')
            ->limit(1)
            ->get()
            ->getFirstRow();

        $tvaRate = $this->db->table('tva_rates')
            ->where('is_default', true)
            ->where('company_id', $company->id ?? null)
            ->get()
            ->getFirstRow();

        if (!$company || !$category || !$tvaRate) {
            echo "⚠️  Entreprise, catégories ou taux de TVA manquants\n";
            return;
        }

        $products = [
            [
                'id' => $this->generateUUID(),
                'company_id' => $company->id,
                'name' => 'MacBook Pro 14" M3',
                'description' => 'Ordinateur portable Apple M3, 16GB RAM, 512GB SSD.',
                'reference' => 'MBP-14-M3-16-512',
                'price_ht' => 2199.00,
                'category_id' => $category->id,
                'tva_id' => $tvaRate->id,
                'stock_quantity' => 5,
                'stock_alert_threshold' => 2,
                'image_path' => null,
                'is_archived' => false,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => $this->generateUUID(),
                'company_id' => $company->id,
                'name' => 'Dell XPS 15',
                'description' => 'PC portable haute performance Dell, Intel Core i7.',
                'reference' => 'DELL-XPS15-I7-32-1TB',
                'price_ht' => 1899.00,
                'category_id' => $category->id,
                'tva_id' => $tvaRate->id,
                'stock_quantity' => 4,
                'stock_alert_threshold' => 2,
                'image_path' => null,
                'is_archived' => false,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => $this->generateUUID(),
                'company_id' => $company->id,
                'name' => 'Écran Dell UltraSharp 27"',
                'description' => 'Moniteur professionnel 4K USB-C.',
                'reference' => 'DELL-U2723DE',
                'price_ht' => 549.00,
                'category_id' => $category->id,
                'tva_id' => $tvaRate->id,
                'stock_quantity' => 12,
                'stock_alert_threshold' => 5,
                'image_path' => null,
                'is_archived' => false,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => $this->generateUUID(),
                'company_id' => $company->id,
                'name' => 'Clavier mécanique Keychron K8',
                'description' => 'Clavier mécanique sans fil, switches Gateron Red.',
                'reference' => 'KEY-K8-RED-RGB',
                'price_ht' => 89.00,
                'category_id' => $category->id,
                'tva_id' => $tvaRate->id,
                'stock_quantity' => 25,
                'stock_alert_threshold' => 10,
                'image_path' => null,
                'is_archived' => false,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => $this->generateUUID(),
                'company_id' => $company->id,
                'name' => 'Souris Logitech MX Master 3S',
                'description' => 'Souris ergonomique sans fil.',
                'reference' => 'LOG-MXMS3-BLK',
                'price_ht' => 99.00,
                'category_id' => $category->id,
                'tva_id' => $tvaRate->id,
                'stock_quantity' => 30,
                'stock_alert_threshold' => 10,
                'image_path' => null,
                'is_archived' => false,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => $this->generateUUID(),
                'company_id' => $company->id,
                'name' => 'Bureau assis-debout électrique',
                'description' => 'Bureau réglable en hauteur motorisé.',
                'reference' => 'DESK-ELEC-140-OAK',
                'price_ht' => 449.00,
                'category_id' => $category->id,
                'tva_id' => $tvaRate->id,
                'stock_quantity' => 7,
                'stock_alert_threshold' => 3,
                'image_path' => null,
                'is_archived' => false,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => $this->generateUUID(),
                'company_id' => $company->id,
                'name' => 'Chaise ergonomique Herman Miller',
                'description' => 'Fauteuil de bureau ergonomique haut de gamme.',
                'reference' => 'HM-AERON-B-BLK',
                'price_ht' => 1299.00,
                'category_id' => $category->id,
                'tva_id' => $tvaRate->id,
                'stock_quantity' => 3,
                'stock_alert_threshold' => 1,
                'image_path' => null,
                'is_archived' => false,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => $this->generateUUID(),
                'company_id' => $company->id,
                'name' => 'Casque Sony WH-1000XM5',
                'description' => 'Casque sans fil à réduction de bruit.',
                'reference' => 'SONY-WH1000XM5-BLK',
                'price_ht' => 349.00,
                'category_id' => $category->id,
                'tva_id' => $tvaRate->id,
                'stock_quantity' => 18,
                'stock_alert_threshold' => 5,
                'image_path' => null,
                'is_archived' => false,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => $this->generateUUID(),
                'company_id' => $company->id,
                'name' => 'Webcam Logitech Brio 4K',
                'description' => 'Webcam professionnelle 4K Ultra HD.',
                'reference' => 'LOG-BRIO-4K',
                'price_ht' => 199.00,
                'category_id' => $category->id,
                'tva_id' => $tvaRate->id,
                'stock_quantity' => 14,
                'stock_alert_threshold' => 5,
                'image_path' => null,
                'is_archived' => false,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => $this->generateUUID(),
                'company_id' => $company->id,
                'name' => 'Pack 500 feuilles A4 Premium',
                'description' => 'Ramette papier A4 80g/m².',
                'reference' => 'PAPER-A4-80G-500',
                'price_ht' => 4.99,
                'category_id' => $category->id,
                'tva_id' => $tvaRate->id,
                'stock_quantity' => 200,
                'stock_alert_threshold' => 50,
                'image_path' => null,
                'is_archived' => false,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('products')->ignore(true)->insertBatch($products);
        echo "✓ " . count($products) . " produits créés\n";
    }

    private function generateUUID(): string
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff)
        );
    }
}
