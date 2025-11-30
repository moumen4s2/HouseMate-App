<?php

namespace Database\Factories;

use App\Models\ApartmentImage;
use Illuminate\Database\Eloquent\Factories\Factory;

class ApartmentImageFactory extends Factory
{
    protected $model = ApartmentImage::class;

    public function definition(): array
    {
        return [
            'url' => 'apartments/' . fake()->uuid() . '.jpg',
            'is_main' => fake()->boolean(20),
        ];
    }
}