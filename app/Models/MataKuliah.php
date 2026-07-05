<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MataKuliah extends Model
{
    use HasFactory;

    protected $table = 'mata_kuliahs';

    protected $fillable = ['kode', 'nama', 'sks', 'semester', 'dosen_id', 'kapasitas'];

    public function dosen()
    {
        return $this->belongsTo(Dosen::class);
    }

    public function krs()
    {
        return $this->hasMany(Krs::class);
    }

    public function jadwals()
    {
        return $this->hasMany(Jadwal::class);
    }

    public function jumlahPesertaAktif(): int
    {
        return $this->krs()->where('status', '!=', 'ditolak')->count();
    }
}
