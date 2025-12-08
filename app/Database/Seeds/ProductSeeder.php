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
                'description' => 'Ordinateur portable professionnel Apple avec puce M3, 16GB RAM, 512GB SSD. Écran Liquid Retina XDR.',
                'reference' => 'MBP-14-M3-16-512',
                'price_ht' => 2199.00,
                'category_id' => $category->id,
                'tva_id' => $tvaRate->id,
                'image_path' => null,
                'is_archived' => false,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => $this->generateUUID(),
                'company_id' => $company->id,
                'name' => 'Dell XPS 15',
                'description' => 'PC portable haute performance Dell, Intel Core i7, 32GB RAM, 1TB SSD, écran 4K tactile.',
                'reference' => 'DELL-XPS15-I7-32-1TB',
                'price_ht' => 1899.00,
                'category_id' => $category->id,
                'tva_id' => $tvaRate->id,
                'image_path' => null,
                'is_archived' => false,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => $this->generateUUID(),
                'company_id' => $company->id,
                'name' => 'Écran Dell UltraSharp 27"',
                'description' => 'Moniteur professionnel 27 pouces, résolution 4K, IPS, 99% sRGB, USB-C avec charge 90W.',
                'reference' => 'DELL-U2723DE',
                'price_ht' => 549.00,
                'category_id' => $category->id,
                'tva_id' => $tvaRate->id,
                'image_path' => null,
                'is_archived' => false,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => $this->generateUUID(),
                'company_id' => $company->id,
                'name' => 'Clavier mécanique Keychron K8',
                'description' => 'Clavier mécanique sans fil, switches Gateron Red, rétroéclairage RGB, compatible Mac/PC.',
                'reference' => 'KEY-K8-RED-RGB',
                'price_ht' => 89.00,
                'category_id' => $category->id,
                'tva_id' => $tvaRate->id,
                'image_path' => null,
                'is_archived' => false,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => $this->generateUUID(),
                'company_id' => $company->id,
                'name' => 'Souris Logitech MX Master 3S',
                'description' => 'Souris ergonomique sans fil, 8000 DPI, boutons programmables, capteur ultra-précis.',
                'reference' => 'LOG-MXMS3-BLK',
                'price_ht' => 99.00,
                'category_id' => $category->id,
                'tva_id' => $tvaRate->id,
                'image_path' => null,
                'is_archived' => false,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => $this->generateUUID(),
                'company_id' => $company->id,
                'name' => 'Bureau assis-debout électrique',
                'description' => 'Bureau réglable en hauteur motorisé, plateau 140x70cm, capacité 100kg, mémorisation de positions.',
                'reference' => 'DESK-ELEC-140-OAK',
                'price_ht' => 449.00,
                'category_id' => $category->id,
                'tva_id' => $tvaRate->id,
                'image_path' => null,
                'is_archived' => false,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => $this->generateUUID(),
                'company_id' => $company->id,
                'name' => 'Chaise ergonomique Herman Miller',
                'description' => 'Fauteuil de bureau haut de gamme, support lombaire ajustable, accoudoirs 4D, garantie 12 ans.',
                'reference' => 'HM-AERON-B-BLK',
                'price_ht' => 1299.00,
                'category_id' => $category->id,
                'tva_id' => $tvaRate->id,
                'image_path' => null,
                'is_archived' => false,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => $this->generateUUID(),
                'company_id' => $company->id,
                'name' => 'Casque Sony WH-1000XM5',
                'description' => 'Casque sans fil à réduction de bruit active, autonomie 30h, audio haute résolution.',
                'reference' => 'SONY-WH1000XM5-BLK',
                'price_ht' => 349.00,
                'category_id' => $category->id,
                'tva_id' => $tvaRate->id,
                'image_path' => null,
                'is_archived' => false,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => $this->generateUUID(),
                'company_id' => $company->id,
                'name' => 'Webcam Logitech Brio 4K',
                'description' => 'Webcam professionnelle 4K Ultra HD, autofocus, HDR, correction de lumière, micro stéréo.',
                'reference' => 'LOG-BRIO-4K',
                'price_ht' => 199.00,
                'category_id' => $category->id,
                'tva_id' => $tvaRate->id,
                'image_path' => null,
                'is_archived' => false,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => $this->generateUUID(),
                'company_id' => $company->id,
                'name' => 'Pack 500 feuilles A4 Premium',
                'description' => 'Ramette papier A4 80g/m², ultra-blanc, certifié FSC, compatible tous types d\'imprimantes.',
                'reference' => 'PAPER-A4-80G-500',
                'price_ht' => 4.99,
                'category_id' => $category->id,
                'tva_id' => $tvaRate->id,
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

