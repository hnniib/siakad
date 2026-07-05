@extends('layouts.app')
@section('title', 'Jadwal Kuliah & Ujian')

@section('content')
<h1 class="text-2xl font-bold text-slate-800 mb-1">Jadwal Kuliah & Ujian</h1>
<p class="text-slate-500 text-sm mb-6">Jadwal berdasarkan mata kuliah yang sudah disetujui di KRS kamu.</p>

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
        <p class="text-sm text-slate-400 col-span-full">Belum ada jadwal kuliah. Pastikan KRS kamu sudah disetujui dosen.</p>
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
        <p class="text-sm text-slate-400 col-span-full">Belum ada jadwal UTS.</p>
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
        <p class="text-sm text-slate-400 col-span-full">Belum ada jadwal UAS.</p>
    @endforelse
</div>
@endsection