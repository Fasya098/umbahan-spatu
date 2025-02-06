<?php

namespace App\Http\Controllers;

use App\Models\Layanan;
use App\Models\Promo;
use App\Models\Toko;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserpageController extends Controller
{
    public function store(Request $request)
    {
        // Validasi data input
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|max:15',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Buat user baru dengan role_id = 2
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role_id' => 2,
            'status' => '2',
        ])->assignRole('mitra');

        // Berikan respon sukses atau redirect ke halaman lain
        return response()->json([
            'message' => 'User successfully created',
            'user' => $user,
        ], 201);
    }

    public function show()
    {
        $data = toko::all();
        $layanan = Layanan::all();

        // Filter data toko berdasarkan user_id yang ada pada layanan
        $filterToko = $data->filter(function ($toko) use ($layanan) {
            return $layanan->contains('user_id', $toko->user_id);
        });

        return response()->json($filterToko);
    }

    public function shiw($uuid)
    {
        $data = Toko::where('uuid', $uuid)->get();

        return response()->json($data);
    }

    public function shaw($userId)
    {
        $data = Layanan::with(['User', 'ReferensiLayanan'])->where('user_id', $userId)->get();

        return response()->json($data);
    }

    public function ahay($uuid)
    {
        $data = Toko::findByUuid($uuid);
        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    public function register(Request $request)
    {
        // Validasi data input
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|max:15',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Buat user baru dengan role_id = 2
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role_id' => 3,
            'status' => '2',
        ])->assignRole('customer');

        // Berikan respon sukses atau redirect ke halaman lain
        return response()->json([
            'message' => 'User successfully created',
            'user' => $user,
        ], 201);
    }
}
