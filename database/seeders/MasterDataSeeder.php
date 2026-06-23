<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Supplier;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MasterDataSeeder extends Seeder
{
    public function run(): void
    {
        $storeId = 1;

        $owner = User::find(2);

        if ($owner && ! $owner->stores()->where('store_id', $storeId)->exists()) {
            $owner->stores()->attach($storeId, ['role' => 'owner']);
        }

        $this->seedCategories($storeId);
        $this->seedUnits($storeId);
        $this->seedSuppliers($storeId);
        $this->seedCustomers($storeId);
        $this->seedProducts($storeId);

        $this->command->info('Master data seeded successfully.');
    }

    private function seedCategories(int $storeId): void
    {
        $categories = [
            ['name' => 'Minuman', 'description' => 'Air mineral, teh, susu, kopi, minuman ringan'],
            ['name' => 'Makanan Ringan', 'description' => 'Mi instan, biskuit, permen, snack'],
            ['name' => 'Sembako', 'description' => 'Beras, gula, minyak, telur, kebutuhan pokok'],
            ['name' => 'Bumbu & Saus', 'description' => 'Kecap, saos sambal, bumbu masak'],
            ['name' => 'Produk Rumah Tangga', 'description' => 'Sabun, deterjen, shampoo, pasta gigi'],
            ['name' => 'Rokok', 'description' => 'Rokok kretek & filter'],
        ];

        foreach ($categories as $data) {
            Category::create([
                'store_id' => $storeId,
                'name' => $data['name'],
                'slug' => Str::slug($data['name']),
                'description' => $data['description'],
                'is_active' => true,
            ]);
        }

        $this->command->info('  Categories: '.count($categories));
    }

    private function seedUnits(int $storeId): void
    {
        $units = [
            ['name' => 'Pcs', 'abbreviation' => 'pcs'],
            ['name' => 'Dus', 'abbreviation' => 'dus'],
            ['name' => 'Pack', 'abbreviation' => 'pack'],
            ['name' => 'Bungkus', 'abbreviation' => 'bks'],
            ['name' => 'Botol', 'abbreviation' => 'btl'],
            ['name' => 'Saset', 'abbreviation' => 'sst'],
            ['name' => 'Kaleng', 'abbreviation' => 'klg'],
            ['name' => 'Slop', 'abbreviation' => 'slop'],
            ['name' => 'Kilogram', 'abbreviation' => 'kg'],
            ['name' => 'Liter', 'abbreviation' => 'ltr'],
        ];

        foreach ($units as $data) {
            Unit::create([
                'store_id' => $storeId,
                'name' => $data['name'],
                'abbreviation' => $data['abbreviation'],
                'is_active' => true,
            ]);
        }

        $this->command->info('  Units: '.count($units));
    }

    private function seedSuppliers(int $storeId): void
    {
        $suppliers = [
            ['name' => 'PT Indofood Sukses Makmur', 'phone' => '021-57951122', 'email' => 'sales@indofood.co.id', 'city' => 'Jakarta'],
            ['name' => 'PT Unilever Indonesia', 'phone' => '021-80828900', 'email' => 'order@unilever.co.id', 'city' => 'Jakarta'],
            ['name' => 'PT Mayora Indah', 'phone' => '021-80627000', 'email' => 'info@mayora.co.id', 'city' => 'Jakarta'],
            ['name' => 'CV Sinar Jaya', 'phone' => '021-5550004', 'email' => 'sinarjaya@gmail.com', 'city' => 'Jakarta'],
            ['name' => 'UD Sumber Rejeki', 'phone' => '031-5550005', 'email' => 'sumberrejeki@yahoo.com', 'city' => 'Surabaya'],
        ];

        foreach ($suppliers as $data) {
            Supplier::create([
                'store_id' => $storeId,
                'name' => $data['name'],
                'phone' => $data['phone'],
                'email' => $data['email'],
                'address' => fake()->address(),
                'city' => $data['city'],
                'is_active' => true,
            ]);
        }

        $this->command->info('  Suppliers: '.count($suppliers));
    }

    private function seedCustomers(int $storeId): void
    {
        $customers = [
            ['name' => 'Budi Santoso', 'phone' => '081234567891'],
            ['name' => 'Siti Rahmawati', 'phone' => '081234567892'],
            ['name' => 'Ahmad Hidayat', 'phone' => '081234567893'],
            ['name' => 'Dewi Lestari', 'phone' => '081234567894'],
            ['name' => 'Rudi Hartono', 'phone' => '081234567895'],
        ];

        foreach ($customers as $data) {
            Customer::create([
                'store_id' => $storeId,
                'name' => $data['name'],
                'phone' => $data['phone'],
                'email' => strtolower(str_replace(' ', '.', $data['name'])).'@email.com',
                'address' => fake()->address(),
                'is_active' => true,
            ]);
        }

        $this->command->info('  Customers: '.count($customers));
    }

    private function seedProducts(int $storeId): void
    {
        $categories = Category::where('store_id', $storeId)->pluck('id', 'name');
        $units = Unit::where('store_id', $storeId)->pluck('id', 'name');

        $products = [
            [
                'name' => 'Air Mineral 600ml 1 Dus (isi 24)',
                'category' => 'Minuman', 'unit' => 'Dus',
                'purchase_price' => 25000, 'selling_price' => 35000,
                'variants' => null,
            ],
            [
                'name' => 'Air Mineral 1500ml 1 Dus (isi 12)',
                'category' => 'Minuman', 'unit' => 'Dus',
                'purchase_price' => 28000, 'selling_price' => 38000,
                'variants' => null,
            ],
            [
                'name' => 'Teh Kotak 1 Dus (isi 24)',
                'category' => 'Minuman', 'unit' => 'Dus',
                'purchase_price' => 42000, 'selling_price' => 55000,
                'variants' => null,
            ],
            [
                'name' => 'Mi Instan 1 Dus (isi 40)',
                'category' => 'Makanan Ringan', 'unit' => 'Dus',
                'purchase_price' => 85000, 'selling_price' => 110000,
                'variants' => ['Ayam Bawang', 'Kari', 'Soto'],
            ],
            [
                'name' => 'Biskuit Roma Kelapa 1 Pack',
                'category' => 'Makanan Ringan', 'unit' => 'Pack',
                'purchase_price' => 6500, 'selling_price' => 10000,
                'variants' => null,
            ],
            [
                'name' => 'Biskuit Roma Sandwich 1 Pack',
                'category' => 'Makanan Ringan', 'unit' => 'Pack',
                'purchase_price' => 7500, 'selling_price' => 11500,
                'variants' => null,
            ],
            [
                'name' => 'Beras 5kg',
                'category' => 'Sembako', 'unit' => 'Pcs',
                'purchase_price' => 55000, 'selling_price' => 68000,
                'variants' => null,
            ],
            [
                'name' => 'Beras 25kg',
                'category' => 'Sembako', 'unit' => 'Pcs',
                'purchase_price' => 250000, 'selling_price' => 310000,
                'variants' => null,
            ],
            [
                'name' => 'Gula Pasir 1kg',
                'category' => 'Sembako', 'unit' => 'Pcs',
                'purchase_price' => 14000, 'selling_price' => 18000,
                'variants' => null,
            ],
            [
                'name' => 'Minyak Goreng 2L',
                'category' => 'Sembako', 'unit' => 'Botol',
                'purchase_price' => 28000, 'selling_price' => 36000,
                'variants' => null,
            ],
            [
                'name' => 'Telur Ayam 1kg',
                'category' => 'Sembako', 'unit' => 'Pcs',
                'purchase_price' => 22000, 'selling_price' => 28000,
                'variants' => null,
            ],
            [
                'name' => 'Kopi Saset 1 Renteng (isi 24)',
                'category' => 'Minuman', 'unit' => 'Pack',
                'purchase_price' => 15000, 'selling_price' => 22000,
                'variants' => ['Original', 'Susu'],
            ],
            [
                'name' => 'Teh Celup 1 Box (isi 100)',
                'category' => 'Minuman', 'unit' => 'Dus',
                'purchase_price' => 18000, 'selling_price' => 25000,
                'variants' => null,
            ],
            [
                'name' => 'Susu Kental Manis 1 Kaleng',
                'category' => 'Minuman', 'unit' => 'Kaleng',
                'purchase_price' => 10000, 'selling_price' => 14000,
                'variants' => null,
            ],
            [
                'name' => 'Kecap Manis 600ml',
                'category' => 'Bumbu & Saus', 'unit' => 'Botol',
                'purchase_price' => 15000, 'selling_price' => 22000,
                'variants' => null,
            ],
            [
                'name' => 'Saos Sambal 600ml',
                'category' => 'Bumbu & Saus', 'unit' => 'Botol',
                'purchase_price' => 13000, 'selling_price' => 20000,
                'variants' => null,
            ],
            [
                'name' => 'Sabun Mandi 1 Pack (isi 3)',
                'category' => 'Produk Rumah Tangga', 'unit' => 'Pack',
                'purchase_price' => 18000, 'selling_price' => 25000,
                'variants' => null,
            ],
            [
                'name' => 'Shampoo Saset 1 Pack (isi 12)',
                'category' => 'Produk Rumah Tangga', 'unit' => 'Pack',
                'purchase_price' => 8000, 'selling_price' => 12000,
                'variants' => null,
            ],
            [
                'name' => 'Deterjen Bubuk 1kg',
                'category' => 'Produk Rumah Tangga', 'unit' => 'Pcs',
                'purchase_price' => 16000, 'selling_price' => 23000,
                'variants' => null,
            ],
            [
                'name' => 'Sabun Cuci Piring 500ml',
                'category' => 'Produk Rumah Tangga', 'unit' => 'Botol',
                'purchase_price' => 10000, 'selling_price' => 15000,
                'variants' => null,
            ],
            [
                'name' => 'Pasta Gigi 190gr',
                'category' => 'Produk Rumah Tangga', 'unit' => 'Pcs',
                'purchase_price' => 12000, 'selling_price' => 17000,
                'variants' => null,
            ],
            [
                'name' => 'Rokok Kretek 1 Slop (isi 10)',
                'category' => 'Rokok', 'unit' => 'Slop',
                'purchase_price' => 180000, 'selling_price' => 220000,
                'variants' => null,
            ],
        ];

        $count = 0;

        foreach ($products as $data) {
            $product = Product::create([
                'store_id' => $storeId,
                'category_id' => $categories[$data['category']],
                'unit_id' => $units[$data['unit']],
                'name' => $data['name'],
                'slug' => Str::slug($data['name']),
                'description' => fake()->sentence(),
                'sku' => strtoupper(fake()->bothify('SKU-####')),
                'barcode' => fake()->ean13(),
                'purchase_price' => $data['purchase_price'],
                'selling_price' => $data['selling_price'],
                'has_variants' => $data['variants'] !== null,
                'is_active' => true,
            ]);

            if ($data['variants']) {
                foreach ($data['variants'] as $variantName) {
                    ProductVariant::create([
                        'product_id' => $product->id,
                        'name' => $variantName,
                        'sku' => strtoupper(fake()->bothify('SKU-####')),
                        'barcode' => fake()->ean13(),
                        'purchase_price' => $data['purchase_price'],
                        'selling_price' => $data['selling_price'],
                        'is_active' => true,
                    ]);
                }
            }

            $count++;
        }

        $this->command->info('  Products: '.$count);
        $this->command->info('  Product Variants: '.ProductVariant::count());
    }
}
