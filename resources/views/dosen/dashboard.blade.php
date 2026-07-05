@extends('layouts.app')
@section('title', 'Dashboard Dosen')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-slate-800">Selamat datang, {{ auth()->user()->name }} 👨‍🏫</h1>
    <p class="text-slate-500 text-sm">NIDN {{ $dosen->nidn }} · {{ $dosen->bidang_keahlian }}</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
    <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100">
        <p class="text-xs text-slate-500 mb-1">Mata Kuliah Diampu</p>
        <p class="text-3xl font-bold text-emerald-600">{{ $mataKuliah->count() }}</p>
    </div>
    <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100">
        <p class="text-xs text-slate-500 mb-1">Total Mahasiswa</p>
        <p class="text-3xl font-bold text-slate-800">{{ $mataKuliah->sum('krs_count') }}</p>
    </div>
    <a href="{{ route('jadwal.index') }}" class="bg-emerald-600 rounded-2xl p-5 shadow-sm text-white flex flex-col justify-between hover:bg-emerald-700 transition">
        <p class="text-xs text-emerald-100 mb-1">Kelola</p>
        <p class="text-lg font-semibold">Jadwal & Sistem Cerdas →</p>
    </a>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
    <a href="{{ route('perkuliahan.index') }}" class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100 hover:border-emerald-300 transition">
        <p class="font-medium text-slate-700">📚 Kelola Perkuliahan</p>
        <p class="text-xs text-slate-500 mt-1">Tambah mata kuliah baru untuk semester tertentu</p>
    </a>
    <a href="{{ route('krs.indexForDosen') }}" class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100 hover:border-emerald-300 transition">
        <p class="font-medium text-slate-700">✅ Persetujuan KRS</p>
        <p class="text-xs text-slate-500 mt-1">Setujui/tolak pengajuan mata kuliah mahasiswa</p>
    </a>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
    <h2 class="font-semibold text-slate-800 mb-4">Perkuliahan yang Anda Ampu</h2>
    <table class="w-full text-sm">
        <thead>
            <tr class="text-left text-slate-400 border-b border-slate-100">
                <th class="pb-3 font-medium">Kode</th>
                <th class="pb-3 font-medium">Mata Kuliah</th>
                <th class="pb-3 font-medium">SKS</th>
                <th class="pb-3 font-medium">Peserta</th>
                <th class="pb-3 font-medium text-right">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($mataKuliah as $mk)
                <tr class="border-b border-slate-50 last:border-0">
                    <td class="py-3 font-mono text-xs text-slate-500">{{ $mk->kode }}</td>
                    <td class="py-3 font-medium text-slate-700">{{ $mk->nama }}</td>
                    <td class="py-3 text-slate-600">{{ $mk->sks }}</td>
                    <td class="py-3 text-slate-600">{{ $mk->krs_count }} / {{ $mk->kapasitas }}</td>
                    <td class="py-3 text-right">
                        <a href="{{ route('khs.inputForm', $mk) }}" class="text-emerald-600 hover:underline text-sm font-medium">Input Nilai →</a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="py-6 text-center text-slate-400">Belum ada mata kuliah yang diampu.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
