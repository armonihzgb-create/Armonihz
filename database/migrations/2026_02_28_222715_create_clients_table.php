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
            
            // 1. El identificador de Firebase (Obligatorio y único)
            $table->string('firebase_uid')->unique(); 
            
            // 2. CORRECCIÓN: nullable() va ANTES de constrained()
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade'); 
            
            // 3. Faltaban estos campos que usas en syncClient()
            $table->string('nombre')->nullable();
            $table->string('email')->nullable();
            
            $table->string('fotoPerfil')->nullable();
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
