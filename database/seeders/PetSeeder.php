<?php

namespace Database\Seeders;

use App\Models\Pet;
use App\Models\User;
use Illuminate\Database\Seeder;

class PetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure we have users before seeding pets
        if (User::count() == 0) {
            User::factory(10)->create(); // Create 10 users first
        }

        // Create pets and associate them with users
        Pet::factory(20000)->create();
    }
}