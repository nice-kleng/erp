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
            'Air Mineral 600ml 1 Dus (isi 24)', 'Air Mineral 1500ml 1 Dus (isi 12)',
            'Teh Kotak 1 Dus (isi 24)', 'Mi Instan 1 Dus (isi 40)',
            'Biskuit Roma Kelapa 1 Pack', 'Biskuit Roma Sandwich 1 Pack',
            'Beras 5kg', 'Beras 25kg', 'Gula Pasir 1kg',
            'Minyak Goreng 2L', 'Telur Ayam 1kg',
            'Kopi Saset 1 Renteng (isi 24)', 'Teh Celup 1 Box (isi 100)',
            'Susu Kental Manis 1 Kaleng',
            'Kecap Manis 600ml', 'Saos Sambal 600ml',
            'Sabun Mandi 1 Pack (isi 3)', 'Shampoo Saset 1 Pack (isi 12)',
            'Deterjen Bubuk 1kg', 'Sabun Cuci Piring 500ml', 'Pasta Gigi 190gr',
        ]);

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => fake()->sentence(),
            'sku' => strtoupper(fake()->bothify('SKU-####')),
            'barcode' => fake()->ean13(),
            'purchase_price' => fake()->numberBetween(5000, 150000),
            'selling_price' => fake()->numberBetween(10000, 200000),
            'has_variants' => false,
            'is_active' => true,
        ];
    }
}
