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
        $data = Pesanan::with(['user', 'toko', 'layanan.ReferensiLayanan'])->
        when($request->search, function (Builder $query, string $search) {
            $query->where('name', 'like', "%$search%")
                ->orWhere('email', 'like', "%$search%")
                ->orWhere('phone', 'like', "%$search%");
        })->latest()->paginate($per, ['*', DB::raw('@no := @no + 1 AS no')]);

        return response()->json($data);
    }

    public function store(Request $request)
    {
        try {
            $date = Carbon::now()->toDateString();

            foreach ($request->input('inputs') as $index => $input) {
                $foto_sepatu = null;

                if ($request->hasFile("inputs.{$index}.foto_sepatu")) {
                    $file = $request->file("inputs.{$index}.foto_sepatu");
                    $foto_sepatu = str_replace('public/', '', $file->store('public/foto_sepatu'));
                }

                Pesanan::create([
                    'toko_id' => $input['toko_id'],
                    'user_id' => $input['user_id'],
                    'tanggal_pesanan' => $date,
                    'foto_sepatu' => $foto_sepatu,
                    'brand_sepatu' => $input['brand_sepatu'],
                    'warna_sepatu' => $input['warna_sepatu'],
                    'layanan_id' => $input['layanan_id'],
                    'total_harga' => $input['total_harga'],
                    'status' => 1,
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

}
