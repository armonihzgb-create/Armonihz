<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration 
{
    public function up(): void
    {
        Schema::table('client_events', function (Blueprint $table) {
            $table->enum('status', ['open', 'closed', 'cancelled'])->default('open')->after('presupuesto');
        });
    }

    public function down(): void
    {
        Schema::table('client_events', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
