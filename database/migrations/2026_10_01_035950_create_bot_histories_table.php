<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('bot_histories', function (Blueprint $table) {
            $table->id();
            $table->text('pertanyaan');
            $table->text('jawaban_bot')->nullable();
            $table->string('ip_address')->nullable(); // Opsional untuk melacak pengunjung
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bot_histories');
    }
};
