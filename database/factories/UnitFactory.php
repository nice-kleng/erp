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

        $unit = fake()->unique()->randomElement($units);

        return [
            'name' => $unit['name'],
            'abbreviation' => $unit['abbreviation'],
            'is_active' => true,
        ];
    }
}
