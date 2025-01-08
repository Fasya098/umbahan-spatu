<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Promo;
use App\Models\Toko;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class PromoController extends Controller
{
    public function index(Request $request)
    {
        $per = $request->per ?? 10;
        $page = $request->page ? $request->page - 1 : 0;

        DB::statement('set @no=0+' . $page * $per);
        $data = Promo::when($request->search, function (Builder $query, string $search) {
            $query->where('nama_layanan', 'like', "%$search%")
                ->orWhere('harga', 'like', "%$search%");
        })
        ->with(['Toko'])
        ->latest()
        ->paginate($per, ['*', DB::raw('@no := @no + 1 AS no')]);

        return response()->json($data);
    }

    public function store(Request $request)
    {
        // Validasi data input
        $request->validate([
            'toko_id' => 'required',
            'nama_promo' => 'required',
            'harga' => 'required',
        ]);

        $user = Promo::create([
            'toko_id' => $request->toko_id,
            'nama_promo' => $request->nama_promo,
            'harga' => $request->harga,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_berakhir' => $request->tanggal_berakhir,
        ]);

        // Berikan respon sukses atau redirect ke halaman lain
        return response()->json([
            'message' => 'Promo successfully created',
            'data' => $user,
        ], 201);
    }

    public function get () {
        return response()->json(['data' => Toko::all()]);
    }

    public function destroy($id)
    {
        $base = Promo::find($id);
        if ($base) {
            $base->delete();
            return response()->json([
                'message' => "Data successfully deleted",
                'code' => 200
            ]);
        } else {
            return response([
                'message' => "Failed delete data $id / data doesn't exists"
            ]);
        }
    }

    public function edit($id)
    {
        $Promo = Promo::find($id);
        return response()->json([
            'success' => true,
            'data' => $Promo
        ]);
    }

    public function update(Request $request, $id)
    {
        $base = Promo::find($id);
        if ($base) {
            $base->update($request->all());

            return response()->json([
                'status' => 'true',
                'message' => 'data berhasil diubah'
            ]);
        } else {
            return response([
                'message' => 'gagal mengubah'
            ]);
        }
    }

    
}
