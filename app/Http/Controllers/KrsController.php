<?php

namespace App\Http\Controllers;

use App\Models\Jadwal;
use App\Models\Krs;
use App\Models\MataKuliah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KrsController extends Controller
{
    /** Mahasiswa: lihat & isi KRS */
    public function index()
    {
        $mahasiswa = Auth::user()->mahasiswa;

        $mataKuliahTersedia = MataKuliah::where('semester', $mahasiswa->semester)
            ->withCount('krs')
            ->get();

        $krsSaya = $mahasiswa->krs()->with('mataKuliah')->get();

        return view('mahasiswa.krs', compact('mataKuliahTersedia', 'krsSaya', 'mahasiswa'));
    }

    /** Mahasiswa: ajukan mata kuliah ke KRS */
    public function store(Request $request)
    {
        $request->validate([
            'mata_kuliah_id' => ['required', 'exists:mata_kuliahs,id'],
        ]);

        $mahasiswa = Auth::user()->mahasiswa;
        $mk = MataKuliah::findOrFail($request->mata_kuliah_id);

        if ($mk->jumlahPesertaAktif() >= $mk->kapasitas) {
            return back()->with('error', 'Kuota kelas '.$mk->nama.' sudah penuh.');
        }

        if ($konflik = $this->cekBentrokJadwal($mahasiswa, $mk)) {
            return back()->with('error', 'Jadwal '.$mk->nama.' bentrok dengan '.$konflik.' yang sudah ada di KRS Anda.');
        }

        Krs::firstOrCreate([
            'mahasiswa_id' => $mahasiswa->id,
            'mata_kuliah_id' => $mk->id,
            'tahun_ajaran' => date('Y').'/'.(date('Y') + 1),
            'semester_ajaran' => 'ganjil',
        ], [
            'status' => 'diajukan',
        ]);

        return back()->with('success', 'Mata kuliah '.$mk->nama.' berhasil diajukan ke KRS.');
    }

    /** Mahasiswa: batalkan pengajuan KRS */
    public function destroy(Krs $krs)
    {
        abort_unless($krs->mahasiswa_id === Auth::user()->mahasiswa->id, 403);
        $krs->delete();

        return back()->with('success', 'Mata kuliah dibatalkan dari KRS.');
    }

    /** Dosen: lihat semua pengajuan KRS mahasiswa untuk mata kuliah yang diampu */
    
    public function indexForDosen()
{
    $user = Auth::user();

    if (! $user->dosen) {
        abort(403, 'Data dosen tidak ditemukan.');
    }

    $dosen = $user->dosen;
    $mataKuliahIds = $dosen->mataKuliah()->pluck('id');

    $krsList = Krs::whereIn('mata_kuliah_id', $mataKuliahIds)
        ->with(['mahasiswa.user', 'mataKuliah'])
        ->latest()
        ->get()
        ->groupBy(fn ($k) => $k->mataKuliah->nama);

    return view('dosen.krs', compact('krsList'));
}
    /** Dosen: setujui/tolak KRS mahasiswa untuk mata kuliah yang diampu */
    public function updateStatus(Request $request, Krs $krs)
    {
        $dosen = Auth::user()->dosen;
        abort_unless($krs->mataKuliah->dosen_id === $dosen->id, 403);

        $request->validate(['status' => ['required', 'in:disetujui,ditolak']]);

        if ($request->status === 'disetujui') {
            if ($konflik = $this->cekBentrokJadwal($krs->mahasiswa, $krs->mataKuliah, $krs->id)) {
                return back()->with('error', 'Tidak bisa menyetujui: jadwal '.$krs->mataKuliah->nama.' bentrok dengan '.$konflik.' milik mahasiswa ini.');
            }
        }

        $krs->update(['status' => $request->status]);

        return back()->with('success', 'Status KRS diperbarui.');
    }

    /**
     * Cek apakah jadwal mata kuliah $mk bentrok waktu dengan mata kuliah lain
     * yang sudah ada di KRS mahasiswa (status diajukan/disetujui).
     * Hanya relevan kalau jadwal untuk $mk sudah pernah digenerate.
     *
     * @return string|null Nama mata kuliah yang bentrok, atau null kalau aman.
     */
    protected function cekBentrokJadwal($mahasiswa, MataKuliah $mk, ?int $kecualiKrsId = null): ?string
    {
        $jadwalMk = Jadwal::where('mata_kuliah_id', $mk->id)->get();

        if ($jadwalMk->isEmpty()) {
            return null; // jadwal belum digenerate, tidak ada yang bisa dicek
        }

        $mkLainIds = $mahasiswa->krs()
            ->whereIn('status', ['diajukan', 'disetujui'])
            ->when($kecualiKrsId, fn ($q) => $q->where('id', '!=', $kecualiKrsId))
            ->where('mata_kuliah_id', '!=', $mk->id)
            ->pluck('mata_kuliah_id');

        if ($mkLainIds->isEmpty()) {
            return null;
        }

        $jadwalLain = Jadwal::whereIn('mata_kuliah_id', $mkLainIds)->with('mataKuliah')->get();

        foreach ($jadwalMk as $j1) {
            foreach ($jadwalLain as $j2) {
                if (
                    $j1->hari === $j2->hari &&
                    $j1->jam_mulai < $j2->jam_selesai &&
                    $j1->jam_selesai > $j2->jam_mulai
                ) {
                    return $j2->mataKuliah->nama;
                }
            }
        }

        return null;
    }
}