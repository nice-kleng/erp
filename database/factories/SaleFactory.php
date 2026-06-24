<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Store;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class SaleFactory extends Factory
{
    public function definition(): array
    {
        $subtotal = fake()->randomFloat(2, 10000, 1000000);
        $discount = fake()->optional(0.3, 0)->randomFloat(2, 0, $subtotal * 0.2);
        $tax = fake()->optional(0.5, 0)->randomFloat(2, 0, $subtotal * 0.11);
        $total = $subtotal - $discount + $tax;

        return [
            'store_id' => Store::factory(),
            'user_id' => User::factory(),
            'customer_id' => fake()->optional(0.6, null)->randomElement(Customer::pluck('id')),
            'invoice_number' => 'INV-'.now()->format('Ymd').'-'.strtoupper(fake()->bothify('####')),
            'subtotal' => $subtotal,
            'tax' => $tax,
            'discount' => $discount,
            'total' => $total,
            'payment_method' => fake()->randomElement(['cash', 'transfer', 'qris', 'debit']),
            'amount_paid' => $total,
            'change' => 0,
            'status' => 'completed',
        ];
    }
}
