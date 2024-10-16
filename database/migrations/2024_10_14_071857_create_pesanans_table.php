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
            $table->foreignId('tokos_id')->references('id')->on('tokos')->onDelete('cascade');
            $table->foreignId('layanans_id')->references('id')->on('layanans')->onDelete('cascade');
            $table->foreignId('promos_id')->references('id')->on('promos')->onDelete('cascade');
            $table->date('tanggal_pesanan');
            $table->string('status');
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
