<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mahasiswa extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'nim', 'program_studi', 'semester'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function krs()
    {
        return $this->hasMany(Krs::class);
    }

    /**
     * Hitung IPK (Indeks Prestasi Kumulatif) dari seluruh KHS yang sudah dinilai.
     */
    public function hitungIpk(): float
    {
        $krsList = $this->krs()->with(['khs', 'mataKuliah'])->whereHas('khs', function ($q) {
            $q->whereNotNull('nilai_akhir');
        })->get();

        $totalSks = 0;
        $totalBobot = 0;

        foreach ($krsList as $krs) {
            $sks = $krs->mataKuliah->sks;
            $bobot = self::bobotHuruf($krs->khs->nilai_huruf);
            $totalSks += $sks;
            $totalBobot += $sks * $bobot;
        }

        return $totalSks > 0 ? round($totalBobot / $totalSks, 2) : 0.0;
    }

    public static function bobotHuruf(?string $huruf): float
    {
        return match ($huruf) {
            'A' => 4.0,
            'AB' => 3.5,
            'B' => 3.0,
            'BC' => 2.5,
            'C' => 2.0,
            'D' => 1.0,
            'E' => 0.0,
            default => 0.0,
        };
    }
}
