<?php

namespace Database\Seeders;

use App\Models\InventoryItem;
use Illuminate\Database\Seeder;

class PosProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            // Aftercare Products
            [
                'name' => 'Acne Cream',
                'sku' => 'POS-ACNE-001',
                'category' => 'Aftercare Products',
                'description' => 'Acne treatment cream for post-treatment care',
                'cost_price' => 200.00,
                'selling_price' => 250.00,
                'current_stock' => 100,
                'minimum_stock' => 20,
                'unit' => 'piece',
                'supplier' => 'K-Derma Supplies',
                'expiry_date' => now()->addMonths(24),
                'is_active' => true,
            ],
            [
                'name' => 'Toner',
                'sku' => 'POS-TONER-001',
                'category' => 'Aftercare Products',
                'description' => 'Facial toner for daily skincare routine',
                'cost_price' => 250.00,
                'selling_price' => 300.00,
                'current_stock' => 100,
                'minimum_stock' => 20,
                'unit' => 'bottle',
                'supplier' => 'K-Derma Supplies',
                'expiry_date' => now()->addMonths(18),
                'is_active' => true,
            ],
            [
                'name' => 'Sunscreen',
                'sku' => 'POS-SUN-001',
                'category' => 'Aftercare Products',
                'description' => 'Sun protection cream for daily use',
                'cost_price' => 250.00,
                'selling_price' => 300.00,
                'current_stock' => 100,
                'minimum_stock' => 20,
                'unit' => 'tube',
                'supplier' => 'K-Derma Supplies',
                'expiry_date' => now()->addMonths(12),
                'is_active' => true,
            ],
            [
                'name' => 'Foaming Wash',
                'sku' => 'POS-FW-001',
                'category' => 'Aftercare Products',
                'description' => 'Gentle foaming facial wash',
                'cost_price' => 280.00,
                'selling_price' => 350.00,
                'current_stock' => 100,
                'minimum_stock' => 20,
                'unit' => 'bottle',
                'supplier' => 'K-Derma Supplies',
                'expiry_date' => now()->addMonths(18),
                'is_active' => true,
            ],
            [
                'name' => 'Soap',
                'sku' => 'POS-SOAP-001',
                'category' => 'Aftercare Products',
                'description' => 'Gentle cleansing soap',
                'cost_price' => 120.00,
                'selling_price' => 150.00,
                'current_stock' => 100,
                'minimum_stock' => 20,
                'unit' => 'bar',
                'supplier' => 'K-Derma Supplies',
                'expiry_date' => now()->addMonths(24),
                'is_active' => true,
            ],
            [
                'name' => 'Day Cream',
                'sku' => 'POS-DC-001',
                'category' => 'Aftercare Products',
                'description' => 'Daily moisturizing day cream',
                'cost_price' => 250.00,
                'selling_price' => 300.00,
                'current_stock' => 100,
                'minimum_stock' => 20,
                'unit' => 'jar',
                'supplier' => 'K-Derma Supplies',
                'expiry_date' => now()->addMonths(18),
                'is_active' => true,
            ],
            [
                'name' => 'Night Cream',
                'sku' => 'POS-NC-001',
                'category' => 'Aftercare Products',
                'description' => 'Nighttime repair and moisturizing cream',
                'cost_price' => 250.00,
                'selling_price' => 300.00,
                'current_stock' => 100,
                'minimum_stock' => 20,
                'unit' => 'jar',
                'supplier' => 'K-Derma Supplies',
                'expiry_date' => now()->addMonths(18),
                'is_active' => true,
            ],
            [
                'name' => 'Package',
                'sku' => 'POS-PKG-001',
                'category' => 'Aftercare Products',
                'description' => 'Complete aftercare package',
                'cost_price' => 600.00,
                'selling_price' => 750.00,
                'current_stock' => 50,
                'minimum_stock' => 10,
                'unit' => 'package',
                'supplier' => 'K-Derma Supplies',
                'expiry_date' => now()->addMonths(24),
                'is_active' => true,
            ],
            // The Skin Story Products
            [
                'name' => 'The Skin Story Skin Perfection Cream',
                'sku' => 'POS-TSS-001',
                'category' => 'The Skin Story',
                'description' => 'Premium skin perfection cream',
                'cost_price' => 400.00,
                'selling_price' => 500.00,
                'current_stock' => 50,
                'minimum_stock' => 10,
                'unit' => 'jar',
                'supplier' => 'The Skin Story',
                'expiry_date' => now()->addMonths(18),
                'is_active' => true,
            ],
            [
                'name' => 'Collagen Water Sleeping Mask',
                'sku' => 'POS-CWSM-001',
                'category' => 'The Skin Story',
                'description' => 'Hydrating collagen water sleeping mask',
                'cost_price' => 350.00,
                'selling_price' => 450.00,
                'current_stock' => 50,
                'minimum_stock' => 10,
                'unit' => 'jar',
                'supplier' => 'The Skin Story',
                'expiry_date' => now()->addMonths(18),
                'is_active' => true,
            ],
            [
                'name' => 'Whitening Cream',
                'sku' => 'POS-WC-001',
                'category' => 'The Skin Story',
                'description' => 'Skin whitening and brightening cream',
                'cost_price' => 400.00,
                'selling_price' => 500.00,
                'current_stock' => 50,
                'minimum_stock' => 10,
                'unit' => 'jar',
                'supplier' => 'The Skin Story',
                'expiry_date' => now()->addMonths(18),
                'is_active' => true,
            ],
            // Laser Treatment Products
            [
                'name' => 'Aesthetic Cream (gamit sa lazer)',
                'sku' => 'POS-AEC-001',
                'category' => 'Laser Treatment',
                'description' => 'Aesthetic cream for laser treatment aftercare',
                'cost_price' => 300.00,
                'selling_price' => 400.00,
                'current_stock' => 50,
                'minimum_stock' => 10,
                'unit' => 'tube',
                'supplier' => 'K-Derma Supplies',
                'expiry_date' => now()->addMonths(12),
                'is_active' => true,
            ],
            [
                'name' => 'AE Cream (gamit sa lazer)',
                'sku' => 'POS-AE-001',
                'category' => 'Laser Treatment',
                'description' => 'AE cream for laser treatment aftercare',
                'cost_price' => 300.00,
                'selling_price' => 400.00,
                'current_stock' => 50,
                'minimum_stock' => 10,
                'unit' => 'tube',
                'supplier' => 'K-Derma Supplies',
                'expiry_date' => now()->addMonths(12),
                'is_active' => true,
            ],
            [
                'name' => 'Hydrocort (gamit sa lazer)',
                'sku' => 'POS-HC-001',
                'category' => 'Laser Treatment',
                'description' => 'Hydrocortisone cream for laser treatment aftercare',
                'cost_price' => 250.00,
                'selling_price' => 350.00,
                'current_stock' => 50,
                'minimum_stock' => 10,
                'unit' => 'tube',
                'supplier' => 'K-Derma Supplies',
                'expiry_date' => now()->addMonths(12),
                'is_active' => true,
            ],
        ];

        foreach ($products as $product) {
            // Check if product already exists by SKU
            $existing = InventoryItem::where('sku', $product['sku'])->first();
            
            if (!$existing) {
                InventoryItem::create($product);
                $this->command->info("Created product: {$product['name']}");
            } else {
                $this->command->warn("Product already exists: {$product['name']} (SKU: {$product['sku']})");
            }
        }

        $this->command->info('POS products seeded successfully!');
    }
}
