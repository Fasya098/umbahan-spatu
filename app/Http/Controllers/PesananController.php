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
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class PesananController extends Controller
{
    public function index(Request $request)
    {
        $per = $request->per ?? 10;
        $page = $request->page ? $request->page - 1 : 0;

        DB::statement('set @no=0+' . $page * $per);
        $data = Pesanan::with(['user', 'toko', 'layanan.ReferensiLayanan'])
            ->when($request->toko_id, function (Builder $query, $tokoId) {
                $query->where('toko_id', $tokoId);
            })
            ->when($request->search, function (Builder $query, string $search) {
                $query->whereHas('user', function (Builder $q) use ($search) {
                    $q->where('name', 'like', "%$search%");
                });
            })
            ->latest()
            ->paginate($per, ['*', DB::raw('@no := @no + 1 AS no')]);


        return response()->json($data);
    }

    public function dataPesanan(Request $request)
    {
        $per = $request->per ?? 10;
        $page = $request->page ? $request->page - 1 : 0;

        DB::statement('set @no=0+' . $page * $per);
        $data = Pesanan::with(['user', 'toko', 'layanan.ReferensiLayanan'])->when($request->search, function (Builder $query, string $search) {
            $query->where('name', 'like', "%$search%")
                ->orWhere('email', 'like', "%$search%")
                ->orWhere('phone', 'like', "%$search%");
        })->latest()->paginate($per, ['*', DB::raw('@no := @no + 1 AS no')]);

        return response()->json($data);
    }

    //     public function ahay($id, Request $request)
    // {
    //     try {
    //         $per = $request->per ?? 10;
    //         $page = $request->page ? $request->page - 1 : 0;

    //         DB::statement('set @no=0+' . $page * $per);

    //         $pesanan = Pesanan::with([
    //                 'user',
    //                 'toko',
    //                 'layanan.ReferensiLayanan'
    //             ])
    //             ->where('toko_id', $id)
    //             ->latest()
    //             ->paginate($per, ['*', DB::raw('@no := @no + 1 AS no')]);

    //         return response()->json([
    //             'success' => true,
    //             'data' => $pesanan
    //         ]);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Terjadi kesalahan saat mengambil data pesanan.',
    //             'error' => $e->getMessage()
    //         ], 500);
    //     }
    // }

    public function store(Request $request)
    {
        try {
            $date = Carbon::now()->toDateString();
            $kode = Str::upper(Str::random(8));

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
                    'kode_pesanan' => $kode,
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

    public function edit($uuid)
    {
        $pesanan = Pesanan::findByUuid($uuid);
        return response()->json([
            'success' => true,
            'data' => $pesanan
        ]);
    }

    public function get($id)
    {
        $data = Pesanan::with('user')->where('toko_id', $id)->get();
        $totalPesanan = $data->count();

        return response()->json([
            'success' => true,
            'data' => $data,
            'total_pesanan' => $totalPesanan,
        ]);
    }

    public function proses($id)
    {
        $data = Pesanan::where('toko_id', $id)
            ->where('status', 2)
            ->get();

        $totalPesanan = $data->count();

        return response()->json([
            'success' => true,
            'data' => $data,
            'total_pesanan' => $totalPesanan,
        ]);
    }

    public function selesai($id)
    {
        $data = Pesanan::where('toko_id', $id)
            ->where('status', 3)
            ->get();

        $totalPesanan = $data->count();

        return response()->json([
            'success' => true,
            'data' => $data,
            'total_pesanan' => $totalPesanan,
        ]);
    }

    public function destroy($uuid)
    {
        $base = Pesanan::findByUuid($uuid);
        if ($base) {
            $base->delete();
            return response()->json([
                'message' => "Data successfully deleted",
                'code' => 200
            ]);
        } else {
            return response([
                'message' => "Failed delete data $uuid / data doesn't exists"
            ]);
        }
    }

    public function terima($uuid)
    {
        // Ambil pesanan dengan relasi user
        $pesanan = Pesanan::with('user')->where('uuid', $uuid)->firstOrFail();

        // Pastikan user ada
        if (!$pesanan->user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User tidak ditemukan untuk pesanan ini',
            ], 404);
        }

        $pesananSemua = Pesanan::where('kode_pesanan', $pesanan->kode_pesanan)->get();

        // Ubah status pesanan menjadi diterima (2)
        foreach ($pesananSemua as $p) {
            $p->status = 2;
            $p->update();
        }

        // Kirim email ke user
        $response = Http::withHeaders([
            'api-key' => env('SENDINBLUE_API_KEY'),
            "Content-Type" => "application/json"
        ])->post('https://api.brevo.com/v3/smtp/email', [
            "sender" => [
                "name" => env('SENDINBLUE_SENDER_NAME'),
                "email" => env('SENDINBLUE_SENDER_EMAIL'),
            ],
            'to' => [
                ['email' => $pesanan->user->email]
            ],
            "subject" => "FreshFT.Clean - Sepatu Sedang Dijemput",
            "htmlContent" => "
                <div style='font-family: Arial, sans-serif; text-align: center; padding: 20px;'>
                    <h2 style='color: #333;'>Pesanan Anda Diterima</h2>
                    <p style='font-size: 16px; color: #555;'>
                        Sepatu Anda sedang dalam proses penjemputan. Harap siapkan sepatu Anda untuk diambil oleh kurir kami.
                    </p>
                    <div style='display: inline-block; background: #f4f4f4; padding: 10px 20px; font-size: 18px; font-weight: bold; border-radius: 5px; margin: 10px 0;'>
                        Terima kasih telah menggunakan layanan kami!
                    </div>
                    <p style='font-size: 14px; color: #777;'>
                        Jika Anda memiliki pertanyaan, jangan ragu untuk menghubungi tim kami.
                    </p>
                    <hr style='border: none; border-top: 1px solid #ddd; margin: 20px 0;'>
                    <p style='font-size: 12px; color: #999;'>
                        FreshFT.Clean - Layanan cuci sepatu terbaik untuk Anda.
                    </p>
                </div>
            "
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Pesanan diterima dan email telah dikirim ke pelanggan',
            'data' => $pesanan,
        ]);
    }

    public function tolak($uuid)
    {
        // Ambil pesanan dengan relasi user
        $pesanan = Pesanan::with('user')->where('uuid', $uuid)->firstOrFail();

        // Pastikan user ada
        if (!$pesanan->user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User tidak ditemukan untuk pesanan ini',
            ], 404);
        }

        $pesananSemua = Pesanan::where('kode_pesanan', $pesanan->kode_pesanan)->get();

        foreach ($pesananSemua as $p) {
            $p->delete();
        }

        // Kirim email ke user
        $response = Http::withHeaders([
            'api-key' => env('SENDINBLUE_API_KEY'),
            "Content-Type" => "application/json"
        ])->post('https://api.brevo.com/v3/smtp/email', [
            "sender" => [
                "name" => env('SENDINBLUE_SENDER_NAME'),
                "email" => env('SENDINBLUE_SENDER_EMAIL'),
            ],
            'to' => [
                ['email' => $pesanan->user->email]
            ],
            "subject" => "FreshFT.Clean - Data sepatu tidak valid",
            "htmlContent" => "
                <div style='font-family: Arial, sans-serif; text-align: center; padding: 20px;'>
                    <h2 style='color: #333;'>Pesanan Anda Ditolak</h2>
                    <p style='font-size: 16px; color: #555;'>
                        Data pesanan tidak valid. Harap melakukan pemesanan lagi.
                    </p>
                    <div style='display: inline-block; background: #f4f4f4; padding: 10px 20px; font-size: 18px; font-weight: bold; border-radius: 5px; margin: 10px 0;'>
                        Terima kasih telah menggunakan layanan kami!
                    </div>
                    <p style='font-size: 14px; color: #777;'>
                        Jika Anda memiliki pertanyaan, jangan ragu untuk menghubungi tim kami.
                    </p>
                    <hr style='border: none; border-top: 1px solid #ddd; margin: 20px 0;'>
                    <p style='font-size: 12px; color: #999;'>
                        FreshFT.Clean - Layanan cuci sepatu terbaik untuk Anda.
                    </p>
                </div>
            "
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Pesanan ditolak dan email telah dikirim ke pelanggan',
            'data' => $pesanan,
        ]);
    }

    public function kirim($uuid)
    {
        $pesanan = Pesanan::with('user')->where('uuid', $uuid)->firstOrFail();

        // Pastikan user ada
        if (!$pesanan->user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User tidak ditemukan untuk pesanan ini',
            ], 404);
        }

        $pesanan->status = 3;
        $pesanan->update();

        $response = Http::withHeaders([
            'api-key' => env('SENDINBLUE_API_KEY'),
            "Content-Type" => "application/json"
        ])->post('https://api.brevo.com/v3/smtp/email', [
            "sender" => [
                "name" => env('SENDINBLUE_SENDER_NAME'),
                "email" => env('SENDINBLUE_SENDER_EMAIL'),
            ],
            'to' => [
                ['email' => $pesanan->user->email]
            ],
            "subject" => "FreshFT.Clean - Sepatu Sedang Diantar",
            "htmlContent" => "
                <div style='font-family: Arial, sans-serif; text-align: center; padding: 20px;'>
                    <h2 style='color: #333;'>Pesanan Anda Diantar</h2>
                    <p style='font-size: 16px; color: #555;'>
                        Sepatu Anda sedang dalam proses pengantaran. Harap bersiap untuk menerima pesanan dari kurir kami.
                    </p>
                    <div style='display: inline-block; background: #f4f4f4; padding: 10px 20px; font-size: 18px; font-weight: bold; border-radius: 5px; margin: 10px 0;'>
                        Terima kasih telah menggunakan layanan kami!
                    </div>
                    <p style='font-size: 14px; color: #777;'>
                        Jika Anda memiliki pertanyaan, jangan ragu untuk menghubungi tim kami.
                    </p>
                    <hr style='border: none; border-top: 1px solid #ddd; margin: 20px 0;'>
                    <p style='font-size: 12px; color: #999;'>
                        FreshFT.Clean - Layanan cuci sepatu terbaik untuk Anda.
                    </p>
                </div>
            "
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Pesanan sedang Diantar dan email telah dikirim ke pelanggan',
            'data' => $pesanan,
        ]);
    }

    public function cekPesanan($kode_pesanan)
    {
        // Ambil semua data pesanan berdasarkan kode_pesanan
        $pesananList = Pesanan::where('kode_pesanan', $kode_pesanan)->get();

        if ($pesananList->isEmpty()) {
            return response()->json([
                'message' => 'Pesanan tidak ditemukan'
            ], 404);
        }

        // Format data pesanan
        $pesananData = $pesananList->map(function ($pesanan) {
            $statusText = match ($pesanan->status) {
                1 => 'Pesanan dalam proses penjemputan',
                2 => 'Pesanan sedang diproses',
                3 => 'Pesanan dalam proses pengiriman',
                default => 'Status tidak diketahui',
            };

            return [
                'user_id' => $pesanan->user_id,
                'toko_id' => $pesanan->toko_id,
                'layanan_id' => $pesanan->layanan_id,
                'foto_sepatu' => $pesanan->foto_sepatu,
                'brand_sepatu' => $pesanan->brand_sepatu,
                'warna_sepatu' => $pesanan->warna_sepatu,
                'tanggal_pesanan' => $pesanan->tanggal_pesanan,
                'status' => $pesanan->status,
                'total_harga' => $pesanan->total_harga,
                'kode_pesanan' => $pesanan->kode_pesanan,
            ];
        });

        return response()->json($pesananData);
    }
}
