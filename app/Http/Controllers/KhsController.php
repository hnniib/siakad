<?php

namespace App\Http\Controllers;

use App\Models\Khs;
use App\Models\Krs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KhsController extends Controller
{
    /** Mahasiswa: lihat KHS (hasil studi) sendiri */
    public function index()
    {
        $mahasiswa = Auth::user()->mahasiswa;

        $khsList = Krs::where('mahasiswa_id', $mahasiswa->id)
            ->where('status', 'disetujui')
            ->with(['mataKuliah', 'khs'])
            ->get();

        return view('mahasiswa.khs', [
            'khsList' => $khsList,
            'ipk' => $mahasiswa->hitungIpk(),
        ]);
    }

    /** Dosen: lihat daftar mahasiswa per mata kuliah untuk input nilai */
    public function inputForm(\App\Models\MataKuliah $mataKuliah)
    {
        $dosen = Auth::user()->dosen;
        abort_unless($mataKuliah->dosen_id === $dosen->id, 403);

        $pesertaKrs = $mataKuliah->krs()
            ->where('status', 'disetujui')
            ->with(['mahasiswa.user', 'khs'])
            ->get();

        return view('dosen.input-khs', compact('mataKuliah', 'pesertaKrs'));
    }

    /** Dosen: simpan/update nilai untuk satu mahasiswa */
    public function store(Request $request, Krs $krs)
    {
        $dosen = Auth::user()->dosen;
        abort_unless($krs->mataKuliah->dosen_id === $dosen->id, 403);

        $request->validate([
            'nilai_tugas' => ['required', 'numeric', 'min:0', 'max:100'],
            'nilai_uts' => ['required', 'numeric', 'min:0', 'max:100'],
            'nilai_uas' => ['required', 'numeric', 'min:0', 'max:100'],
        ]);

        $khs = Khs::firstOrNew(['krs_id' => $krs->id]);
        $khs->nilai_tugas = $request->nilai_tugas;
        $khs->nilai_uts = $request->nilai_uts;
        $khs->nilai_uas = $request->nilai_uas;
        $khs->diinput_oleh = $dosen->id;
        $khs->save();
        $khs->hitungDanSimpanNilai();

        return back()->with('success', 'Nilai berhasil disimpan.');
    }
}
