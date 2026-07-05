@extends('layouts.app')
@section('title', 'Perkuliahan')

@section('content')
<h1 class="text-2xl font-bold text-slate-800 mb-1">Perkuliahan</h1>
<p class="text-slate-500 text-sm mb-6">Kelola mata kuliah yang Anda ampu. Mahasiswa hanya bisa mengambil (KRS) mata kuliah yang sesuai dengan semester mereka.</p>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 lg:col-span-1 h-fit">
        <h2 class="font-semibold text-slate-800 mb-4">Tambah Mata Kuliah</h2>
        <form method="POST" action="{{ route('perkuliahan.store') }}" class="space-y-3">
            @csrf
            @if ($errors->any())
                <div class="rounded-lg bg-red-50 border border-red-200 text-red-700 px-3 py-2 text-xs space-y-1">
                    @foreach ($errors->all() as $e) <div>{{ $e }}</div> @endforeach
                </div>
            @endif
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Kode Mata Kuliah</label>
                <input type="text" name="kode" value="{{ old('kode') }}" placeholder="cth. TI701" required
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Nama Mata Kuliah</label>
                <input type="text" name="nama" value="{{ old('nama') }}" placeholder="cth. Sistem Cerdas" required
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">SKS</label>
                    <input type="number" name="sks" min="1" max="6" value="{{ old('sks', 3) }}" required
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Semester</label>
                    <select name="semester" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        @for($i=1;$i<=8;$i++) <option value="{{ $i }}" {{ old('semester')==$i?'selected':'' }}>{{ $i }}</option> @endfor
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Kapasitas Kelas</label>
                <input type="number" name="kapasitas" min="1" value="{{ old('kapasitas', 40) }}" required
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>
            <button class="w-full bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg py-2.5">Tambah Mata Kuliah</button>
        </form>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 lg:col-span-2">
        <h2 class="font-semibold text-slate-800 mb-4">Mata Kuliah Anda</h2>
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-slate-400 border-b border-slate-100">
                    <th class="pb-3 font-medium">Kode</th>
                    <th class="pb-3 font-medium">Nama</th>
                    <th class="pb-3 font-medium">SKS</th>
                    <th class="pb-3 font-medium">Semester</th>
                    <th class="pb-3 font-medium">Peserta</th>
                    <th class="pb-3 font-medium"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($mataKuliah as $mk)
                    <tr class="border-b border-slate-50 last:border-0">
                        <td class="py-3 font-mono text-xs text-slate-500">{{ $mk->kode }}</td>
                        <td class="py-3 font-medium text-slate-700">{{ $mk->nama }}</td>
                        <td class="py-3 text-slate-600">{{ $mk->sks }}</td>
                        <td class="py-3 text-slate-600">{{ $mk->semester }}</td>
                        <td class="py-3 text-slate-600">{{ $mk->krs_count }}/{{ $mk->kapasitas }}</td>
                        <td class="py-3 text-right space-x-3 whitespace-nowrap">
                            <a href="{{ route('khs.inputForm', $mk) }}" class="text-emerald-600 hover:underline text-xs font-medium">Input Nilai</a>
                            <button type="button" class="text-blue-600 hover:underline text-xs font-medium"
                                onclick="bukaEditModal({{ $mk->id }}, '{{ addslashes($mk->kode) }}', '{{ addslashes($mk->nama) }}', {{ $mk->sks }}, {{ $mk->semester }}, {{ $mk->kapasitas }})">
                                Edit
                            </button>
                            <form method="POST" action="{{ route('perkuliahan.destroy', $mk) }}" class="inline" onsubmit="return confirm('Hapus mata kuliah ini?')">
                                @csrf @method('DELETE')
                                <button class="text-red-500 hover:underline text-xs font-medium">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="py-6 text-center text-slate-400">Belum ada mata kuliah. Tambahkan lewat form di samping.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ================= MODAL EDIT MATA KULIAH ================= --}}
<div id="edit-modal" class="hidden fixed inset-0 bg-slate-900/50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-lg w-full max-w-md p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="font-semibold text-slate-800">Edit Mata Kuliah</h2>
            <button type="button" onclick="tutupEditModal()" class="text-slate-400 hover:text-slate-600 text-xl leading-none">&times;</button>
        </div>

        <form id="edit-form" method="POST" action="" class="space-y-3">
            @csrf
            @method('PUT')

            @if ($errors->any())
                <div class="rounded-lg bg-red-50 border border-red-200 text-red-700 px-3 py-2 text-xs space-y-1">
                    @foreach ($errors->all() as $e) <div>{{ $e }}</div> @endforeach
                </div>
            @endif

            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Kode Mata Kuliah</label>
                <input type="text" id="edit-kode" name="kode" required
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Nama Mata Kuliah</label>
                <input type="text" id="edit-nama" name="nama" required
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">SKS</label>
                    <input type="number" id="edit-sks" name="sks" min="1" max="6" required
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Semester</label>
                    <select id="edit-semester" name="semester" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        @for($i=1;$i<=8;$i++) <option value="{{ $i }}">{{ $i }}</option> @endfor
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Kapasitas Kelas</label>
                <input type="number" id="edit-kapasitas" name="kapasitas" min="1" required
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            <div class="flex gap-2 pt-2">
                <button type="button" onclick="tutupEditModal()" class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-medium rounded-lg py-2.5">Batal</button>
                <button type="submit" class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg py-2.5">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
function bukaEditModal(id, kode, nama, sks, semester, kapasitas) {
    document.getElementById('edit-form').action = '/perkuliahan/' + id;
    document.getElementById('edit-kode').value = kode;
    document.getElementById('edit-nama').value = nama;
    document.getElementById('edit-sks').value = sks;
    document.getElementById('edit-semester').value = semester;
    document.getElementById('edit-kapasitas').value = kapasitas;
    document.getElementById('edit-modal').classList.remove('hidden');
}

function tutupEditModal() {
    document.getElementById('edit-modal').classList.add('hidden');
}
</script>
@endsection