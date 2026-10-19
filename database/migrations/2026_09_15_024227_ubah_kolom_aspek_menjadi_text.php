<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Mengubah kolom menjadi TEXT agar bisa menampung paragraf panjang
        Schema::table('aspeks', function (Blueprint $table) {
            $table->text('nama_aspek')->change();
        });

        Schema::table('sub_aspeks', function (Blueprint $table) {
            $table->text('nama_sub_aspek')->change();
        });

        Schema::table('sub_sub_aspeks', function (Blueprint $table) {
            $table->text('nama_sub_sub_aspek')->change();
        });
    }

    public function down(): void
    {
        // Mengembalikan ke string jika di-rollback (Maksimal 255 karakter)
        Schema::table('aspeks', function (Blueprint $table) {
            $table->string('nama_aspek', 255)->change();
        });

        Schema::table('sub_aspeks', function (Blueprint $table) {
            $table->string('nama_sub_aspek', 255)->change();
        });

        Schema::table('sub_sub_aspeks', function (Blueprint $table) {
            $table->string('nama_sub_sub_aspek', 255)->change();
        });
    }
};