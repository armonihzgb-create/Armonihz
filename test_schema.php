<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

echo "Dropping table...\n";
Schema::dropIfExists('mv_client_spending_stats');

echo "Creating table...\n";
Schema::create('mv_client_spending_stats', function (Blueprint $table) {
    $table->id();
    $table->string('firebase_uid')->comment('Firebase UID del cliente');
    $table->string('report_month', 7)->comment('Periodo de reporte: formato YYYY-MM');
    $table->integer('total_events_created')->default(0)->comment('Total de eventos creados por el cliente en el mes');
    $table->decimal('total_spent', 12, 2)->default(0.00)->comment('Suma de presupuestos de eventos creados en el mes');
    $table->timestamp('last_updated')->useCurrent()->useCurrentOnUpdate();
    $table->timestamps();

    $table->unique(['firebase_uid', 'report_month'], 'mv_client_month_unique');
    $table->index('firebase_uid', 'idx_mv_client_firebase_uid');
});

echo "Done.\n";
