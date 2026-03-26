<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('hiring_requests', function (Blueprint $table) {
            // Agregamos la columna end_time
            $table->dateTime('end_time')->nullable()->after('event_date');
        });
    }

    public function down(): void {
        Schema::table('hiring_requests', function (Blueprint $table) {
            $table->dropColumn('end_time');
        });
    }
};