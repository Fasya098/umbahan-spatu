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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->uuid()->unique();
            $table->string('name');
            $table->foreignId('role_id')->nullable()->references('id')->on('roles')->onDelete('cascade');
            $table->string('email')->unique();
            $table->string('phone')->unique();
            $table->string('alamat')->nullable();
            $table->string('password');
            $table->string('otp_code')->nullable();
            $table->timestamp('otp_expired')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->enum('status', ['1', '2', '3'])->comment('1=gagal, 2=menunggu konfirmasi, 3=sukses');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
