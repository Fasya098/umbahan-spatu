<?php

namespace App\Http\Controllers;

use App\Models\Toko;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TokoController extends Controller
{
    public function index(Request $request)
    {
        $per = $request->per ?? 10;
        $page = $request->page ? $request->page - 1 : 0;

        DB::statement('set @no=0+' . $page * $per);
        $data = Toko::with(['user'])->when($request->search, function (Builder $query, string $search) {
            $query->where('nama_toko', 'like', "%$search%")
                ->orWhere('alamat', 'like', "%$search%")
                ->orWhere('phone', 'like', "%$search%")
                ->orWhereHas('user', function ($query) use ($search) {
                    $query->where('nama', 'like', "%$search%");
                });
        })->latest()->paginate($per, ['*', DB::raw('@no := @no + 1 AS no')]);

        return response()->json($data);
    }

    public function add(Request $request)
    {
        $request->validate([
            'nama_toko' => 'required',
            'deskripsi' => 'required',
            'alamat' => 'required',
            'nomor_telepon' => 'required',
            'foto_toko' => 'required|image', // Menambahkan validasi untuk file gambar
            'user_id' => 'required',
            'ongkir' => 'required',
        ]);

        // Mengambil data dari request
        $data = $request->all();

        // Menyimpan foto dan mengubah path sesuai kebutuhan
        $data['foto_toko'] = str_replace('public/', '', $request->file('foto_toko')->store('public/toko'));

        // Cek apakah data Toko sudah ada
        $toko = Toko::where('user_id', $data['user_id'])->first();

        if ($toko) {
            // Jika toko sudah ada, update data toko
            $toko->update($data);
            $message = 'Data toko berhasil diupdate';
        } else {
            // Jika toko belum ada, buat data toko baru
            $toko = Toko::create($data);
            $message = 'Data toko berhasil dibuat';
        }

        return response()->json([
            'status' => true,
            'message' => $message,
            'data' => $toko
        ]);
    }

    public function show()
    {
        return response()->json(Toko::all());
    }

    public function shiw($tokoUuid)
    {
        $data = Toko::where('uuid', $tokoUuid)->get();

        return response()->json($data);
    }

    public function shaw()
    {
        $data = Toko::with('user')->get();

        return response()->json($data);
    }

    public function edit($id)
    {
        $base = Toko::where('user_id', $id)->first();
        if ($base) {
            return response()->json([
                'data' => $base,
            ], 200);
        } else {
            return response()->json([
                'message' => "Data tidak ditemukan"
            ], 404);
        }
    }

    public function destroy($id)
    {
        $dokter = Toko::find($id);
        if ($dokter) {
            $dokter->delete();
            return response()->json([
                'message' => "Data telah dihapus",
                'code' => 200
            ]);
        } else {
            return response([
                'message' => "gagal menghapus $id / data tidak ditemukan"
            ]);
        }
    }

    public function update(Request $request, $id)
    {
        $base = Toko::find($id);
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

    public function get($id)
    {
        $data = Toko::where('user_id', $id)->first();

        if (!$data) {
            return response()->json([
                'success' => false,
                'message' => 'Toko tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }
}
