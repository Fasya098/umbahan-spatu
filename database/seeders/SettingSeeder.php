<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('settings')->truncate();

        Setting::create([
            'app' => 'Umbahan-Spatu',
            'description' =>  'Aplikasi Cuci Sepatu',
            'logo' =>  '/media/logo-spatu-nobackground.png',
            'bg_auth' =>  '/media/misc/bg sepatu.jpg',
            'banner' =>  '/media/misc/bg sepatu.jpg',
            'alamat' =>  'Pondok Benowo Indah',
            'telepon' =>  '08912345678',
            'email' =>  'admin@umbahan.com',
        ]);
    }
}
