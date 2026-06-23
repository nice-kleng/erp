<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $name = fake()->unique()->randomElement([
            'Kopi Hitam', 'Kopi Susu', 'Cappuccino', 'Espresso',
            'Matcha Latte', 'Coklat Panas', 'Kopi Tubruk',
            'Nasi Goreng', 'Mie Ayam', 'Ayam Geprek',
            'Nasi Putih', 'French Fries', 'Onion Ring',
            'Cheesecake', 'Pisang Goreng', 'Lumpia',
            'Air Mineral', 'Susu Segar', 'Jus Jeruk', 'Jus Alpukat',
            'Es Teh Manis', 'Es Jeruk', 'Teh Tarik',
            'Bubble', 'Nata de Coco', 'Grass Jelly',
            'Paket Nasi + Minum', 'Paket Snack + Minum',
        ]);

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => fake()->sentence(),
            'sku' => strtoupper(fake()->bothify('SKU-####')),
            'barcode' => fake()->ean13(),
            'purchase_price' => fake()->numberBetween(3000, 30000),
            'selling_price' => fake()->numberBetween(7000, 75000),
            'has_variants' => false,
            'is_active' => true,
        ];
    }
}
