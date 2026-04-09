<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
 * Programación del refresco diario de vistas materializadas.
 * Actualiza mv_musician_monthly_stats y mv_client_spending_stats cada día a medianoche.
 * Requiere configurar el CRON del servidor:
 *   * * * * * php /ruta/al/proyecto/artisan schedule:run >> /dev/null 2>&1
 */
Schedule::command('stats:refresh-mv')->daily()->description('Refresca vistas materializadas de estadísticas');
