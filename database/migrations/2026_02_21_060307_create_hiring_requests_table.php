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
        Schema::create('hiring_requests', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->foreignId('client_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('musician_profile_id')->constrained('musician_profiles')->onDelete('cascade');
            $table->dateTime('event_date');
            $table->string('event_location');
            $table->text('description');
            $table->decimal('budget', 10, 2);
            $table->enum('status', ['pending', 'accepted', 'rejected'])->default('pending')->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hiring_requests');
    }
};
