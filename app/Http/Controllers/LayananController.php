<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Layanan;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class LayananController extends Controller
{
    public function dataLayanan(Request $request)
    {
        $per = $request->per ?? 10;
        $page = $request->page ? $request->page - 1 : 0;

        DB::statement('set @no=0+' . $page * $per);
        $data = Layanan::when($request->search, function (Builder $query, string $search) {
            $query->where('nama_layanan', 'like', "%$search%")
                ->orWhere('harga', 'like', "%$search%");
        })
            ->with(['user', 'referensiLayanan'])
            ->latest()
            ->paginate($per, ['*', DB::raw('@no := @no + 1 AS no')]);

        return response()->json($data);
    }

    public function index(Request $request)
    {
        $per = $request->per ?? 10;
        $page = $request->page ? $request->page - 1 : 0;
        $mitraId = $request->mitraId; // Ambil mitraId dari request

        DB::statement('set @no=0+' . $page * $per);

        $data = Layanan::with(['user', 'referensiLayanan'])
            ->where('user_id', $mitraId) // Filter berdasarkan mitraId
            ->when($request->search, function (Builder $query, string $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%$search%")
                        ->orWhere('email', 'like', "%$search%")
                        ->orWhere('phone', 'like', "%$search%");
                });
            })
            ->latest()
            ->paginate($per, ['*', DB::raw('@no := @no + 1 AS no')]);

        return response()->json($data);
    }


    public function store(Request $request)
    {
        // Validasi data input
        $request->validate([
            'user_id' => 'required',
            'referensi_layanan_id' => 'required',
            'harga' => 'required',
        ]);

        // Buat user baru dengan role_id = 2
        $user = Layanan::create([
            'user_id' => $request->user_id,
            'referensi_layanan_id' => $request->referensi_layanan_id,
            'harga' => $request->harga,
        ]);

        // Berikan respon sukses atau redirect ke halaman lain
        return response()->json([
            'message' => 'Layanan successfully created',
            'user' => $user,
        ], 201);
    }

    public function get()
    {
        return response()->json(['data' => Layanan::all()]);
    }

    public function destroy($id)
    {
        $base = Layanan::find($id);
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
        $ReferensiLayanan = Layanan::find($id);
        return response()->json([
            'success' => true,
            'data' => $ReferensiLayanan
        ]);
    }

    public function update(Request $request, $id)
    {
        $base = Layanan::find($id);
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
