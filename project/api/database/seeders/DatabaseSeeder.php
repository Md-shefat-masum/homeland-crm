<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
            $this->call([
                UserSeeder::class,
                AddressSeeder::class,
                CustomerGroupSeeder::class,
                ProfessionSeeder::class,
                CustomerSeeder::class,
                LeadSourceSeeder::class,
                InterestedTypeSeeder::class,
            ]);
    }
}
