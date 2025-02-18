<?php

namespace App\Http\Controllers;

use App\Models\Layanan;
use Carbon\Carbon;
use App\Models\Toko;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

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
            'alamat' => $request->alamat,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role_id' => 1,
            'status' => '2',
        ])->assignRole('admin');

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
        Log::info('OTP Check Request:', $request->all());

        // Validasi data input
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'name' => 'required',
            'alamat' => 'required',
            'phone' => 'required',
            'alamat' => 'required',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()], 422);
        }

        if (User::where('email', $request->email)
            ->where('is_verified', true)
            ->orWhere('phone', $request->phone)
            ->where('is_verified', true)
            ->exists()
        ) {
            return response()->json(['status' => false, 'message' => 'Email atau nomor telepon telah terdaftar'], 422);
        }

        User::where('email', $request->email)
            ->where('is_verified', false)
            ->delete();

        $otpCode = sprintf("%06d", mt_rand(1, 999999));

        // Buat user baru dengan role_id = 2
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'alamat' => $request->alamat,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role_id' => 3,
            'status' => '2',
            'otp_code' => $otpCode,
            'is_verified' => false,
            'otp_expired' => Carbon::now()->addMinutes(10),
        ])->assignRole('customer');

        $response = Http::withHeaders([
            'api-key' => env('SENDINBLUE_API_KEY'),
            "Content-Type" => "application/json"
        ])->post('https://api.brevo.com/v3/smtp/email', [
            "sender" => [
                "name" => env('SENDINBLUE_SENDER_NAME'),
                "email" => env('SENDINBLUE_SENDER_EMAIL'),
            ],
            'to' => [
                ['email' => $request->email]
            ],
            "subject" => "Kode OTP registrasi",
            "htmlContent" => "
                <div style='font-family: Arial, sans-serif; text-align: center; padding: 20px;'>
                    <h2 style='color: #333;'>Verifikasi Akun Anda</h2>
                    <p style='font-size: 16px; color: #555;'>
                        Terima kasih telah mendaftar. Gunakan kode OTP berikut untuk memverifikasi akun Anda:
                    </p>
                    <div style='display: inline-block; background: #f4f4f4; padding: 10px 20px; font-size: 24px; font-weight: bold; border-radius: 5px; margin: 10px 0;'>
                        {$otpCode}
                    </div>
                    <p style='font-size: 14px; color: #777;'>
                        Kode ini berlaku selama 10 menit. Jangan berikan kode ini kepada siapa pun.
                    </p>
                    <hr style='border: none; border-top: 1px solid #ddd; margin: 20px 0;'>
                    <p style='font-size: 12px; color: #999;'>
                        Jika Anda tidak merasa melakukan pendaftaran, abaikan email ini.
                    </p>
                </div>
            "
        ]);

        // Berikan respon sukses
        return response()->json([
            'status' => true,
            'message' => 'User successfully created and OTP sent',
        ], 201);
    }

    public function resendOtp(Request $request)
    {
        try {
            Log::info('Resend OTP request received', ['email' => $request->email]);
    
            $user = User::where('email', $request->email)
                ->where('is_verified', false)
                ->first();
    
            if (!$user) {
                Log::warning('User not found or already verified', ['email' => $request->email]);
                return response()->json(['status' => false, 'message' => 'User tidak ditemukan atau sudah terverifikasi'], 422);
            }
    
            Log::info('User ditemukan', ['email' => $user->email, 'otp_code' => $user->otp_code]);
    
            // Jika OTP masih berlaku, kirim ulang OTP ke email user
            if ($user->otp_expired > now()) {
                Log::info('OTP masih berlaku, mengirim ulang kode OTP', ['otp_code' => $user->otp_code]);
    
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
                    "subject" => "Kode OTP registrasi",
                    "htmlContent" => "
                    <div style='font-family: Arial, sans-serif; text-align: center; padding: 20px;'>
                        <h2 style='color: #333;'>Verifikasi Akun Anda</h2>
                        <p style='font-size: 16px; color: #555;'>
                            Terima kasih telah mendaftar. Gunakan kode OTP berikut untuk memverifikasi akun Anda:
                        </p>
                        <div style='display: inline-block; background: #f4f4f4; padding: 10px 20px; font-size: 24px; font-weight: bold; border-radius: 5px; margin: 10px 0;'>
                            {$user->otp_code}
                        </div>
                        <p style='font-size: 14px; color: #777;'>
                            Kode ini berlaku selama 10 menit. Jangan berikan kode ini kepada siapa pun.
                        </p>
                        <hr style='border: none; border-top: 1px solid #ddd; margin: 20px 0;'>
                        <p style='font-size: 12px; color: #999;'>
                            Jika Anda tidak merasa melakukan pendaftaran, abaikan email ini.
                        </p>
                    </div>
                "
                ]);
    
                if ($response->failed()) {
                    Log::error('Gagal mengirim email OTP', ['email' => $user->email, 'response' => $response->body()]);
                    return response()->json(['status' => false, 'message' => 'Gagal mengirim email OTP'], 500);
                }
    
                Log::info('OTP berhasil dikirim ulang', ['email' => $user->email]);
    
                return response()->json([
                    'status' => true,
                    'message' => 'Kode OTP masih berlaku, silakan cek email Anda'
                ], 200);
            }
    
            Log::info('OTP sudah kadaluarsa, membuat OTP baru');
    
            // Generate OTP baru jika sudah kadaluarsa
            $newOtp = rand(100000, 999999);
            $user->otp_code = $newOtp;
            $user->otp_expired = now()->addMinutes(10);
            $user->save();
    
            Log::info('OTP baru berhasil dibuat', ['email' => $user->email, 'new_otp' => $newOtp]);
    
            // Kirim ulang email dengan OTP baru
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
                "subject" => "Kode OTP registrasi",
                "htmlContent" => "
                <div style='font-family: Arial, sans-serif; text-align: center; padding: 20px;'>
                    <h2 style='color: #333;'>Verifikasi Akun Anda</h2>
                    <p style='font-size: 16px; color: #555;'>
                        Terima kasih telah mendaftar. Gunakan kode OTP berikut untuk memverifikasi akun Anda:
                    </p>
                    <div style='display: inline-block; background: #f4f4f4; padding: 10px 20px; font-size: 24px; font-weight: bold; border-radius: 5px; margin: 10px 0;'>
                        {$newOtp}
                    </div>
                    <p style='font-size: 14px; color: #777;'>
                        Kode ini berlaku selama 5 menit. Jangan berikan kode ini kepada siapa pun.
                    </p>
                    <hr style='border: none; border-top: 1px solid #ddd; margin: 20px 0;'>
                    <p style='font-size: 12px; color: #999;'>
                        Jika Anda tidak merasa melakukan pendaftaran, abaikan email ini.
                    </p>
                </div>
            "
            ]);
    
            if ($response->failed()) {
                Log::error('Gagal mengirim email OTP baru', ['email' => $user->email, 'response' => $response->body()]);
                return response()->json(['status' => false, 'message' => 'Gagal mengirim email OTP baru'], 500);
            }
    
            Log::info('OTP baru berhasil dikirim', ['email' => $user->email, 'otp_code' => $newOtp]);
    
            return response()->json([
                'status' => true,
                'message' => 'Kode OTP baru telah dikirim ke email Anda'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Terjadi kesalahan pada resendOtp', ['error' => $e->getMessage()]);
            return response()->json(['status' => false, 'message' => 'Terjadi kesalahan, coba lagi nanti'], 500);
        }
    }

    public function checkOtp(Request $request)
    {
        $user = User::where('email', $request->email)
            ->where('is_verified', false)
            ->first();

        if (!$user || $user->otp_code != $request->otp_code || $user->otp_expired < now()) {
            // Hapus user jika OTP expired
            if ($user && $user->otp_expired < now()) {
                $user->delete();
            }
            return response()->json(['status' => false, 'message' => 'Kode OTP tidak valid atau telah kadaluarsa'], 422);
        }

        $user->is_verified = true;
        $user->otp_code = null;
        $user->otp_expired = null;
        $user->save();

        return response()->json(['status' => true, 'message' => 'Registrasi berhasil', 'user' => $user]);
    }

    public function me()
    {
        $user = User::with(['toko'])->where('id', auth()->user()->id)->first();
        return response()->json([
            'user' => $user
        ]);
    }
}
