<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Sale;
use Illuminate\Database\Eloquent\Factories\Factory;

class SaleItemFactory extends Factory
{
    public function definition(): array
    {
        $qty = fake()->randomFloat(0, 1, 20);
        $unitPrice = fake()->randomFloat(2, 1000, 500000);
        $discount = fake()->optional(0.2, 0)->randomFloat(2, 0, $qty * $unitPrice * 0.1);

        return [
            'sale_id' => Sale::factory(),
            'product_id' => Product::factory(),
            'qty' => $qty,
            'unit_price' => $unitPrice,
            'discount' => $discount,
            'subtotal' => ($qty * $unitPrice) - $discount,
        ];
    }
}
