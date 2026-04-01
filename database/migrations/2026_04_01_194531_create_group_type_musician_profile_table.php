<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('group_type_musician_profile', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_type_id')->constrained()->cascadeOnDelete();
            $table->foreignId('musician_profile_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            
            $table->unique(['group_type_id', 'musician_profile_id'], 'group_type_profile_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('group_type_musician_profile');
    }
};
