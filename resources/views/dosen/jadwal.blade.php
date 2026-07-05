@extends('layouts.app')
@section('title', 'Jadwal & Sistem Cerdas')

@section('content')
<h1 class="text-2xl font-bold text-slate-800 mb-1">Jadwal Kuliah & Ujian</h1>
<p class="text-slate-500 text-sm mb-6">Kelola penjadwalan otomatis berbasis Sistem Cerdas (CSP + backtracking).</p>

<div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 mb-8">
    <h2 class="font-semibold text-slate-800 mb-1">🧠 Sistem Cerdas: Generator Jadwal Otomatis</h2>
    <p class="text-sm text-slate-500 mb-4">
        Algoritma mencari kombinasi sesi/jam/ruang yang bebas konflik dosen, ruang, & mahasiswa, lalu menyebar
        jadwal ke seluruh hari kerja (bukan menumpuk di satu hari). Kuliah berpola mingguan (berulang tiap minggu);
        UTS/UAS berpola tanggal kalender asli mulai dari tanggal yang kamu pilih.
    </p>

    <form method="POST" action="{{ route('jadwal.generate') }}" class="flex flex-wrap items-end gap-3">
        @csrf
        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">Semester</label>
            <select name="semester" class="rounded-lg border border-slate-200 px-3 py-2 text-sm">
                @for($i=1;$i<=8;$i++) <option value="{{ $i }}" {{ $i==6?'selected':'' }}>Semester {{ $i }}</option> @endfor
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">Jenis</label>
            <select name="jenis" id="jenis-select" class="rounded-lg border border-slate-200 px-3 py-2 text-sm" onchange="toggleTanggalMulai(this.value)">
                <option value="kuliah">Jadwal Kuliah</option>
                <option value="ujian_uts">Jadwal UTS</option>
                <option value="ujian_uas">Jadwal UAS</option>
            </select>
        </div>
        <div id="tanggal-mulai-wrapper" class="hidden">
            <label class="block text-xs font-medium text-slate-600 mb-1">Tanggal Mulai Ujian</label>
            <input type="date" name="tanggal_mulai" class="rounded-lg border border-slate-200 px-3 py-2 text-sm">
        </div>
        <button class="bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium px-4 py-2 rounded-lg">⚡ Generate Otomatis</button>
    </form>

    @error('tanggal_mulai')
        <p class="mt-3 text-xs text-red-600">Tanggal mulai wajib diisi untuk jadwal UTS/UAS.</p>
    @enderror

    <div class="mt-4 pt-4 border-t border-slate-100">
        <form method="POST" action="{{ route('jadwal.hapus') }}" class="flex flex-wrap items-end gap-3"
              onsubmit="return confirm('Yakin mau hapus semua jadwal hasil generate otomatis untuk semester & jenis ini? Aksi ini tidak bisa dibatalkan.');">
            @csrf
            @method('DELETE')
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Semester</label>
                <select name="semester" class="rounded-lg border border-slate-200 px-3 py-2 text-sm">
                    @for($i=1;$i<=8;$i++) <option value="{{ $i }}" {{ $i==6?'selected':'' }}>Semester {{ $i }}</option> @endfor
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Jenis</label>
                <select name="jenis" class="rounded-lg border border-slate-200 px-3 py-2 text-sm">
                    <option value="kuliah">Jadwal Kuliah</option>
                    <option value="ujian_uts">Jadwal UTS</option>
                    <option value="ujian_uas">Jadwal UAS</option>
                </select>
            </div>
            <button class="bg-red-50 hover:bg-red-100 text-red-700 text-sm font-medium px-4 py-2 rounded-lg border border-red-200">🗑️ Hapus Jadwal</button>
        </form>
    </div>

    @if(session('success'))
        <div class="mt-4 bg-emerald-50 rounded-xl p-3 text-sm text-emerald-700">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mt-4 bg-red-50 rounded-xl p-3 text-sm text-red-700">{{ session('error') }}</div>
    @endif

    @if(session('hasil_generate'))
        @php $hasil = session('hasil_generate'); @endphp
        @if($hasil['berhasil']->isEmpty() && $hasil['gagal']->isEmpty())
            <div class="mt-5 bg-amber-50 rounded-xl p-4 text-sm text-amber-700">
                ⚠️ Tidak ada mata kuliah ditemukan untuk semester yang dipilih. Tambahkan mata kuliah dulu lewat halaman <a href="{{ route('perkuliahan.index') }}" class="underline font-medium">Perkuliahan</a>.
            </div>
        @endif
        <div class="mt-5 grid md:grid-cols-2 gap-4 text-sm">
            <div class="bg-emerald-50 rounded-xl p-4">
                <p class="font-medium text-emerald-700 mb-2">✅ Berhasil ({{ $hasil['berhasil']->count() }})</p>
                <ul class="text-emerald-700 space-y-1 text-xs">
                    @foreach($hasil['berhasil'] as $b) <li>{{ $b }}</li> @endforeach
                </ul>
            </div>
            @if($hasil['gagal']->count())
            <div class="bg-red-50 rounded-xl p-4">
                <p class="font-medium text-red-700 mb-2">⚠️ Gagal ({{ $hasil['gagal']->count() }})</p>
                <ul class="text-red-700 space-y-1 text-xs">
                    @foreach($hasil['gagal'] as $g) <li>{{ $g }}</li> @endforeach
                </ul>
            </div>
            @endif
        </div>
    @endif
</div>

