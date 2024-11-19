<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Role;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;

class MitraController extends Controller
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
        ]);

        // Berikan respon sukses atau redirect ke halaman lain
        return response()->json([
            'message' => 'User successfully created',
            'user' => $user,
        ], 201);
    }

    public function terima(Request $request) 
    {
        $user = User::find($request->user_id);
        $user->status = 3;
        $user->update();

        return response()->json([
            'status' => 'success',
            'message' => 'Mitra sudah aktif',
            'user' => $user,
        ]);
    }

    public function tolak(Request $request) 
    {
        $user = User::find($request->user_id);
        $user->status = 1;
        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Pendaftaran mitra ditolak',
            'user' => $user,
        ]);
    }
}
