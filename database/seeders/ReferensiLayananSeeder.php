<?php

namespace Database\Seeders;

use App\Models\ReferensiLayanan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ReferensiLayananSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ReferensiLayanan::create([
            'nama_layanan' => 'Sepatu Sporty',
        ]);
        ReferensiLayanan::create([
            'nama_layanan' => 'Sepatu Kulit',
        ]);
    }
}
