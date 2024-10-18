<?php

namespace App\Http\Controllers;

use App\Models\Toko;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
class TokoController extends Controller
{
    public function index(Request $request)
    {
        $per = $request->per ?? 10;
        $page = $request->page ? $request->page - 1 : 0;

        DB::statement('set @no=0+' . $page * $per);
        $data = Toko::when($request->search, function (Builder $query, string $search) {
            $query->where('nama_toko', 'like', "%$search%")
                ->orWhere('alamat', 'like', "%$search%")
                ->orWhere('phone', 'like', "%$search%");
        })->latest()->paginate($per, ['*', DB::raw('@no := @no + 1 AS no')]);

        return response()->json($data);
    }

    public function add (Request $request) {
        $request->validate([
            'nama_toko' => 'required',
            'deskripsi' => 'required',
            'alamat' => 'required',
            'nomor_telepon' => 'required',
            'foto_toko' => 'required',
            'user_id' => 'required',
        ]);

        $base = Toko::create ([
            'nama_toko' => $request->input('nama_toko'),
            'user_id' => $request->input('user_id'),
            'foto_toko' => str_replace('public/', '', $request->file('foto_toko')->store('public/toko')),
            'deskripsi' => $request->input('deskripsi'),
            'alamat' => $request->input('alamat'),
            'nomor_telepon' => $request->input('nomor_telepon'),
        ]);

        return response()->json([
            'status' => true,
            'message' => 'telah disimpan',
            'data' => $base
        ]);
    }

    public function show () {
        $users = Toko::all();
        return response()->json($users);
    }

    public function edit($id)
    {
        $base = Toko::find($id);
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

}
