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
    Schema::create('reports', function (Blueprint $table) {
        $table->id();
        // NUEVO: Especificamos explícitamente que es client_id y apunta a la tabla clients
        $table->foreignId('client_id')->constrained('clients')->onDelete('cascade'); 
        $table->foreignId('musician_profile_id')->constrained()->onDelete('cascade');
        $table->text('reason');
        $table->string('status')->default('pending');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
