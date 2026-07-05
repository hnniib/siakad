@extends('layouts.app')
@section('title', 'KRS')

@section('content')
<h1 class="text-2xl font-bold text-slate-800 mb-1">Kartu Rencana Studi (KRS)</h1>
<p class="text-slate-500 text-sm mb-6">Semester {{ $mahasiswa->semester }} · Pilih mata kuliah yang ingin diambil.</p>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
        <h2 class="font-semibold text-slate-800 mb-4">Mata Kuliah Tersedia</h2>
        <div class="space-y-3">
            @forelse($mataKuliahTersedia as $mk)
                <div class="flex items-center justify-between border border-slate-100 rounded-xl p-3.5">
                    <div>
                        <p class="font-medium text-slate-700 text-sm">{{ $mk->nama }}</p>
                        <p class="text-xs text-slate-400">{{ $mk->kode }} · {{ $mk->sks }} SKS · {{ $mk->krs_count }}/{{ $mk->kapasitas }} peserta</p>
                    </div>
                    <form method="POST" action="{{ route('krs.store') }}">
                        @csrf
                        <input type="hidden" name="mata_kuliah_id" value="{{ $mk->id }}">
                        <button class="text-xs font-medium bg-[#3d5aef] text-white px-3 py-1.5 rounded-lg hover:bg-[#2f47c9]">Ambil</button>
                    </form>
                </div>
            @empty
                <p class="text-sm text-slate-400">Tidak ada mata kuliah tersedia untuk semester ini.</p>
            @endforelse
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
        <h2 class="font-semibold text-slate-800 mb-4">KRS Saya</h2>
        <div class="space-y-3">
            @forelse($krsSaya as $krs)
                <div class="flex items-center justify-between border border-slate-100 rounded-xl p-3.5">
                    <div>
                        <p class="font-medium text-slate-700 text-sm">{{ $krs->mataKuliah->nama }}</p>
                        <p class="text-xs text-slate-400">{{ $krs->mataKuliah->sks }} SKS</p>
                        <span @class([
                            'text-xs px-2 py-0.5 rounded-full font-medium inline-block mt-1',
                            'bg-amber-50 text-amber-600' => $krs->status === 'diajukan',
                            'bg-emerald-50 text-emerald-600' => $krs->status === 'disetujui',
                            'bg-red-50 text-red-600' => $krs->status === 'ditolak',
                        ])>{{ ucfirst($krs->status) }}</span>
                    </div>
                    @if($krs->status !== 'disetujui')
                        <form method="POST" action="{{ route('krs.destroy', $krs) }}" onsubmit="return confirm('Batalkan mata kuliah ini?')">
                            @csrf @method('DELETE')
                            <button class="text-xs text-red-500 hover:underline">Batalkan</button>
                        </form>
                    @endif
                </div>
            @empty
                <p class="text-sm text-slate-400">Anda belum mengambil mata kuliah.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
