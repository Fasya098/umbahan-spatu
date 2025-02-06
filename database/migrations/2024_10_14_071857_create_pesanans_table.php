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
            $table->foreignId('user_id')->nullable()->references('id')->on('users')->onDelete('cascade');
            $table->foreignId('toko_id')->nullable()->references('id')->on('tokos')->onDelete('cascade');
            $table->foreignId('layanan_id')->nullable()->references('id')->on('layanans')->onDelete('cascade');
            $table->string('foto_sepatu')->nullable();
            $table->string('brand_sepatu')->nullable();
            $table->string('warna_sepatu')->nullable();
            $table->date('tanggal_pesanan')->nullable();
            $table->enum('status', ['1', '2', '3'])->nullable()->comment('1=Penjemputan, 2=Pengerjaan, 3=pengiriman');
            $table->double('total_harga')->nullable();
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
