<?php

namespace App\Services;

use App\Models\Jadwal;
use App\Models\Krs;
use App\Models\MataKuliah;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * SISTEM CERDAS: Penjadwalan Otomatis Kuliah & Ujian.
 *
 * Ini adalah implementasi dari Constraint Satisfaction Problem (CSP):
 * setiap mata kuliah adalah "variabel" yang harus diberi "nilai" berupa
 * (sesi, jam, ruang) sedemikian sehingga semua "constraint" (batasan) terpenuhi:
 *
 *   1. Hard constraint - Dosen tidak boleh mengajar 2 kelas di waktu yang sama.
 *   2. Hard constraint - Ruang tidak boleh dipakai 2 kelas di waktu yang sama.
 *   3. Hard constraint - Jadwal harus berada dalam jam operasional kampus.
 *   4. Hard constraint - Mahasiswa yang sama tidak boleh punya 2 mata kuliah
 *      pada jam yang sama (dicek dari irisan peserta KRS yang disetujui).
 *   5. Soft constraint (heuristik) - Mata kuliah dengan SKS besar / peserta
 *      banyak diprioritaskan mendapat slot lebih dulu (Most Constrained Variable).
 *   6. Soft constraint (heuristik) - Sesi (hari untuk kuliah, tanggal untuk ujian)
 *      yang masih paling kosong diprioritaskan, supaya jadwal menyebar merata
 *      ke seluruh hari kerja alih-alih menumpuk di satu hari saja.
 *
 * Perbedaan konsep penting:
 *   - Kuliah (jenis='kuliah') berpola MINGGUAN: hanya perlu hari (Senin-Jumat),
 *     berulang tiap minggu selama satu semester. Kolom 'tanggal' dibiarkan null.
 *   - Ujian (jenis='ujian_uts' / 'ujian_uas') berpola TANGGAL KALENDER ASLI:
 *     dimulai dari tanggal yang ditentukan pengguna, lalu disebar ke hari-hari
 *     kerja berikutnya. Kolom 'tanggal' diisi, dan 'hari' diturunkan dari tanggal
 *     tersebut (hanya untuk keperluan tampilan).
 */
