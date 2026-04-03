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
        // ID del cliente que reporta (ajusta 'user_id' según cómo manejes la sesión en Firebase)
        $table->foreignId('user_id')->constrained()->onDelete('cascade'); 
        $table->foreignId('musician_profile_id')->constrained()->onDelete('cascade');
        $table->text('reason');
        $table->string('status')->default('pending'); // pending, reviewed, resolved
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
