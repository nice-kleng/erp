<?php

namespace Database\Factories;

use App\Models\ProductVariant;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductVariantFactory extends Factory
{
    protected $model = ProductVariant::class;

    public function definition(): array
    {
        return [
            'name' => fake()->randomElement(['Original', 'Susu', 'Ayam Bawang', 'Kari', 'Soto']),
            'sku' => strtoupper(fake()->bothify('SKU-####')),
            'barcode' => fake()->ean13(),
            'purchase_price' => fake()->numberBetween(5000, 50000),
            'selling_price' => fake()->numberBetween(10000, 100000),
            'is_active' => true,
        ];
    }
}
