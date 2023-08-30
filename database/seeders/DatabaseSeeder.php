<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
// use App\Models\User;
use App\Models\Admin;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // add 20 user
        User::factory(20)->create();

        // Call StoriesTableSeeder
        $this->call(AdminsTableSeeder::class);

        // Call StoriesTableSeeder
        $this->call(StoriesTableSeeder::class);

        // Call StoriesMediaTableSeeder
        $this->call(StoriesMediaTableSeeder::class);
    }
}
