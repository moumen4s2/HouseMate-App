<?php

namespace Database\Factories;

use App\Models\Apartment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ApartmentFactory extends Factory
{
    protected $model = Apartment::class;

    public function definition(): array
    {
        $owner = User::where('role', 'owner')->inRandomOrder()->first();

        return [
            'owner_id' => $owner->id ?? User::factory()->state(['role' => 'owner', 'is_approved' => true])->create()->id, 
            'title' => fake()->sentence(3),
            'description' => fake()->paragraph(),
            'city' => fake()->randomElement(['دمشق', 'حلب', 'حمص', 'اللاذقية']),
            'province' => fake()->word(),
            'address' => fake()->address(),
            'price' => fake()->numberBetween(100, 1500),
            'rooms' => fake()->numberBetween(1, 5),
            'guests' => fake()->numberBetween(1, 10),
            'is_active' => true,
        ];
    }
}