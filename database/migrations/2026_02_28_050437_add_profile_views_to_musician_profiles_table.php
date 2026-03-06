<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration 
{
    public function up(): void
    {
        Schema::table('musician_profiles', function (Blueprint $table) {
            $table->unsignedBigInteger('profile_views')->default(0)->after('is_verified');
        });
    }

    public function down(): void
    {
        Schema::table('musician_profiles', function (Blueprint $table) {
            $table->dropColumn('profile_views');
        });
    }
};
