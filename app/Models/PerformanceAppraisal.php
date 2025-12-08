<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PerformanceAppraisal extends Model
{
    protected $fillable = ['target_id', 'appraiser_id', 'score', 'notes'];

    public function target(): BelongsTo {
        return $this->belongsTo(StaffPerformance::class);
    }

    public function appraiser(): BelongsTo {
        return $this->belongsTo(Staff::class);
    }

    protected static function booted(): void
    {
        // Jalankan logic ini setiap kali data penilaian dibuat atau diupdate
        static::saved(function (PerformanceAppraisal $appraisal) {
            static::updatePeriodAverage($appraisal);
        });

        // Jalankan logic ini jika data penilaian dihapus (nilai rata-rata pasti berubah)
        static::deleted(function (PerformanceAppraisal $appraisal) {
            static::updatePeriodAverage($appraisal);
        });
    }

    protected static function updatePeriodAverage(PerformanceAppraisal $appraisal)
    {
        $period = $appraisal->target->period;

        if (!$period) return;

        
        // Pilihan A: Rata-rata dari Rata-rata Pegawai (Lebih Fair)
        // (Nilai A + Nilai B + Nilai C) / 3 Pegawai
        // $averageScore = $period->performance() // Relasi HasMany ke StaffPerformance
        //     ->withAvg('appraisal', 'score') // Hitung rata-rata penilaian per orang dulu
        //     ->get()
        //     ->avg('appraisal_avg_score'); // Lalu rata-ratakan hasil per orang tersebut

        // Pilihan B: Rata-rata gelondongan (Semua nilai penilaian dibagi total penilaian)
        $averageScore = PerformanceAppraisal::whereHas('target', function ($q) use ($period) {
            $q->where('period_id', $period->id);
        })->avg('score');

        // 3. Simpan ke Periode
        // Kita gunakan updateQuietly agar tidak memicu event loop lain (jika ada)
        $period->updateQuietly([
            'score' => round($averageScore, 2) // Bulatkan 2 desimal
        ]);
    }
}
