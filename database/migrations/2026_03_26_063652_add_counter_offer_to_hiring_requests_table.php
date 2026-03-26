<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('hiring_requests', function (Blueprint $table) {
            // El mensaje que el músico le escribe de vuelta al cliente
            $table->text('musician_message')->nullable()->after('description');
            
            // El nuevo precio propuesto por el músico
            $table->decimal('counter_offer', 10, 2)->nullable()->after('budget');
            
            // Actualizamos los estados permitidos a nivel comentario o aplicación
            // Tu status enum actual es: 'pending', 'accepted', 'rejected'
            // Podríamos necesitar cambiar la columna entera o solo manejar el string si no es estricto en BD.
        });
    }

    public function down(): void {
        Schema::table('hiring_requests', function (Blueprint $table) {
            $table->dropColumn(['musician_message', 'counter_offer']);
        });
    }
};