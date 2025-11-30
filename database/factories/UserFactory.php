<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        static $password;

        return [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'phone' => fake()->unique()->numerify('9########'), 
            'email' => fake()->unique()->safeEmail(),
            'password' => $password ??= Hash::make('password12345'), 
            'role' => fake()->randomElement(['tenant', 'owner']), 
            'is_approved' => true, 
            'email_verified_at' => now(),
            'date_of_birth' => fake()->date('Y-m-d', '2000-01-01'),
            'id_document_url' => 'documents/' . fake()->uuid() . '.jpg', 
            'avatar_url' => 'profiles/' . fake()->uuid() . '.jpg',
        ];
    }
}