<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Apartment;
use App\Models\ApartmentImage;
use Illuminate\Database\Seeder;

class DevelopmentSeeder extends Seeder
{
    public function run(): void
    {
        $ownerUsers = User::factory()->count(5)->create(['role' => 'owner', 'is_approved' => true]);
        User::factory()->count(15)->create(['role' => 'tenant', 'is_approved' => true]);

        $apartments = Apartment::factory()->count(20)->create();

        foreach ($apartments as $apartment) {
            $apartment->images()->createMany(
                ApartmentImage::factory()->count(rand(3, 5))->make()->toArray()
            );
        }
    }
}