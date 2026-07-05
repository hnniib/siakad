<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jadwal extends Model
{
    use HasFactory;

    protected $fillable = [
        'mata_kuliah_id', 'jenis', 'hari', 'tanggal', 'jam_mulai', 'jam_selesai', 'ruang', 'digenerate_otomatis',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function mataKuliah()
    {
        return $this->belongsTo(MataKuliah::class);
    }
}