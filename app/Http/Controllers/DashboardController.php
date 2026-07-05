<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Satu route /dashboard, tapi menampilkan view yang BERBEDA
     * tergantung role user yang sedang login (mahasiswa vs dosen).
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->isMahasiswa()) {
            $mahasiswa = $user->mahasiswa;
            $krsAktif = $mahasiswa->krs()->with('mataKuliah')->latest()->take(5)->get();

            return view('mahasiswa.dashboard', [
                'mahasiswa' => $mahasiswa,
                'krsAktif' => $krsAktif,
                'ipk' => $mahasiswa->hitungIpk(),
            ]);
        }

        if ($user->isDosen()) {
            $dosen = $user->dosen;
            $mataKuliah = $dosen->mataKuliah()->withCount('krs')->get();

            return view('dosen.dashboard', [
                'dosen' => $dosen,
                'mataKuliah' => $mataKuliah,
            ]);
        }

        // fallback admin / role lain
        return view('mahasiswa.dashboard', ['mahasiswa' => null, 'krsAktif' => collect(), 'ipk' => 0]);
    }
}
