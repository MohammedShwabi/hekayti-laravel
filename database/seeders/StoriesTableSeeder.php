<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         // Insert data of one story in level 1
         DB::table('stories')->insert([
            [
                'name' => 'الأشكال الملونة',
                'cover_photo' => '1_5_sc.jpg',
                'author' => 'ليان علي',
                'level' => 1,
                'story_order' => 1,
                'required_stars' => 0,
                'published' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
