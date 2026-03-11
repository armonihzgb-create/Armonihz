<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration 
{
    public function up(): void
    {
        Schema::create('casting_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_event_id')->constrained('client_events')->onDelete('cascade');
            $table->foreignId('musician_profile_id')->constrained('musician_profiles')->onDelete('cascade');
            $table->decimal('proposed_price', 10, 2);
            $table->text('message');
            $table->enum('status', ['pending', 'accepted', 'rejected'])->default('pending');
            $table->unique(['client_event_id', 'musician_profile_id']); // Un músico, una postulación por evento
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('casting_applications');
    }
};
