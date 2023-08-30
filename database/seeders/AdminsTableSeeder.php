<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $names = [
            'محمد شوابي',
            'هسام نعمان',
            'عبده الحرازي',
            'اسامة الكولي',
            'مريم حاجب',
        ];
        // Insert your data for 'admins' table
        for ($i = 0; $i < 5; $i++) {
            DB::table('admins')->insert([
                'name' => $names[$i],
                'image' => 'admin_' . $i . '.jpg',
                'email' => ($i == 0) ?'admin@example.com' : 'manager'.($i+1).'@example.com',
                'password' => Hash::make(($i == 0) ? 'adminpassword' : 'managerpassword'),
                'role' => ($i == 0) ?'admin' : 'manager',
                'locked' => ($i == 0) ? 1 : 0,
            ]);
        }
    }
}
