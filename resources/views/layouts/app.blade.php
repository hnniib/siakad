<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SIAKAD')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
      tailwind.config = {
        theme: {
          extend: {
            colors: {
              slate: { 950: '#0b1220' },
              brand: { 50:'#eef4ff',100:'#dbe6fe',500:'#3d5aef',600:'#2f47c9',700:'#26399e',900:'#101b45' }
            },
            fontFamily: { sans: ['Inter','ui-sans-serif','system-ui'] }
          }
        }
      }
    </script>
    <style> body { font-family: 'Inter', ui-sans-serif, system-ui; } </style>
</head>
<body class="bg-slate-50 text-slate-800 min-h-screen">

@auth
    @php $u = auth()->user(); @endphp
    <nav class="bg-brand-900 text-white">
        <div class="max-w-6xl mx-auto px-4 flex items-center justify-between h-16">
            <div class="flex items-center gap-8">
                <span class="font-bold tracking-tight text-lg">SIAKAD <span class="text-brand-100 font-normal">· {{ $u->isDosen() ? 'Portal Dosen' : 'Portal Mahasiswa' }}</span></span>
                <div class="hidden md:flex gap-1 text-sm">
                    <a href="{{ route('dashboard') }}" class="px-3 py-2 rounded-lg hover:bg-brand-700 {{ request()->routeIs('dashboard') ? 'bg-brand-700' : '' }}">Dashboard</a>
                    @if($u->isMahasiswa())
                        <a href="{{ route('krs.index') }}" class="px-3 py-2 rounded-lg hover:bg-brand-700 {{ request()->routeIs('krs.*') ? 'bg-brand-700' : '' }}">KRS</a>
                        <a href="{{ route('khs.index') }}" class="px-3 py-2 rounded-lg hover:bg-brand-700 {{ request()->routeIs('khs.*') ? 'bg-brand-700' : '' }}">KHS</a>
                    @endif
                    @if($u->isDosen())
                        <a href="{{ route('perkuliahan.index') }}" class="px-3 py-2 rounded-lg hover:bg-brand-700 {{ request()->routeIs('perkuliahan.*') ? 'bg-brand-700' : '' }}">Perkuliahan</a>
                        <a href="{{ route('krs.indexForDosen') }}" class="px-3 py-2 rounded-lg hover:bg-brand-700 {{ request()->routeIs('krs.indexForDosen') ? 'bg-brand-700' : '' }}">Persetujuan KRS</a>
                    @endif
                    <a href="{{ route('jadwal.index') }}" class="px-3 py-2 rounded-lg hover:bg-brand-700 {{ request()->routeIs('jadwal.*') ? 'bg-brand-700' : '' }}">Jadwal</a>
                </div>
            </div>
            <div class="flex items-center gap-4 text-sm">
                <span class="text-brand-100">{{ $u->name }}</span>
                <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $u->isDosen() ? 'bg-emerald-500' : 'bg-brand-500' }}">{{ ucfirst($u->role) }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="px-3 py-1.5 rounded-lg bg-white/10 hover:bg-white/20">Keluar</button>
                </form>
            </div>
        </div>
    </nav>
@endauth

<main class="max-w-6xl mx-auto px-4 py-8">
    @if (session('success'))
        <div class="mb-6 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 text-sm">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="mb-6 rounded-xl bg-red-50 border border-red-200 text-red-700 px-4 py-3 text-sm">{{ session('error') }}</div>
    @endif

    @yield('content')
</main>

</body>
</html>
