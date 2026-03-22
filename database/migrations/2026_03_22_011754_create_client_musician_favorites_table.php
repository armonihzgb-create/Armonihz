<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('client_musician_favorites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');
            $table->foreignId('musician_id')->constrained('musicians')->onDelete('cascade');
            $table->timestamps();

            // Evitar que un cliente le de favorito 2 veces al mismo músico
            $table->unique(['client_id', 'musician_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('client_musician_favorites');
    }
};