class JadwalOtomatisService
{
    /** Nama hari kerja kampus, index 1=Senin ... 5=Jumat (ISO-8601 dayOfWeekIso) */
    protected array $namaHariKerja = [1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat'];

    /** Slot jam kuliah (format 24 jam) */
    protected array $slotJam = [
        ['07:30', '09:10'],
        ['09:10', '10:50'],
        ['10:50', '12:30'],
        ['13:30', '15:10'],
        ['15:10', '16:50'],
    ];

    /** Daftar ruang yang tersedia */
    protected array $daftarRuang = ['R101', 'R102', 'R103', 'R201', 'R202', 'LAB1', 'LAB2'];

    /** Berapa banyak hari kerja ke depan yang disediakan sebagai kandidat sesi ujian */
    protected int $jangkauanHariUjian = 15;

    /**
     * Jalankan penjadwalan otomatis untuk semua mata kuliah pada semester tertentu.
     *
     * @param  string  $jenis  'kuliah' | 'ujian_uts' | 'ujian_uas'
     * @param  string|null  $tanggalMulai  Wajib diisi (format Y-m-d) jika $jenis bukan 'kuliah'.
     * @return array{berhasil: Collection, gagal: Collection}
     */
    public function generate(int $semester, string $jenis = 'kuliah', ?string $tanggalMulai = null): array
    {
        if ($jenis !== 'kuliah' && empty($tanggalMulai)) {
            return [
                'berhasil' => collect(),
                'gagal' => collect(['Tanggal mulai wajib diisi untuk generate jadwal ujian.']),
            ];
        }

        // Hapus jadwal otomatis lama untuk semester & jenis ini agar tidak duplikat
        MataKuliah::where('semester', $semester)->get()->pluck('id')->each(function ($id) use ($jenis) {
            Jadwal::where('mata_kuliah_id', $id)
                ->where('jenis', $jenis)
                ->where('digenerate_otomatis', true)
                ->delete();
        });

        $mataKuliahList = MataKuliah::where('semester', $semester)
            ->withCount('krs')
            ->get()
            // Heuristik MCV: urutkan dari yang paling "sulit" dijadwalkan dulu
            ->sortByDesc(function ($mk) {
                return $mk->sks * 10 + $mk->jumlahPesertaAktif();
            })
            ->values();

        // Peta mata_kuliah_id => daftar mahasiswa_id (dari KRS yang sudah disetujui).
        // Dipakai untuk constraint 4: cegah mahasiswa yang sama dijadwalkan bentrok.
        $mahasiswaPerMk = Krs::where('status', 'disetujui')
            ->whereIn('mata_kuliah_id', $mataKuliahList->pluck('id'))
            ->get()
            ->groupBy('mata_kuliah_id')
            ->map(fn ($rows) => $rows->pluck('mahasiswa_id')->unique()->values()->all());

        $sesiKandidat = $jenis === 'kuliah'
            ? $this->sesiMingguan()
            : $this->sesiTanggal($tanggalMulai);

        // occupied[sesi_key][jam_mulai] = ['dosen' => [...], 'ruang' => [...], 'mahasiswa' => [...]]
        $occupied = [];

        $berhasil = collect();
        $gagal = collect();

        foreach ($mataKuliahList as $mk) {
            $mahasiswaMk = $mahasiswaPerMk->get($mk->id, []);

            $slot = $this->cariSlotTerbaik($mk, $occupied, $sesiKandidat, $mahasiswaMk);

            if ($slot === null) {
                $gagal->push($mk->nama.' ('.$mk->kode.') - tidak ditemukan slot bebas konflik');
                continue;
            }

            ['sesi' => $sesi, 'jam_mulai' => $jamMulai, 'jam_selesai' => $jamSelesai, 'ruang' => $ruang] = $slot;

            Jadwal::create([
                'mata_kuliah_id' => $mk->id,
                'jenis' => $jenis,
                'hari' => $sesi['hari'],
                'tanggal' => $sesi['tanggal'],
                'jam_mulai' => $jamMulai,
                'jam_selesai' => $jamSelesai,
                'ruang' => $ruang,
                'digenerate_otomatis' => true,
            ]);

            $occupied[$sesi['key']][$jamMulai]['dosen'][] = $mk->dosen_id;
            $occupied[$sesi['key']][$jamMulai]['ruang'][] = $ruang;
            $occupied[$sesi['key']][$jamMulai]['mahasiswa'] = array_merge(
                $occupied[$sesi['key']][$jamMulai]['mahasiswa'] ?? [],
                $mahasiswaMk
            );

            $labelSesi = $sesi['tanggal'] ? $sesi['hari'].', '.$sesi['tanggal'] : $sesi['hari'];
            $berhasil->push($mk->nama." → $labelSesi, $jamMulai-$jamSelesai, ruang $ruang");
        }

        return ['berhasil' => $berhasil, 'gagal' => $gagal];
    }

    /**
     * Kandidat sesi untuk KULIAH: pola mingguan, hanya nama hari, tanpa tanggal.
     * Berulang tiap minggu selama satu semester.
     */
    protected function sesiMingguan(): array
    {
        return collect($this->namaHariKerja)
            ->map(fn ($namaHari) => [
                'key' => $namaHari,        // key unik untuk occupied[]
                'hari' => $namaHari,
                'tanggal' => null,
            ])
            ->values()
            ->all();
    }

    /**
     * Kandidat sesi untuk UJIAN: tanggal kalender asli, mulai dari $tanggalMulai,
     * disebar ke hari-hari kerja berikutnya (Senin-Jumat), melompati Sabtu/Minggu.
     */
    protected function sesiTanggal(string $tanggalMulai): array
    {
        $tanggal = Carbon::parse($tanggalMulai);
        $sesi = [];

        // Iterasi cukup banyak hari kalender untuk memastikan tersedia $jangkauanHariUjian hari kerja
        $batasIterasi = $this->jangkauanHariUjian * 2;

        for ($i = 0; $i < $batasIterasi && count($sesi) < $this->jangkauanHariUjian; $i++) {
            $dow = $tanggal->dayOfWeekIso; // 1=Senin ... 7=Minggu

            if (isset($this->namaHariKerja[$dow])) {
                $sesi[] = [
                    'key' => $tanggal->toDateString(),
                    'hari' => $this->namaHariKerja[$dow],
                    'tanggal' => $tanggal->toDateString(),
                ];
            }

            $tanggal = $tanggal->copy()->addDay();
        }

        return $sesi;
    }

    /**
     * Pencarian slot bebas konflik memakai backtracking sederhana + heuristik penyebaran:
     * sesi (hari/tanggal) yang masih paling kosong dicoba lebih dulu, supaya jadwal
     * tidak menumpuk di satu hari saja. Baru kemudian dicoba tiap kombinasi jam & ruang.
     */
    protected function cariSlotTerbaik(MataKuliah $mk, array &$occupied, array $sesiKandidat, array $mahasiswaMk): ?array
    {
        // Untuk kelas besar, hanya ruang besar yang dipakai
        $ruangKandidat = $mk->jumlahPesertaAktif() > 30
            ? array_filter($this->daftarRuang, fn ($r) => str_starts_with($r, 'R2') || $r === 'R103')
            : $this->daftarRuang;

        if (empty($ruangKandidat)) {
            $ruangKandidat = $this->daftarRuang;
        }

        // Heuristik penyebaran: urutkan sesi dari yang paling sedikit terpakai
        $sesiTerurut = collect($sesiKandidat)
            ->sortBy(function ($sesi) use ($occupied) {
                return isset($occupied[$sesi['key']])
                    ? count($occupied[$sesi['key']], COUNT_RECURSIVE)
                    : 0;
            })
            ->values()
            ->all();

        foreach ($sesiTerurut as $sesi) {
            foreach ($this->slotJam as [$mulai, $selesai]) {
                foreach ($ruangKandidat as $ruang) {
                    if ($this->slotBebasKonflik($sesi['key'], $mulai, $ruang, $mk->dosen_id, $occupied, $mahasiswaMk)) {
                        return ['sesi' => $sesi, 'jam_mulai' => $mulai, 'jam_selesai' => $selesai, 'ruang' => $ruang];
                    }
                }
            }
        }

        return null; // backtrack habis, tidak ada slot valid (semua penuh)
    }

    protected function slotBebasKonflik(
        string $sesiKey,
        string $jamMulai,
        string $ruang,
        ?int $dosenId,
        array $occupied,
        array $mahasiswaMk
    ): bool {
        $dosenTerpakai = $occupied[$sesiKey][$jamMulai]['dosen'] ?? [];
        $ruangTerpakai = $occupied[$sesiKey][$jamMulai]['ruang'] ?? [];
        $mahasiswaTerpakai = $occupied[$sesiKey][$jamMulai]['mahasiswa'] ?? [];

        if ($dosenId !== null && in_array($dosenId, $dosenTerpakai, true)) {
            return false; // constraint 1: dosen bentrok
        }

        if (in_array($ruang, $ruangTerpakai, true)) {
            return false; // constraint 2: ruang bentrok
        }

        if (!empty($mahasiswaMk) && array_intersect($mahasiswaMk, $mahasiswaTerpakai)) {
            return false; // constraint 4: ada mahasiswa yang jadwalnya bentrok
        }

        return true;
    }
}