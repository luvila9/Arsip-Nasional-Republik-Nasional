<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('sub_aspeks', function (Blueprint $table) {
            $table->boolean('is_active')->default(true)->after('link');
        });

        Schema::table('sub_sub_aspeks', function (Blueprint $table) {
            $table->boolean('is_active')->default(true)->after('link');
        });
    }

    public function down()
    {
        Schema::table('sub_aspeks', function (Blueprint $table) {
            $table->dropColumn('is_active');
        });

        Schema::table('sub_sub_aspeks', function (Blueprint $table) {
            $table->dropColumn('is_active');
        });
    }
};