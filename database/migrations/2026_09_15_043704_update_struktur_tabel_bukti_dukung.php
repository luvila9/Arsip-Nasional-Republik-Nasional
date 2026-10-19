<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bukti_dukungs', function (Blueprint $table) {
            // Siapa yang mengupload dokumen ini
            $table->foreignId('user_id')->after('id')->constrained('users')->cascadeOnDelete();
            
            // Kategori Aspek dokumen (Level 1, 2, 3)
            $table->foreignId('aspek_id')->after('user_id')->constrained('aspeks')->cascadeOnDelete();
            // Sub dan Sub-sub dibuat nullable (boleh kosong) karena mungkin ada dokumen yang hanya sampai level 1 atau 2
            $table->foreignId('sub_aspek_id')->nullable()->after('aspek_id')->constrained('sub_aspeks')->cascadeOnDelete();
            $table->foreignId('sub_sub_aspek_id')->nullable()->after('sub_aspek_id')->constrained('sub_sub_aspeks')->cascadeOnDelete();
            
            // Detail Dokumen
            $table->string('nama_dokumen')->after('sub_sub_aspek_id');
            $table->string('file_path')->after('nama_dokumen'); // Lokasi file PDF/Excel disimpan
            $table->text('keterangan')->nullable()->after('file_path');
            
            // Status verifikasi dokumen
            $table->enum('status', ['Menunggu Verifikasi', 'Disetujui', 'Revisi'])->default('Menunggu Verifikasi')->after('keterangan');
        });
    }

    public function down(): void
    {
        Schema::table('bukti_dukungs', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['aspek_id']);
            $table->dropForeign(['sub_aspek_id']);
            $table->dropForeign(['sub_sub_aspek_id']);
            
            $table->dropColumn([
                'user_id', 'aspek_id', 'sub_aspek_id', 'sub_sub_aspek_id', 
                'nama_dokumen', 'file_path', 'keterangan', 'status'
            ]);
        });
    }
};