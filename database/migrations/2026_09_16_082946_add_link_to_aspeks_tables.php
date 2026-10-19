<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tambahkan kolom link (nullable = boleh kosong) di ketiga tabel
        Schema::table('aspeks', function (Blueprint $table) {
            $table->string('link')->nullable()->after('nama_aspek');
        });
        Schema::table('sub_aspeks', function (Blueprint $table) {
            $table->string('link')->nullable()->after('nama_sub_aspek');
        });
        Schema::table('sub_sub_aspeks', function (Blueprint $table) {
            $table->string('link')->nullable()->after('nama_sub_sub_aspek');
        });
    }

    public function down(): void
    {
        Schema::table('aspeks', function (Blueprint $table) { $table->dropColumn('link'); });
        Schema::table('sub_aspeks', function (Blueprint $table) { $table->dropColumn('link'); });
        Schema::table('sub_sub_aspeks', function (Blueprint $table) { $table->dropColumn('link'); });
    }
};