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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            
            // ── Identificadores y Relaciones ──
            $table->string('firebase_uid')->unique();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            
            // ── Datos del Perfil (Permitimos nulos para la lógica del candado) ──
            $table->string('nombre')->nullable();
            $table->string('apellido')->nullable();
            $table->string('email')->nullable();
            $table->string('telefono', 20)->nullable();
            
            // ── Fotografías ──
            $table->string('fotoPerfil')->nullable();
            $table->text('google_picture')->nullable(); // Usamos text por si la URL de Google es muy larga
            
            // ── Notificaciones ──
            $table->text('fcm_token')->nullable();
            
            // ── Seguridad y Onboarding ──
            // 🔥 Nuestro nuevo candado maestro. Empieza en false por defecto.
            $table->boolean('terminos_aceptados')->default(false); 

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};