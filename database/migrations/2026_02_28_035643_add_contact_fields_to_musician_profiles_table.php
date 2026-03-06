<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration 
{
    public function up(): void
    {
        Schema::table('musician_profiles', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('profile_picture');
            $table->string('instagram')->nullable()->after('phone');
            $table->string('facebook')->nullable()->after('instagram');
            $table->string('youtube')->nullable()->after('facebook');
            $table->string('coverage_notes')->nullable()->after('youtube');
        });
    }

    public function down(): void
    {
        Schema::table('musician_profiles', function (Blueprint $table) {
            $table->dropColumn(['phone', 'instagram', 'facebook', 'youtube', 'coverage_notes']);
        });
    }
};
