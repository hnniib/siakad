<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk · SIAKAD</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-[#0f1730] flex items-center justify-center px-4">
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <div class="inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-[#3d5aef] text-white font-bold text-xl mb-3">S</div>
            <h1 class="text-2xl font-bold text-white">SIAKAD</h1>
            <p class="text-slate-400 text-sm mt-1">Sistem Informasi Akademik</p>
        </div>

        <div class="bg-white rounded-2xl shadow-xl p-8">
            <h2 class="text-lg font-semibold text-slate-800 mb-1">Masuk ke akun Anda</h2>

            @if ($errors->any())
                <div class="mb-4 rounded-lg bg-red-50 border border-red-200 text-red-700 px-4 py-3 text-sm">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus
                        class="w-full rounded-xl border border-slate-300 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-[#3d5aef] focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Password</label>
                    <input type="password" name="password" required
                        class="w-full rounded-xl border border-slate-300 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-[#3d5aef] focus:border-transparent">
                </div>
                <label class="flex items-center gap-2 text-sm text-slate-600">
                    <input type="checkbox" name="remember" class="rounded"> Ingat saya
                </label>
                <button type="submit" class="w-full bg-[#3d5aef] hover:bg-[#2f47c9] text-white font-medium rounded-xl py-2.5 transition">
                    Masuk
                </button>
            </form>

            <p class="text-center text-sm text-slate-500 mt-6">
                Belum punya akun? <a href="{{ route('register') }}" class="text-[#3d5aef] font-medium hover:underline">Daftar</a>
            </p>
    </div>
</body>
</html>
