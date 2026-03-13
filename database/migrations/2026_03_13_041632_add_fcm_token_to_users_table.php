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
        Schema::table('users', function (Blueprint $table) {
            // Agregamos la columna fcm_token de tipo texto (los tokens son largos)
            $table->text('fcm_token')->nullable()->after('email');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Si algún día revertimos la migración, borramos la columna
            $table->dropColumn('fcm_token');
        });
    }
};
