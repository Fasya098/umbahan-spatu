<?php

namespace App\Http\Controllers;

use App\Models\Pesanan;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PesananController extends Controller
{
    public function index(Request $request)
    {
        $per = $request->per ?? 10;
        $page = $request->page ? $request->page - 1 : 0;

        DB::statement('set @no=0+' . $page * $per);
        $data = Pesanan::when($request->search, function (Builder $query, string $search) {
            $query->where('name', 'like', "%$search%")
                ->orWhere('email', 'like', "%$search%")
                ->orWhere('phone', 'like', "%$search%");
        })->latest()->paginate($per, ['*', DB::raw('@no := @no + 1 AS no')]);

        return response()->json($data);
    }

    public function store(Request $request)
{
    // Validasi data dari request
    $validatedData = $request->validate([
        'toko_id' => 'required|integer|exists:tokos,id',
        'tanggal_pesanan' => 'required|date',
        'status' => 'required|integer',
        'total_harga' => 'required|numeric',
        'sepatu' => 'required|array',
        'sepatu.*.layanan_id' => 'required|integer|exists:layanans,id',
        'sepatu.*.brand_sepatu' => 'required|string',
        'sepatu.*.warna_sepatu' => 'required|string',
        'sepatu.*.promo_id' => 'nullable|integer|exists:promos,id',
        'sepatu.*.foto_sepatu' => 'nullable',
    ]);

    // Simpan data pesanan
    $pesanan = Pesanan::create([
        'toko_id' => $validatedData['toko_id'],
        'tanggal_pesanan' => $validatedData['tanggal_pesanan'],
        'status' => $validatedData['status'],
        'total_harga' => $validatedData['total_harga'],
    ]);

    // Simpan masing-masing data sepatu
    foreach ($validatedData['sepatu'] as $sepatu) {
        $pesanan->sepatu()->create([
            'layanan_id' => $sepatu['layanan_id'],
            'promo_id' => $sepatu['promo_id'] ?? null,
            'foto_sepatu' => $sepatu['foto_sepatu'] ?? null,
            'brand_sepatu' => $sepatu['brand_sepatu'],
            'warna_sepatu' => $sepatu['warna_sepatu'],
        ]);
    }

    return response()->json(['message' => 'Pesanan berhasil disimpan!'], 201);
}

    
}
