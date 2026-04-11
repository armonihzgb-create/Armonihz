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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });
        
        // Insert default settings
        DB::table('settings')->insert([
            ['key' => 'maintenance_mode', 'value' => '0'],
            ['key' => 'registration_enabled', 'value' => '1'],
            ['key' => 'notify_new_musician', 'value' => '1'],
            ['key' => 'notify_report', 'value' => '1'],
            ['key' => 'notify_casting', 'value' => '0'],
            ['key' => 'support_email', 'value' => 'soporte@armonihz.com'],
            ['key' => 'support_phone', 'value' => '+52 55 1234 5678'],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
