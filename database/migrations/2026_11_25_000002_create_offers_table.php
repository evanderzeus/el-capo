<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('offers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('barang_tujuan_id')->constrained('items')->cascadeOnDelete();
            $table->foreignId('barang_penawaran_id')->constrained('items')->cascadeOnDelete();
            $table->foreignId('pengaju_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('pemilik_id')->constrained('users')->cascadeOnDelete();
            $table->string('status_penawaran')->default('Menunggu')->index(); // Menunggu | Diterima | Ditolak
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('offers');
    }
};
