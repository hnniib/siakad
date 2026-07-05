<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->isMahasiswa()) {
            $mahasiswa = $user->mahasiswa;

            if (! $mahasiswa) {
                return view('mahasiswa.dashboard', [
                    'mahasiswa' => null,
                    'krsAktif' => collect(),
                    'ipk' => 0,
                ]);
            }

            $krsAktif = $mahasiswa->krs()->with('mataKuliah')->latest()->take(5)->get();

            return view('mahasiswa.dashboard', [
                'mahasiswa' => $mahasiswa,
                'krsAktif' => $krsAktif,
                'ipk' => $mahasiswa->hitungIpk(),
            ]);
        }

        if ($user->isDosen()) {
            $dosen = $user->dosen;

            if (! $dosen) {
                return view('dosen.dashboard', [
                    'dosen' => (object)[
                        'nidn' => '-',
                        'bidang_keahlian' => 'Data dosen belum terhubung',
                    ],
                    'mataKuliah' => collect(),
                ]);
            }

            $mataKuliah = $dosen->mataKuliah()->withCount('krs')->get();

            return view('dosen.dashboard', [
                'dosen' => $dosen,
                'mataKuliah' => $mataKuliah,
            ]);
        }

        return view('mahasiswa.dashboard', [
            'mahasiswa' => null,
            'krsAktif' => collect(),
            'ipk' => 0
        ]);
    }
}