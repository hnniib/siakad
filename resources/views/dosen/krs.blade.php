@extends('layouts.app')
@section('title', 'Persetujuan KRS')

@section('content')
<h1 class="text-2xl font-bold text-slate-800 mb-1">Persetujuan KRS Mahasiswa</h1>
<p class="text-slate-500 text-sm mb-6">Setujui atau tolak pengajuan mata kuliah dari mahasiswa. KRS yang <span class="font-medium text-emerald-600">disetujui</span> baru bisa diberi nilai (KHS) dan muncul di jadwal mahasiswa.</p>

<div class="space-y-6">
    @forelse($krsList as $namaMk => $items)
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
            <h2 class="font-semibold text-slate-800 mb-4">{{ $namaMk }}</h2>
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-slate-400 border-b border-slate-100">
                        <th class="pb-3 font-medium">NIM</th>
                        <th class="pb-3 font-medium">Nama</th>
                        <th class="pb-3 font-medium">Status</th>
                        <th class="pb-3 font-medium text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($items as $krs)
                        <tr class="border-b border-slate-50 last:border-0">
                            <td class="py-3 font-mono text-xs text-slate-500">{{ $krs->mahasiswa->nim }}</td>
                            <td class="py-3 font-medium text-slate-700">{{ $krs->mahasiswa->user->name }}</td>
                            <td class="py-3">
                                <span @class([
                                    'text-xs px-2.5 py-1 rounded-full font-medium',
                                    'bg-amber-50 text-amber-600' => $krs->status === 'diajukan',
                                    'bg-emerald-50 text-emerald-600' => $krs->status === 'disetujui',
                                    'bg-red-50 text-red-600' => $krs->status === 'ditolak',
                                ])>{{ ucfirst($krs->status) }}</span>
                            </td>
                            <td class="py-3 text-right space-x-2">
                                @if($krs->status !== 'disetujui')
                                    <form method="POST" action="{{ route('krs.updateStatus', $krs) }}" class="inline">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="status" value="disetujui">
                                        <button class="text-xs font-medium bg-emerald-600 text-white px-3 py-1.5 rounded-lg hover:bg-emerald-700">Setujui</button>
                                    </form>
                                @endif
                                @if($krs->status !== 'ditolak')
                                    <form method="POST" action="{{ route('krs.updateStatus', $krs) }}" class="inline">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="status" value="ditolak">
                                        <button class="text-xs font-medium bg-red-500 text-white px-3 py-1.5 rounded-lg hover:bg-red-600">Tolak</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @empty
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 text-center text-slate-400">
            Belum ada mahasiswa yang mengajukan KRS untuk mata kuliah Anda.
        </div>
    @endforelse
</div>
@endsection
