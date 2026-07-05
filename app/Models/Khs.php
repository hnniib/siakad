<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Khs extends Model
{
    use HasFactory;

    protected $table = 'khs';

    protected $fillable = [
        'krs_id', 'nilai_tugas', 'nilai_uts', 'nilai_uas', 'nilai_akhir', 'nilai_huruf', 'diinput_oleh',
    ];

    public function krs()
    {
        return $this->belongsTo(Krs::class);
    }

    public function dosenPenginput()
    {
        return $this->belongsTo(Dosen::class, 'diinput_oleh');
    }

    /**
     * Hitung nilai akhir dari komponen (bobot: tugas 30%, UTS 30%, UAS 40%)
     * lalu konversi ke nilai huruf.
     */
    public function hitungDanSimpanNilai(): void
    {
        $tugas = $this->nilai_tugas ?? 0;
        $uts = $this->nilai_uts ?? 0;
        $uas = $this->nilai_uas ?? 0;

        $akhir = ($tugas * 0.3) + ($uts * 0.3) + ($uas * 0.4);
        $this->nilai_akhir = round($akhir, 2);
        $this->nilai_huruf = self::konversiHuruf($this->nilai_akhir);
        $this->save();
    }

    public static function konversiHuruf(float $nilai): string
    {
        return match (true) {
            $nilai >= 85 => 'A',
            $nilai >= 75 => 'AB',
            $nilai >= 65 => 'B',
            $nilai >= 60 => 'BC',
            $nilai >= 50 => 'C',
            $nilai >= 40 => 'D',
            default => 'E',
        };
    }
}
