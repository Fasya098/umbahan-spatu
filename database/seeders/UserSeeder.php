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
            'email' => 'admin@gmail.com',
            'password' => bcrypt('12345678'),
            'phone' => '0812345678',
        ])->assignRole('admin');
        User::create([
            'name' => 'Mitra',
            'email' => 'mitra@gmail.com',
            'password' => bcrypt('12345678'),
            'phone' => '08123456789',
        ])->assignRole('mitra');
        User::create([
            'name' => 'Customer',
            'email' => 'customer@gmail.com',
            'password' => bcrypt('12345678'),
            'phone' => '081234567',
        ])->assignRole('customer');
    }
}