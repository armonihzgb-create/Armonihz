<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modelo: MusicianMonthlyStat
 *
 * Representa la vista materializada simulada `mv_musician_monthly_stats`.
 * Almacena estadísticas mensuales pre-calculadas de cada músico:
 * total de contrataciones, ganancias y rating promedio por periodo.
 *
 * Esta tabla es refrescada automáticamente a través del Trigger `trg_after_insert_review`
 * y el Artisan Command `stats:refresh-mv`.
 *
 * Uso:
 *   MusicianMonthlyStat::where('musician_profile_id', $id)
 *       ->orderByDesc('report_month')
 *       ->get();
 */
class MusicianMonthlyStat extends Model
{
    protected $table = 'mv_musician_monthly_stats';

    protected $fillable = [
        'musician_profile_id',
        'report_month',
        'total_hired',
        'total_earned',
        'avg_rating',
    ];

    protected $casts = [
        'total_earned' => 'decimal:2',
        'avg_rating'   => 'decimal:2',
    ];

    /**
     * Relación: el músico al que pertenecen estas estadísticas.
     */
    public function musician()
    {
        return $this->belongsTo(MusicianProfile::class, 'musician_profile_id');
    }
}
