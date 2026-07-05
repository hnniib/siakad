@extends('layouts.app')
@section('title', 'KHS')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Kartu Hasil Studi (KHS)</h1>
        <p class="text-slate-500 text-sm">Rekap nilai mata kuliah yang telah disetujui.</p>
    </div>
    <div class="bg-white rounded-xl px-5 py-3 shadow-sm border border-slate-100 text-right">
        <p class="text-xs text-slate-500">IPK</p>
        <p class="text-2xl font-bold text-[#3d5aef]">{{ number_format($ipk, 2) }}</p>
    </div>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
    <table class="w-full text-sm">
        <thead>
            <tr class="text-left text-slate-400 border-b border-slate-100">
                <th class="pb-3 font-medium">Kode</th>
                <th class="pb-3 font-medium">Mata Kuliah</th>
                <th class="pb-3 font-medium">SKS</th>
                <th class="pb-3 font-medium">Nilai Akhir</th>
                <th class="pb-3 font-medium">Huruf</th>
            </tr>
        </thead>
        <tbody>
            @forelse($khsList as $krs)
                <tr class="border-b border-slate-50 last:border-0">
                    <td class="py-3 font-mono text-xs text-slate-500">{{ $krs->mataKuliah->kode }}</td>
                    <td class="py-3 font-medium text-slate-700">{{ $krs->mataKuliah->nama }}</td>
                    <td class="py-3 text-slate-600">{{ $krs->mataKuliah->sks }}</td>
                    <td class="py-3 text-slate-600">{{ $krs->khs->nilai_akhir ?? '—' }}</td>
                    <td class="py-3">
                        @if($krs->khs && $krs->khs->nilai_huruf)
                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-[#eef4ff] text-[#2f47c9]">{{ $krs->khs->nilai_huruf }}</span>
                        @else
                            <span class="text-xs text-slate-400">Belum dinilai</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="py-6 text-center text-slate-400">Belum ada data KHS.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
