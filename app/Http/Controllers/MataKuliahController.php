<?php

namespace App\Http\Controllers;

use App\Models\MataKuliah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class MataKuliahController extends Controller
{
    /** Dosen: lihat semua mata kuliah miliknya + form tambah baru */
    public function index()
    {
        $dosen = Auth::user()->dosen;
        $mataKuliah = $dosen->mataKuliah()->withCount('krs')->latest()->get();

        return view('dosen.perkuliahan', compact('mataKuliah'));
    }

    /** Dosen: simpan mata kuliah (Perkuliahan) baru */
    public function store(Request $request)
    {
        $dosen = Auth::user()->dosen;

        $request->validate([
            'kode' => ['required', 'string', 'unique:mata_kuliahs,kode'],
            'nama' => ['required', 'string', 'max:255'],
            'sks' => ['required', 'integer', 'min:1', 'max:6'],
            'semester' => ['required', 'integer', 'min:1', 'max:8'],
            'kapasitas' => ['required', 'integer', 'min:1', 'max:200'],
        ]);

        MataKuliah::create([
            'kode' => $request->kode,
            'nama' => $request->nama,
            'sks' => $request->sks,
            'semester' => $request->semester,
            'kapasitas' => $request->kapasitas,
            'dosen_id' => $dosen->id,
        ]);

        return back()->with('success', 'Mata kuliah "'.$request->nama.'" berhasil ditambahkan untuk Semester '.$request->semester.'.');
    }

    /** Dosen: perbarui data mata kuliah miliknya (kode, nama, sks, semester, kapasitas) */
    public function update(Request $request, MataKuliah $mataKuliah)
    {
        abort_unless($mataKuliah->dosen_id === Auth::user()->dosen->id, 403);

        $request->validate([
            'kode' => ['required', 'string', Rule::unique('mata_kuliahs', 'kode')->ignore($mataKuliah->id)],
            'nama' => ['required', 'string', 'max:255'],
            'sks' => ['required', 'integer', 'min:1', 'max:6'],
            'semester' => ['required', 'integer', 'min:1', 'max:8'],
            'kapasitas' => ['required', 'integer', 'min:1', 'max:200'],
        ]);

        $pesertaAktif = $mataKuliah->jumlahPesertaAktif();

        if ((int) $request->kapasitas < $pesertaAktif) {
            return back()
                ->withErrors(['kapasitas' => "Kapasitas tidak boleh lebih kecil dari jumlah peserta aktif saat ini ($pesertaAktif)."])
                ->withInput();
        }

        $mataKuliah->update([
            'kode' => $request->kode,
            'nama' => $request->nama,
            'sks' => $request->sks,
            'semester' => $request->semester,
            'kapasitas' => $request->kapasitas,
        ]);

        return back()->with('success', 'Mata kuliah "'.$mataKuliah->nama.'" berhasil diperbarui.');
    }

    /** Dosen: hapus mata kuliah miliknya */
    public function destroy(MataKuliah $mataKuliah)
    {
        abort_unless($mataKuliah->dosen_id === Auth::user()->dosen->id, 403);
        $mataKuliah->delete();

        return back()->with('success', 'Mata kuliah dihapus.');
    }
}