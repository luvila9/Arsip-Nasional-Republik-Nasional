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
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            // Menyimpan ID siapa yang melakukan aksi (bisa kosong jika sistem yang melakukan)
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            // Menyimpan jenis aksi: 'Login', 'Tambah', 'Edit', 'Hapus'
            $table->string('action');
            // Penjelasan detail: 'Admin A menghapus dokumen X'
            $table->text('description');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
