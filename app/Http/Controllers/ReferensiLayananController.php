<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ReferensiLayanan;
use App\Models\RequestLayanan;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ReferensiLayananController extends Controller
{
    public function index(Request $request)
    {
        $per = $request->per ?? 10;
        $page = $request->page ? $request->page - 1 : 0;

        DB::statement('set @no=0+' . $page * $per);
        $data = ReferensiLayanan::when($request->search, function (Builder $query, string $search) {
            $query->where('nama_layanan', 'like', "%$search%")
                ->orWhere('harga', 'like', "%$search%");
        })->latest()->paginate($per, ['*', DB::raw('@no := @no + 1 AS no')]);

        return response()->json($data);
    }

    public function store(Request $request)
    {
        // Validasi data input
        $request->validate([
            'nama_layanan' => 'required',
        ]);

        // Buat user baru dengan role_id = 2
        $user = ReferensiLayanan::create([
            'nama_layanan' => $request->nama_layanan,
        ]);

        // Berikan respon sukses atau redirect ke halaman lain
        return response()->json([
            'message' => 'Layanan successfully created',
            'user' => $user,
        ], 201);
    }

    public function get()
    {
        return response()->json(['data' => ReferensiLayanan::all()]);
    }

    public function destroy($id)
    {
        $base = ReferensiLayanan::find($id);
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
        $ReferensiLayanan = ReferensiLayanan::find($id);
        return response()->json([
            'success' => true,
            'data' => $ReferensiLayanan
        ]);
    }

    public function update(Request $request, $id)
    {
        $base = ReferensiLayanan::find($id);
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

    public function terima(Request $request)
    {
        $validatedData = $request->validate([
            'request_nama_layanan' => 'required|string',
        ]);

        // Membuat data baru di ReferensiLayanan
        $layanan = ReferensiLayanan::create([
            'nama_layanan' => $validatedData['request_nama_layanan'],
        ]);

        // Mencari dan menghapus data pada RequestLayanan yang memiliki nama layanan yang diterima
        $requestLayanan = RequestLayanan::where('request_nama_layanan', $validatedData['request_nama_layanan'])->first();

        if ($requestLayanan) {
            $requestLayanan->delete();
        }

        return response()->json(['message' => 'Layanan diterima dan data request dihapus', 'data' => $layanan], 200);
    }

    public function tolak(Request $request)
    {
        $validatedData = $request->validate([
            'request_nama_layanan' => 'required|string',
        ]);

        $requestLayanan = RequestLayanan::where('request_nama_layanan', $validatedData['request_nama_layanan'])->first();
        if ($requestLayanan) {
            $requestLayanan->delete();
        }

        return response()->json([
            'status' => 'success'
        ]);
    }
}
