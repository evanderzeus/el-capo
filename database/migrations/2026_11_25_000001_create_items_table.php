<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('nama_barang');
            $table->text('deskripsi');
            $table->string('kondisi');
            $table->unsignedInteger('harga_min')->default(0);
            $table->unsignedInteger('harga_max')->default(0);
            $table->string('foto')->nullable();
            $table->string('status')->default('tersedia')->index(); // tersedia | ditukar
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
