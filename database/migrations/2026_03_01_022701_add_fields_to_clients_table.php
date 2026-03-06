<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration 
{
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            // Only add firebase_uid if it doesn't exist yet
            if (!Schema::hasColumn('clients', 'firebase_uid')) {
                $table->string('firebase_uid')->nullable()->unique()->after('id');
            }

            // fotoPerfil already exists in create_clients_table — skip if present
            if (!Schema::hasColumn('clients', 'fotoPerfil')) {
                $table->string('fotoPerfil')->nullable()->after('firebase_uid');
            }
        });
    }

    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            if (Schema::hasColumn('clients', 'firebase_uid')) {
                $table->dropColumn('firebase_uid');
            }
        });
    }
};
