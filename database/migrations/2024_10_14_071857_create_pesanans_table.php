<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pesanans', function (Blueprint $table) {
            $table->id();
            $table->uuid()->unique();
            $table->foreignId('toko_id')->references('id')->on('tokos')->onDelete('cascade');
            $table->foreignId('layanan_id')->references('id')->on('layanans')->onDelete('cascade');
            $table->foreignId('promo_id')->nullable()->references('id')->on('promos')->onDelete('cascade');
            $table->string('foto_sepatu');
            $table->string('brand_sepatu');
            $table->string('warna_sepatu');
            $table->date('tanggal_pesanan');
            $table->enum('status', ['1', '2', '3'])->comment('1=Penjemputan, 2=Pengerjaan, 3=pengiriman');
            $table->double('total_harga');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pesanans');
    }
};
