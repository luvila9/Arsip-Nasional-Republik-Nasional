<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Menyuntikkan kolom nama ke tabel aspeks yang sudah ada
        Schema::table('aspeks', function (Blueprint $table) {
            $table->string('nama_aspek')->after('id');
        });

        // 2. Menyuntikkan relasi dan nama ke tabel sub_aspeks yang sudah ada
        Schema::table('sub_aspeks', function (Blueprint $table) {
            $table->foreignId('aspek_id')->after('id')->constrained('aspeks')->cascadeOnDelete();
            $table->string('nama_sub_aspek')->after('aspek_id');
        });

        // 3. Membuat tabel sub_sub_aspeks dari awal karena belum ada
        Schema::create('sub_sub_aspeks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sub_aspek_id')->constrained('sub_aspeks')->cascadeOnDelete();
            $table->string('nama_sub_sub_aspek');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sub_sub_aspeks');
        
        Schema::table('sub_aspeks', function (Blueprint $table) {
            $table->dropForeign(['aspek_id']);
            $table->dropColumn(['aspek_id', 'nama_sub_aspek']);
        });

        Schema::table('aspeks', function (Blueprint $table) {
            $table->dropColumn('nama_aspek');
        });
    }
};