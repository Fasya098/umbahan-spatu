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
use Illuminate\Support\Facades\Http;

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

        $response = Http::withHeaders([
            'api-key' => env('SENDINBLUE_API_KEY'),
            "Content-Type" => "application/json"
        ])->post('https://api.brevo.com/v3/smtp/email', [
            "sender" => [
                "name" => env('SENDINBLUE_SENDER_NAME'),
                "email" => env('SENDINBLUE_SENDER_EMAIL'),
            ],
            'to' => [
                ['email' => $user->email]
            ],
            "subject" => "FreshFT.Clean",
            "htmlContent" => "
                <div style='font-family: Arial, sans-serif; text-align: center; padding: 20px;'>
                    <h2 style='color: #333;'>Selamat Bergabung!</h2>
                    <p style='font-size: 16px; color: #555;'>
                        Selamat! Akun Anda telah berhasil dibuat. Kami senang menyambut Anda ke dalam komunitas kami.
                    </p>
                    <div style='display: inline-block; background: #f4f4f4; padding: 10px 20px; font-size: 20px; font-weight: bold; border-radius: 5px; margin: 10px 0;'>
                        Nikmati pengalaman terbaik bersama kami!
                    </div>
                    <p style='font-size: 14px; color: #777;'>
                        Jika Anda memiliki pertanyaan atau butuh bantuan, jangan ragu untuk menghubungi tim kami.
                    </p>
                    <hr style='border: none; border-top: 1px solid #ddd; margin: 20px 0;'>
                    <p style='font-size: 12px; color: #999;'>
                        Terima kasih telah bergabung! Kami harap Anda menikmati layanan kami.
                    </p>
                </div>
            "
        ]);

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

        $response = Http::withHeaders([
            'api-key' => env('SENDINBLUE_API_KEY'),
            "Content-Type" => "application/json"
        ])->post('https://api.brevo.com/v3/smtp/email', [
            "sender" => [
                "name" => env('SENDINBLUE_SENDER_NAME'),
                "email" => env('SENDINBLUE_SENDER_EMAIL'),
            ],
            'to' => [
                ['email' => $user->email]
            ],
            "subject" => "FreshFT.Clean",
            "htmlContent" => "
                <div style='font-family: Arial, sans-serif; text-align: center; padding: 20px;'>
                    <h2 style='color: #D9534F;'>Pendaftaran Ditolak</h2>
                    <p style='font-size: 16px; color: #555;'>
                        Maaf, pendaftaran Anda tidak dapat diproses karena terdapat kesalahan dalam data yang Anda masukkan.
                    </p>
                    <div style='display: inline-block; background: #f8d7da; padding: 10px 20px; font-size: 16px; font-weight: bold; border-radius: 5px; margin: 10px 0; color: #721c24;'>
                        Silakan periksa kembali data Anda dan coba daftar kembali.
                    </div>
                    <p style='font-size: 14px; color: #777;'>
                        Pastikan informasi yang Anda masukkan sudah benar dan sesuai. Jika butuh bantuan, hubungi tim dukungan kami.
                    </p>
                    <hr style='border: none; border-top: 1px solid #ddd; margin: 20px 0;'>
                    <p style='font-size: 12px; color: #999;'>
                        Jika ini adalah kesalahan, silakan coba lagi atau hubungi layanan pelanggan kami untuk mendapatkan bantuan lebih lanjut.
                    </p>
                </div>
            "
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Pendaftaran mitra ditolak',
            'user' => $user,
        ]);
    }
}
