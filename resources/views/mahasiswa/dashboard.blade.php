@extends('layouts.app')
@section('title', 'Dashboard Mahasiswa')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-slate-800">Halo, {{ auth()->user()->name }} 👋</h1>
    <p class="text-slate-500 text-sm">{{ $mahasiswa->program_studi ?? '-' }} · Semester {{ $mahasiswa->semester ?? '-' }} · NIM {{ $mahasiswa->nim ?? '-' }}</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
    <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100">
        <p class="text-xs text-slate-500 mb-1">IPK Saat Ini</p>
        <p class="text-3xl font-bold text-[#3d5aef]">{{ number_format($ipk, 2) }}</p>
    </div>
    <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100">
        <p class="text-xs text-slate-500 mb-1">Mata Kuliah Diambil (KRS)</p>
        <p class="text-3xl font-bold text-slate-800">{{ $krsAktif->count() }}</p>
    </div>
    <a href="{{ route('jadwal.index') }}" class="bg-[#3d5aef] rounded-2xl p-5 shadow-sm text-white flex flex-col justify-between hover:bg-[#2f47c9] transition">
        <p class="text-xs text-brand-100 mb-1">Lihat</p>
        <p class="text-lg font-semibold">Jadwal Kuliah & Ujian →</p>
    </a>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="font-semibold text-slate-800">KRS Terakhir</h2>
            <a href="{{ route('krs.index') }}" class="text-sm text-[#3d5aef] hover:underline">Kelola KRS →</a>
        </div>
        @forelse($krsAktif as $krs)
            <div class="flex items-center justify-between py-2.5 border-b border-slate-50 last:border-0">
                <div>
                    <p class="font-medium text-slate-700 text-sm">{{ $krs->mataKuliah->nama }}</p>
                    <p class="text-xs text-slate-400">{{ $krs->mataKuliah->kode }} · {{ $krs->mataKuliah->sks }} SKS</p>
                </div>
                <span @class([
                    'text-xs px-2.5 py-1 rounded-full font-medium',
                    'bg-amber-50 text-amber-600' => $krs->status === 'diajukan',
                    'bg-emerald-50 text-emerald-600' => $krs->status === 'disetujui',
                    'bg-red-50 text-red-600' => $krs->status === 'ditolak',
                ])>{{ ucfirst($krs->status) }}</span>
            </div>
        @empty
            <p class="text-sm text-slate-400">Belum ada mata kuliah di KRS.</p>
        @endforelse
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
        <h2 class="font-semibold text-slate-800 mb-4">Menu Cepat</h2>
        <div class="grid grid-cols-2 gap-3">
            <a href="{{ route('krs.index') }}" class="border border-slate-200 rounded-xl p-4 hover:border-[#3d5aef] hover:bg-[#eef4ff] transition text-center">
                <p class="text-sm font-medium text-slate-700">📋 KRS</p>
            </a>
            <a href="{{ route('khs.index') }}" class="border border-slate-200 rounded-xl p-4 hover:border-[#3d5aef] hover:bg-[#eef4ff] transition text-center">
                <p class="text-sm font-medium text-slate-700">📊 KHS</p>
            </a>
        </div>
    </div>
</div>
@endsection
