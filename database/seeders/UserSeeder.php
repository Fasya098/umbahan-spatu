<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Admin',
            'role_id' => 1,
            'email' => 'admin@gmail.com',
            'password' => bcrypt('12345678'),
            'phone' => '0812345678',
        ])->assignRole('admin');

        // User::create([
        //     'name' => 'Mitra',
        //     'role_id' => 1,
        //     'email' => 'mitra@gmail.com',
        //     'password' => bcrypt('12345678'),
        //     'phone' => '08123456789',
        // ])->assignRole('admin');

        User::create([
            'name' => 'Mitra',
            'role_id' => 2,
            'email' => 'mitra@gmail.com',
            'password' => bcrypt('12345678'),
            'phone' => '08123456789',
        ])->assignRole('mitra');
        
        User::create([
            'name' => 'Customer',
            'role_id' => 3,
            'email' => 'customer@gmail.com',
            'password' => bcrypt('12345678'),
            'phone' => '081234567',
        ])->assignRole('customer');
    }
}