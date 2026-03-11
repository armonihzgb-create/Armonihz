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
        Schema::create('musician_media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('musician_profile_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['photo', 'video']);
            $table->string('path');
            $table->string('title')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->smallInteger('order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('musician_media');
    }
};
