<?php

namespace Database\Seeders;

use App\Models\Dosen;
use App\Models\Mahasiswa;
use App\Models\MataKuliah;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ---- Dosen demo ----
        $userDosen = User::create([
            'name' => 'Dr. Budi Santoso',
            'email' => 'dosen@siakad.test',
            'password' => Hash::make('password'),
            'role' => 'dosen',
        ]);

        $dosen = Dosen::create([
            'user_id' => $userDosen->id,
            'nidn' => '0011223344',
            'bidang_keahlian' => 'Kecerdasan Buatan',
        ]);

        $userDosen2 = User::create([
            'name' => 'Dr. Siti Aminah',
            'email' => 'dosen2@siakad.test',
            'password' => Hash::make('password'),
            'role' => 'dosen',
        ]);

        $dosen2 = Dosen::create([
            'user_id' => $userDosen2->id,
            'nidn' => '0055667788',
            'bidang_keahlian' => 'Rekayasa Perangkat Lunak',
        ]);

        // ---- Mahasiswa demo ----
        $userMhs = User::create([
            'name' => 'Andi Wijaya',
            'email' => 'mahasiswa@siakad.test',
            'password' => Hash::make('password'),
            'role' => 'mahasiswa',
        ]);

        Mahasiswa::create([
            'user_id' => $userMhs->id,
            'nim' => '2210101001',
            'program_studi' => 'Teknik Informatika',
            'semester' => 6,
        ]);

        // ---- Mata Kuliah (Perkuliahan) semester 6 ----
        MataKuliah::create([
            'kode' => 'TI601', 'nama' => 'Sistem Cerdas', 'sks' => 3,
            'semester' => 6, 'dosen_id' => $dosen->id, 'kapasitas' => 40,
        ]);

        MataKuliah::create([
            'kode' => 'TI602', 'nama' => 'Pemrograman Web Lanjut', 'sks' => 3,
            'semester' => 6, 'dosen_id' => $dosen2->id, 'kapasitas' => 40,
        ]);

        MataKuliah::create([
            'kode' => 'TI603', 'nama' => 'Basis Data Lanjut', 'sks' => 2,
            'semester' => 6, 'dosen_id' => $dosen2->id, 'kapasitas' => 35,
        ]);

        MataKuliah::create([
            'kode' => 'TI604', 'nama' => 'Interaksi Manusia Komputer', 'sks' => 2,
            'semester' => 6, 'dosen_id' => $dosen->id, 'kapasitas' => 40,
        ]);
    }
}
