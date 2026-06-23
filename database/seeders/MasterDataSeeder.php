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
            ['name' => 'Minuman', 'description' => 'Minuman dingin & panas'],
            ['name' => 'Makanan', 'description' => 'Makanan berat & ringan'],
            ['name' => 'Snack', 'description' => 'Camilan & gorengan'],
            ['name' => 'Topping', 'description' => 'Tambahan topping minuman'],
            ['name' => 'Bahan Baku', 'description' => 'Bahan baku produksi'],
            ['name' => 'Paket', 'description' => 'Paket hemat & combo'],
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
            ['name' => 'Porsi', 'abbreviation' => 'prs'],
            ['name' => 'Cup', 'abbreviation' => 'cup'],
            ['name' => 'Gelas', 'abbreviation' => 'gls'],
            ['name' => 'Kilogram', 'abbreviation' => 'kg'],
            ['name' => 'Gram', 'abbreviation' => 'gr'],
            ['name' => 'Liter', 'abbreviation' => 'ltr'],
            ['name' => 'Pack', 'abbreviation' => 'pack'],
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
            ['name' => 'PT Sumber Makmur', 'phone' => '021-5550001', 'email' => 'info@sumbermakmur.co.id', 'city' => 'Jakarta'],
            ['name' => 'CV Jaya Abadi', 'phone' => '021-5550002', 'email' => 'order@jayaabadi.co.id', 'city' => 'Jakarta'],
            ['name' => 'UD Boga Rasa', 'phone' => '022-5550003', 'email' => 'sales@bogarasa.com', 'city' => 'Bandung'],
            ['name' => 'PT Segar Sentosa', 'phone' => '031-5550004', 'email' => 'info@segarsentosa.co.id', 'city' => 'Surabaya'],
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
                'name' => 'Kopi Hitam',
                'category' => 'Minuman', 'unit' => 'Cup',
                'purchase_price' => 3000, 'selling_price' => 10000,
                'variants' => ['Reguler', 'Large'],
            ],
            [
                'name' => 'Kopi Susu',
                'category' => 'Minuman', 'unit' => 'Cup',
                'purchase_price' => 5000, 'selling_price' => 15000,
                'variants' => ['Reguler', 'Large'],
            ],
            [
                'name' => 'Cappuccino',
                'category' => 'Minuman', 'unit' => 'Cup',
                'purchase_price' => 7000, 'selling_price' => 20000,
                'variants' => ['Reguler', 'Large'],
            ],
            [
                'name' => 'Matcha Latte',
                'category' => 'Minuman', 'unit' => 'Cup',
                'purchase_price' => 8000, 'selling_price' => 22000,
                'variants' => ['Reguler', 'Large'],
            ],
            [
                'name' => 'Coklat Panas',
                'category' => 'Minuman', 'unit' => 'Cup',
                'purchase_price' => 5000, 'selling_price' => 18000,
                'variants' => null,
            ],
            [
                'name' => 'Es Teh Manis',
                'category' => 'Minuman', 'unit' => 'Gelas',
                'purchase_price' => 2000, 'selling_price' => 7000,
                'variants' => ['Reguler', 'Large'],
            ],
            [
                'name' => 'Jus Jeruk',
                'category' => 'Minuman', 'unit' => 'Gelas',
                'purchase_price' => 4000, 'selling_price' => 12000,
                'variants' => ['Reguler', 'Large'],
            ],
            [
                'name' => 'Air Mineral',
                'category' => 'Minuman', 'unit' => 'Gelas',
                'purchase_price' => 1000, 'selling_price' => 4000,
                'variants' => null,
            ],
            [
                'name' => 'Nasi Goreng',
                'category' => 'Makanan', 'unit' => 'Porsi',
                'purchase_price' => 8000, 'selling_price' => 25000,
                'variants' => null,
            ],
            [
                'name' => 'Mie Ayam',
                'category' => 'Makanan', 'unit' => 'Porsi',
                'purchase_price' => 6000, 'selling_price' => 20000,
                'variants' => null,
            ],
            [
                'name' => 'Ayam Geprek',
                'category' => 'Makanan', 'unit' => 'Porsi',
                'purchase_price' => 10000, 'selling_price' => 28000,
                'variants' => null,
            ],
            [
                'name' => 'Nasi Putih',
                'category' => 'Makanan', 'unit' => 'Porsi',
                'purchase_price' => 3000, 'selling_price' => 8000,
                'variants' => null,
            ],
            [
                'name' => 'French Fries',
                'category' => 'Snack', 'unit' => 'Porsi',
                'purchase_price' => 5000, 'selling_price' => 15000,
                'variants' => ['Small', 'Large'],
            ],
            [
                'name' => 'Onion Ring',
                'category' => 'Snack', 'unit' => 'Porsi',
                'purchase_price' => 5000, 'selling_price' => 15000,
                'variants' => null,
            ],
            [
                'name' => 'Pisang Goreng',
                'category' => 'Snack', 'unit' => 'Porsi',
                'purchase_price' => 3000, 'selling_price' => 10000,
                'variants' => null,
            ],
            [
                'name' => 'Cheesecake',
                'category' => 'Snack', 'unit' => 'Pcs',
                'purchase_price' => 8000, 'selling_price' => 25000,
                'variants' => null,
            ],
            [
                'name' => 'Bubble',
                'category' => 'Topping', 'unit' => 'Porsi',
                'purchase_price' => 2000, 'selling_price' => 5000,
                'variants' => null,
            ],
            [
                'name' => 'Nata de Coco',
                'category' => 'Topping', 'unit' => 'Porsi',
                'purchase_price' => 2000, 'selling_price' => 5000,
                'variants' => null,
            ],
            [
                'name' => 'Paket Nasi + Minum',
                'category' => 'Paket', 'unit' => 'Pack',
                'purchase_price' => 12000, 'selling_price' => 35000,
                'variants' => null,
            ],
            [
                'name' => 'Paket Snack + Minum',
                'category' => 'Paket', 'unit' => 'Pack',
                'purchase_price' => 10000, 'selling_price' => 30000,
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
                    $priceMultiplier = $variantName === 'Large' || $variantName === 'Jumbo' ? 1.3 : 1;
                    ProductVariant::create([
                        'product_id' => $product->id,
                        'name' => $variantName,
                        'sku' => strtoupper(fake()->bothify('SKU-####')),
                        'barcode' => fake()->ean13(),
                        'purchase_price' => (int) round($data['purchase_price'] * $priceMultiplier),
                        'selling_price' => (int) round($data['selling_price'] * $priceMultiplier),
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
