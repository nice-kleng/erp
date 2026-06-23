<?php

namespace Database\Factories;

use App\Models\Unit;
use Illuminate\Database\Eloquent\Factories\Factory;

class UnitFactory extends Factory
{
    protected $model = Unit::class;

    public function definition(): array
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

        $unit = fake()->unique()->randomElement($units);

        return [
            'name' => $unit['name'],
            'abbreviation' => $unit['abbreviation'],
            'is_active' => true,
        ];
    }
}