@php
    $labelJenis = ['kuliah' => 'Kuliah', 'ujian_uts' => 'UTS', 'ujian_uas' => 'UAS'];
    $warnaJenis = [
        'kuliah' => 'bg-blue-50 text-blue-700',
        'ujian_uts' => 'bg-amber-50 text-amber-700',
        'ujian_uas' => 'bg-rose-50 text-rose-700',
    ];
    $namaBulan = [1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'];
@endphp

{{-- ================= JADWAL KULIAH (MINGGUAN) ================= --}}
<h2 class="font-semibold text-slate-800 mb-3">📅 Jadwal Kuliah (berulang tiap minggu)</h2>
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-10">
    @forelse($jadwalKuliah as $hari => $items)
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5">
            <h3 class="font-semibold text-slate-800 mb-3">{{ $hari }}</h3>
            <div class="space-y-2.5">
                @foreach($items as $j)
                    <div class="border border-slate-100 rounded-xl p-3">
                        <div class="flex items-center justify-between gap-2 mb-1">
                            <p class="text-sm font-medium text-slate-700">{{ $j->mataKuliah->nama }}</p>
                            <span class="shrink-0 text-[10px] font-semibold px-2 py-0.5 rounded-full {{ $warnaJenis['kuliah'] }}">
                                {{ $labelJenis['kuliah'] }}
                            </span>
                        </div>
                        <p class="text-xs text-slate-400">{{ substr($j->jam_mulai,0,5) }} - {{ substr($j->jam_selesai,0,5) }} · Ruang {{ $j->ruang }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    @empty
        <p class="text-sm text-slate-400 col-span-full">Belum ada jadwal kuliah. Pilih jenis "Jadwal Kuliah" lalu klik "Generate Otomatis" di atas.</p>
    @endforelse
</div>

{{-- ================= JADWAL UTS (PER TANGGAL) ================= --}}
<h2 class="font-semibold text-slate-800 mb-3">📝 Jadwal UTS (per tanggal)</h2>
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-10">
    @forelse($jadwalUTS as $tanggalKey => $items)
        @php
            $contoh = $items->first();
            $tgl = $contoh->tanggal;
            $labelTanggal = $tgl ? $contoh->hari.', '.$tgl->day.' '.$namaBulan[$tgl->month].' '.$tgl->year : '-';
        @endphp
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5">
            <h3 class="font-semibold text-slate-800 mb-3">{{ $labelTanggal }}</h3>
            <div class="space-y-2.5">
                @foreach($items as $j)
                    <div class="border border-slate-100 rounded-xl p-3">
                        <div class="flex items-center justify-between gap-2 mb-1">
                            <p class="text-sm font-medium text-slate-700">{{ $j->mataKuliah->nama }}</p>
                            <span class="shrink-0 text-[10px] font-semibold px-2 py-0.5 rounded-full {{ $warnaJenis['ujian_uts'] }}">
                                {{ $labelJenis['ujian_uts'] }}
                            </span>
                        </div>
                        <p class="text-xs text-slate-400">{{ substr($j->jam_mulai,0,5) }} - {{ substr($j->jam_selesai,0,5) }} · Ruang {{ $j->ruang }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    @empty
        <p class="text-sm text-slate-400 col-span-full">Belum ada jadwal UTS. Pilih jenis "UTS", isi tanggal mulai, lalu klik "Generate Otomatis" di atas.</p>
    @endforelse
</div>

{{-- ================= JADWAL UAS (PER TANGGAL) ================= --}}
<h2 class="font-semibold text-slate-800 mb-3">📝 Jadwal UAS (per tanggal)</h2>
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
    @forelse($jadwalUAS as $tanggalKey => $items)
        @php
            $contoh = $items->first();
            $tgl = $contoh->tanggal;
            $labelTanggal = $tgl ? $contoh->hari.', '.$tgl->day.' '.$namaBulan[$tgl->month].' '.$tgl->year : '-';
        @endphp
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5">
            <h3 class="font-semibold text-slate-800 mb-3">{{ $labelTanggal }}</h3>
            <div class="space-y-2.5">
                @foreach($items as $j)
                    <div class="border border-slate-100 rounded-xl p-3">
                        <div class="flex items-center justify-between gap-2 mb-1">
                            <p class="text-sm font-medium text-slate-700">{{ $j->mataKuliah->nama }}</p>
                            <span class="shrink-0 text-[10px] font-semibold px-2 py-0.5 rounded-full {{ $warnaJenis['ujian_uas'] }}">
                                {{ $labelJenis['ujian_uas'] }}
                            </span>
                        </div>
                        <p class="text-xs text-slate-400">{{ substr($j->jam_mulai,0,5) }} - {{ substr($j->jam_selesai,0,5) }} · Ruang {{ $j->ruang }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    @empty
        <p class="text-sm text-slate-400 col-span-full">Belum ada jadwal UAS. Pilih jenis "UAS", isi tanggal mulai, lalu klik "Generate Otomatis" di atas.</p>
    @endforelse
</div>

<script>
function toggleTanggalMulai(jenis) {
    const wrapper = document.getElementById('tanggal-mulai-wrapper');
    if (jenis === 'kuliah') {
        wrapper.classList.add('hidden');
    } else {
        wrapper.classList.remove('hidden');
    }
}
// Set kondisi awal saat halaman dimuat
document.addEventListener('DOMContentLoaded', function () {
    toggleTanggalMulai(document.getElementById('jenis-select').value);
});
</script>
@endsection