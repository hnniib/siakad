@extends('layouts.app')
@section('title', 'Input Nilai')

@section('content')
<h1 class="text-2xl font-bold text-slate-800 mb-1">Input Nilai — {{ $mataKuliah->nama }}</h1>
<p class="text-slate-500 text-sm mb-6">{{ $mataKuliah->kode }} · {{ $mataKuliah->sks }} SKS · {{ $pesertaKrs->count() }} peserta disetujui</p>

<div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 overflow-x-auto">
    <table class="w-full text-sm min-w-[700px]">
        <thead>
            <tr class="text-left text-slate-400 border-b border-slate-100">
                <th class="pb-3 font-medium">NIM</th>
                <th class="pb-3 font-medium">Nama</th>
                <th class="pb-3 font-medium">Tugas</th>
                <th class="pb-3 font-medium">UTS</th>
                <th class="pb-3 font-medium">UAS</th>
                <th class="pb-3 font-medium">Akhir</th>
                <th class="pb-3 font-medium">Huruf</th>
                <th class="pb-3 font-medium"></th>
            </tr>
        </thead>
        <tbody>
            @forelse($pesertaKrs as $krs)
                <tr class="border-b border-slate-50 last:border-0">
                    <form method="POST" action="{{ route('khs.store', $krs) }}">
                        @csrf
                        <td class="py-3 font-mono text-xs text-slate-500">{{ $krs->mahasiswa->nim }}</td>
                        <td class="py-3 font-medium text-slate-700">{{ $krs->mahasiswa->user->name }}</td>
                        <td class="py-2"><input type="number" step="0.01" min="0" max="100" name="nilai_tugas" value="{{ $krs->khs->nilai_tugas ?? '' }}" class="w-20 rounded-lg border border-slate-200 px-2 py-1.5"></td>
                        <td class="py-2"><input type="number" step="0.01" min="0" max="100" name="nilai_uts" value="{{ $krs->khs->nilai_uts ?? '' }}" class="w-20 rounded-lg border border-slate-200 px-2 py-1.5"></td>
                        <td class="py-2"><input type="number" step="0.01" min="0" max="100" name="nilai_uas" value="{{ $krs->khs->nilai_uas ?? '' }}" class="w-20 rounded-lg border border-slate-200 px-2 py-1.5"></td>
                        <td class="py-3 text-slate-600">{{ $krs->khs->nilai_akhir ?? '—' }}</td>
                        <td class="py-3">
                            @if($krs->khs?->nilai_huruf)
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-600">{{ $krs->khs->nilai_huruf }}</span>
                            @else
                                <span class="text-xs text-slate-400">—</span>
                            @endif
                        </td>
                        <td class="py-2"><button class="text-xs font-medium bg-emerald-600 text-white px-3 py-1.5 rounded-lg hover:bg-emerald-700">Simpan</button></td>
                    </form>
                </tr>
            @empty
                <tr><td colspan="8" class="py-6 text-center text-slate-400">Belum ada mahasiswa yang KRS-nya disetujui.</td></tr>
            @endforelse
        </tbody>
    </table>
    <p class="text-xs text-slate-400 mt-4">Nilai akhir dihitung otomatis: Tugas 30% + UTS 30% + UAS 40%, lalu dikonversi ke huruf.</p>
</div>
@endsection
