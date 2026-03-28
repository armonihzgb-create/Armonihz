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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('musician_profile_id')->constrained('musician_profiles')->onDelete('cascade');
            $table->foreignId('hiring_request_id')->nullable()->constrained('hiring_requests')->onDelete('set null');
            $table->foreignId('casting_application_id')->nullable()->constrained('casting_applications')->onDelete('set null');
            $table->integer('rating');
            $table->text('comment')->nullable();
            $table->text('response')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
