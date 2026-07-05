<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dosen extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'nidn', 'bidang_keahlian'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function mataKuliah()
    {
        return $this->hasMany(MataKuliah::class);
    }
}
