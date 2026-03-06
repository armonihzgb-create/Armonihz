<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('client_events', function (Blueprint $table) {
            $table->id();
            // Vinculamos el evento con el usuario logueado usando su uid de Firebase
            $table->string('firebase_uid'); 
            
            $table->string('titulo');
            $table->string('tipo_musica'); // Ej. Mariachi, Banda
            $table->string('fecha'); // Guardado como string (DD/MM/YYYY) para coincidir con Android
            $table->string('duracion');
            $table->string('ubicacion');
            $table->text('descripcion')->nullable();
            $table->decimal('presupuesto', 10, 2); // Hasta 99,999,999.99
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('client_events');
    }
};