<?php

namespace Database\Seeders;

use App\Models\Toko;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TokoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Toko::create([
            'user_id' => '2',
            'nama_toko' => 'Toko Pastur',
            'foto_toko' => 'foto_sepatu/6p57ifKbe7THp85mT9CDh2yJ05tFLpdmFW6X3ega.jpg',
            'deskripsi' => 'Toko ini akan membantu anda untuk merawat baik itu mencuci menyemir mengelem dan berbagainya yang berhubungan dengan perawatan sepatu, kami akan merawat sepatu keluarga anda',
            'alamat' => 'PBI Blok N-13',
            'nomor_telepon' => '08934242843',
            'ongkir' => 3000,
        ]);
    }
}
