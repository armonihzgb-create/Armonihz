<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modelo: ClientSpendingStat
 *
 * Representa la vista materializada simulada `mv_client_spending_stats`.
 * Almacena estadísticas mensuales pre-calculadas de cada cliente:
 * total de eventos creados y presupuesto total invertido por periodo.
 *
 * Uso:
 *   ClientSpendingStat::where('client_id', $clientId)
 *       ->orderByDesc('report_month')
 *       ->get();
 */
class ClientSpendingStat extends Model
{
    protected $table = 'mv_client_spending_stats';

    protected $fillable = [
        'client_id',
        'report_month',
        'total_events_created',
        'total_spent',
    ];

    protected $casts = [
        'total_spent' => 'decimal:2',
    ];

    /**
     * Relación: el usuario (cliente) al que pertenecen estas estadísticas.
     */
    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }
}
