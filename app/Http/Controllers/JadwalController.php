<?php

namespace App\Http\Controllers;

use App\Models\Jadwal;
use App\Models\MataKuliah;
use App\Services\JadwalOtomatisService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JadwalController extends Controller
{
    /** Semua role: lihat jadwal kuliah & ujian */
    public function index(Request $request)
    {
        $user = Auth::user();

        $baseQuery = Jadwal::with('mataKuliah.dosen.user');

        if ($user->isMahasiswa()) {
            $mataKuliahIds = $user->mahasiswa->krs()->where('status', 'disetujui')->pluck('mata_kuliah_id');
            $baseQuery->whereIn('mata_kuliah_id', $mataKuliahIds);
        } elseif ($user->isDosen()) {
            $mataKuliahIds = $user->dosen->mataKuliah()->pluck('id');
            $baseQuery->whereIn('mata_kuliah_id', $mataKuliahIds);
        }

        // Jadwal kuliah: berpola mingguan, dikelompokkan per hari
        $jadwalKuliah = (clone $baseQuery)
            ->where('jenis', 'kuliah')
            ->orderByRaw("FIELD(hari,'Senin','Selasa','Rabu','Kamis','Jumat','Sabtu')")
            ->orderBy('jam_mulai')
            ->get()
            ->groupBy('hari');

        // Jadwal UTS: berpola tanggal kalender asli, dikelompokkan per tanggal
        $jadwalUTS = (clone $baseQuery)
            ->where('jenis', 'ujian_uts')
            ->orderBy('tanggal')
            ->orderBy('jam_mulai')
            ->get()
            ->groupBy(fn ($j) => optional($j->tanggal)->format('Y-m-d'));

        // Jadwal UAS: berpola tanggal kalender asli, dikelompokkan per tanggal
        $jadwalUAS = (clone $baseQuery)
            ->where('jenis', 'ujian_uas')
            ->orderBy('tanggal')
            ->orderBy('jam_mulai')
            ->get()
            ->groupBy(fn ($j) => optional($j->tanggal)->format('Y-m-d'));

        $view = $user->isDosen() ? 'dosen.jadwal' : 'mahasiswa.jadwal';

        return view($view, compact('jadwalKuliah', 'jadwalUTS', 'jadwalUAS'));
    }

    /**
     * Jalankan SISTEM CERDAS penjadwalan otomatis (CSP + backtracking).
     * Dibatasi untuk dosen/admin.
     */
    public function generate(Request $request, JadwalOtomatisService $service)
    {
        abort_unless(Auth::user()->isDosen() || Auth::user()->isAdmin(), 403);

        $request->validate([
            'semester' => ['required', 'integer', 'min:1', 'max:8'],
            'jenis' => ['required', 'in:kuliah,ujian_uts,ujian_uas'],
            'tanggal_mulai' => ['nullable', 'date', 'required_if:jenis,ujian_uts,ujian_uas'],
        ]);

        $hasil = $service->generate(
            (int) $request->semester,
            $request->jenis,
            $request->tanggal_mulai
        );

        return back()->with('hasil_generate', $hasil);
    }

    /**
     * Hapus jadwal hasil generate otomatis untuk semester & jenis tertentu.
     * Tidak menyentuh jadwal yang dibuat manual (digenerate_otomatis = false).
     * Dibatasi untuk dosen/admin.
     */
    public function hapus(Request $request)
    {
        abort_unless(Auth::user()->isDosen() || Auth::user()->isAdmin(), 403);

        $request->validate([
            'semester' => ['required', 'integer', 'min:1', 'max:8'],
            'jenis' => ['required', 'in:kuliah,ujian_uts,ujian_uas'],
        ]);

        $mataKuliahIds = MataKuliah::where('semester', $request->semester)->pluck('id');

        $jumlah = Jadwal::whereIn('mata_kuliah_id', $mataKuliahIds)
            ->where('jenis', $request->jenis)
            ->where('digenerate_otomatis', true)
            ->delete();

        $labelJenis = [
            'kuliah' => 'Kuliah',
            'ujian_uts' => 'UTS',
            'ujian_uas' => 'UAS',
        ][$request->jenis];

        if ($jumlah === 0) {
            return back()->with('error', "Tidak ada jadwal $labelJenis untuk semester {$request->semester} yang perlu dihapus.");
        }

        return back()->with('success', "Berhasil menghapus $jumlah jadwal $labelJenis untuk semester {$request->semester}.");
    }
}