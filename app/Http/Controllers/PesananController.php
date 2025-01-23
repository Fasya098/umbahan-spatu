<?php

namespace App\Http\Controllers;

use App\Models\Pesanan;
use App\Models\PesananDetail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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
        try {
            
            dd($request->file('foto_sepatu'));

            $date = Carbon::now()->day;

            foreach ($request->input('sepatuList') as $input) {
                Pesanan::create([
                    'toko_id' => $request->tokoId,
                    'tanggal_pesanan' => $date,
                    'foto_sepatu' => str_replace('public/', '', $request->file('foto_sepatu')->store('public/foto_sepatu')),
                    'brand_sepatu' => $input['brand_sepatu'],
                    'warna_sepatu' => $input['warna_sepatu'],
                    'status' => 1,
                    'total_harga' => 20000,
                ]);
            }

            return response()->json([
                'status' => true,
                'message' => 'Data telah disimpan',
            ]);
        } catch (ValidationException $e) {
            $errors = $e->validator->errors()->all();
            return response()->json([
                'status' => false,
                'message' => 'Gagal Menyimpan Data',
                'errors' => $errors
            ], 422);
        }
    }

    //     public function store(Request $request)
    // {
    //     // Validasi data dari request
    //     $validatedData = $request->validate([
    //         'toko_id' => 'required|integer|exists:tokos,id',
    //         'tanggal_pesanan' => 'required|date',
    //         'status' => 'required|integer',
    //         'total_harga' => 'required|numeric',
    //         'sepatu' => 'required|array',
    //         'sepatu.*.layanan_id' => 'required|integer|exists:layanans,id',
    //         'sepatu.*.brand_sepatu' => 'required|string',
    //         'sepatu.*.warna_sepatu' => 'required|string',
    //         'sepatu.*.promo_id' => 'nullable|integer|exists:promos,id',
    //         'sepatu.*.foto_sepatu' => 'nullable',
    //     ]);

    //     // Simpan data pesanan
    //     $pesanan = Pesanan::create([
    //         'toko_id' => $validatedData['toko_id'],
    //         'tanggal_pesanan' => $validatedData['tanggal_pesanan'],
    //         'status' => $validatedData['status'],
    //         'total_harga' => $validatedData['total_harga'],
    //     ]);

    //     // Simpan masing-masing data sepatu
    //     foreach ($validatedData['sepatu'] as $sepatu) {
    //         $pesanan->sepatu()->create([
    //             'layanan_id' => $sepatu['layanan_id'],
    //             'promo_id' => $sepatu['promo_id'] ?? null,
    //             'foto_sepatu' => $sepatu['foto_sepatu'] ?? null,
    //             'brand_sepatu' => $sepatu['brand_sepatu'],
    //             'warna_sepatu' => $sepatu['warna_sepatu'],
    //         ]);
    //     }

    //     return response()->json(['message' => 'Pesanan berhasil disimpan!'], 201);
    // }

    // public function store(Request $request)
    // {
    //     // Validate the incoming request
    //     $request->validate([
    //         'jumlah_sepatu' => 'required|integer|min:1|max:10',
    //         'toko_id' => 'required|exists:tokos,id',
    //         'tanggal_pesanan' => 'required|date',
    //         'sepatu' => 'required|array|min:1',
    //         'sepatu.*.brand_sepatu' => 'required|string|max:255',
    //         'sepatu.*.warna_sepatu' => 'required|string|max:255',
    //         'sepatu.*.layanan_id' => 'required|exists:layanans,id',
    //         'sepatu.*.promo_id' => 'nullable|exists:promos,id',
    //         'sepatu.*.foto_sepatu' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
    //     ]);

    //     try {

    //         // Create main pesanan record
    //         $pesanan = Pesanan::create([
    //             'user_id' => Auth::id(),
    //             'toko_id' => $request->toko_id,
    //             'jumlah_sepatu' => $request->jumlah_sepatu,
    //             'tanggal_pesanan' => $request->tanggal_pesanan,
    //             'status' => 'pending'
    //         ]);

    //         // Process each shoe in the order
    //         foreach ($request->sepatu as $sepatuData) {
    //             $fotoPath = null;
    //             if (isset($sepatuData['foto_sepatu']) && $sepatuData['foto_sepatu']) {
    //                 $fotoPath = $sepatuData['foto_sepatu']->store('public/sepatu-photos');
    //             }

    //             PesananDetail::create([
    //                 'pesanan_id' => $pesanan->id,
    //                 'foto_sepatu' => $fotoPath,
    //                 'brand_sepatu' => $sepatuData['brand_sepatu'],
    //                 'warna_sepatu' => $sepatuData['warna_sepatu'],
    //                 'layanan_id' => $sepatuData['layanan_id'],
    //                 'promo_id' => $sepatuData['promo_id'] ?? null
    //             ]);
    //         }

    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Pesanan berhasil disimpan',
    //             'data' => $pesanan->load('pesananDetails')
    //         ], 201);

    //     } catch (\Exception $e) {
    //         DB::rollBack();

    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Gagal menyimpan pesanan',
    //             'error' => $e->getMessage()
    //         ], 500);
    //     }
    // }

}
