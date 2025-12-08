<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PriceTiersSeeder extends Seeder
{
    public function run()
    {
        $products = $this->db->table('products')
            ->limit(5)
            ->get()
            ->getResult();
        
        if (empty($products)) {
            echo "⚠️  Aucun produit trouvé\n";
            return;
        }

        $priceTiers = [];
        
        foreach ($products as $product) {
            $basePrice = (float) $product->price_ht;
            
            // Palier 1 : 10+ unités = -5%
            $priceTiers[] = [
                'id' => $this->generateUUID(),
                'product_id' => $product->id,
                'min_quantity' => 10,
                'price_ht' => round($basePrice * 0.95, 2),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
            
            // Palier 2 : 25+ unités = -10%
            $priceTiers[] = [
                'id' => $this->generateUUID(),
                'product_id' => $product->id,
                'min_quantity' => 25,
                'price_ht' => round($basePrice * 0.90, 2),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
            
            // Palier 3 : 50+ unités = -15%
            $priceTiers[] = [
                'id' => $this->generateUUID(),
                'product_id' => $product->id,
                'min_quantity' => 50,
                'price_ht' => round($basePrice * 0.85, 2),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
        }

        $this->db->table('price_tiers')->insertBatch($priceTiers);
        echo "✓ " . count($priceTiers) . " paliers de prix créés\n";
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

