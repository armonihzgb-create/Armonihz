<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('promotions', function (Blueprint $table) {
            // El estado inicial será 'pendiente' hasta que valides el ticket
            $table->string('status')->default('pendiente')->after('is_active');
            
            // Ruta donde guardaremos la imagen del comprobante en el servidor
            $table->string('receipt_path')->nullable()->after('status');
            
            // Para saber qué plan compraron (Basico, Estandar, Premium) y calcular los días después
            $table->string('plan_type')->nullable()->after('receipt_path');

            // Hacemos las fechas anulables, porque al crearse la promoción en "pendiente", 
            // la promoción aún no tiene una fecha de inicio o fin real.
            $table->datetime('valid_from')->nullable()->change();
            $table->datetime('valid_until')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('promotions', function (Blueprint $table) {
            $table->dropColumn(['status', 'receipt_path', 'plan_type']);
        });
    }
};