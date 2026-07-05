<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar · SIAKAD</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-[#0f1730] flex items-center justify-center px-4 py-10">
    <div class="w-full max-w-md">
        <div class="text-center mb-6">
            <div class="inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-[#3d5aef] text-white font-bold text-xl mb-3">S</div>
            <h1 class="text-2xl font-bold text-white">Buat Akun SIAKAD</h1>
        </div>

        <div class="bg-white rounded-2xl shadow-xl p-8">
            @if ($errors->any())
                <div class="mb-4 rounded-lg bg-red-50 border border-red-200 text-red-700 px-4 py-3 text-sm space-y-1">
                    @foreach ($errors->all() as $e) <div>{{ $e }}</div> @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('register.submit') }}" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Daftar sebagai</label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="cursor-pointer">
                            <input type="radio" name="role" value="mahasiswa" class="peer sr-only" onchange="toggleRole('mahasiswa')" {{ old('role', 'mahasiswa') == 'mahasiswa' ? 'checked' : '' }}>
                            <div id="btn-mahasiswa" class="text-center rounded-xl border-2 border-[#3d5aef] bg-[#eef4ff] text-[#2f47c9] font-medium py-2.5 transition">🎓 Mahasiswa</div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="role" value="dosen" class="peer sr-only" onchange="toggleRole('dosen')" {{ old('role') == 'dosen' ? 'checked' : '' }}>
                            <div id="btn-dosen" class="text-center rounded-xl border-2 border-slate-200 text-slate-500 font-medium py-2.5 transition">👨‍🏫 Dosen</div>
                        </label>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                        class="w-full rounded-xl border border-slate-300 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-[#3d5aef]">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                        class="w-full rounded-xl border border-slate-300 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-[#3d5aef]">
                </div>

                <!-- Field khusus Mahasiswa -->
                <div id="field-mahasiswa" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">NIM</label>
                        <input type="text" name="nim" value="{{ old('nim') }}"
                            class="w-full rounded-xl border border-slate-300 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-[#3d5aef]">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Program Studi</label>
                        <input type="text" name="program_studi" value="{{ old('program_studi') }}"
                            class="w-full rounded-xl border border-slate-300 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-[#3d5aef]">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Semester</label>
                        <select name="semester" class="w-full rounded-xl border border-slate-300 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-[#3d5aef]">
                            @for($i = 1; $i <= 8; $i++)
                                <option value="{{ $i }}" {{ old('semester') == $i ? 'selected' : '' }}>Semester {{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                </div>

                <!-- Field khusus Dosen -->
                <div id="field-dosen" class="space-y-4 hidden">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">NIDN</label>
                        <input type="text" name="nidn" value="{{ old('nidn') }}"
                            class="w-full rounded-xl border border-slate-300 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-[#3d5aef]">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Bidang Keahlian</label>
                        <input type="text" name="bidang_keahlian" value="{{ old('bidang_keahlian') }}"
                            class="w-full rounded-xl border border-slate-300 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-[#3d5aef]">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Password</label>
                    <input type="password" name="password" required
                        class="w-full rounded-xl border border-slate-300 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-[#3d5aef]">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" required
                        class="w-full rounded-xl border border-slate-300 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-[#3d5aef]">
                </div>

                <button type="submit" class="w-full bg-[#3d5aef] hover:bg-[#2f47c9] text-white font-medium rounded-xl py-2.5 transition">
                    Daftar
                </button>
            </form>

            <p class="text-center text-sm text-slate-500 mt-6">
                Sudah punya akun? <a href="{{ route('login') }}" class="text-[#3d5aef] font-medium hover:underline">Masuk</a>
            </p>
        </div>
    </div>

    <script>
        function toggleRole(role) {
            const btnM = document.getElementById('btn-mahasiswa');
            const btnD = document.getElementById('btn-dosen');
            const fM = document.getElementById('field-mahasiswa');
            const fD = document.getElementById('field-dosen');

            if (role === 'mahasiswa') {
                btnM.className = 'text-center rounded-xl border-2 border-[#3d5aef] bg-[#eef4ff] text-[#2f47c9] font-medium py-2.5 transition';
                btnD.className = 'text-center rounded-xl border-2 border-slate-200 text-slate-500 font-medium py-2.5 transition';
                fM.classList.remove('hidden');
                fD.classList.add('hidden');
            } else {
                btnD.className = 'text-center rounded-xl border-2 border-[#3d5aef] bg-[#eef4ff] text-[#2f47c9] font-medium py-2.5 transition';
                btnM.className = 'text-center rounded-xl border-2 border-slate-200 text-slate-500 font-medium py-2.5 transition';
                fD.classList.remove('hidden');
                fM.classList.add('hidden');
            }
        }
        // set initial state on load based on old input (validation error reload)
        document.addEventListener('DOMContentLoaded', () => {
            const checked = document.querySelector('input[name=role]:checked');
            toggleRole(checked ? checked.value : 'mahasiswa');
        });
    </script>
</body>
</html>
