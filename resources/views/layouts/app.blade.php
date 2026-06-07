<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Sistem Manajemen Barbershop - Mr. Klimis</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-900 text-gray-100">
    <div class="flex min-h-screen bg-gradient-to-br from-gray-900 via-gray-900 to-slate-900">

        <aside class="w-64 bg-gray-800/40 backdrop-blur-md border-r border-gray-700/50 flex flex-col justify-between sticky top-0 h-screen z-50">
            <div>
                <div class="h-16 flex items-center px-6 border-b border-gray-700/50">
                    <span class="text-xl font-black tracking-widest text-white uppercase">
                        ✂️ MR. KLIMIS
                    </span>
                </div>

                <nav class="p-4 space-y-1.5">
                    <div class="text-[10px] font-bold text-gray-500 uppercase tracking-wider px-3 mb-2">Menu Utama</div>

                    <a href="{{ route('dashboard') }}"
                       class="flex items-center space-x-3 px-3 py-2.5 rounded-lg text-sm font-medium transition
                            {{ request()->routeIs('dashboard') ? 'bg-blue-600 text-white shadow-lg' : 'text-gray-400 hover:bg-gray-700/50 hover:text-gray-200' }}">
                        <span>📋</span>
                        <span>Daftar Pesanan</span>
                    </a>

                    <a href="/dashboard/barbers"
                       class="flex items-center space-x-3 px-3 py-2.5 rounded-lg text-sm font-medium transition
                            {{ request()->is('dashboard/barbers*') ? 'bg-blue-600 text-white shadow-lg' : 'text-gray-400 hover:bg-gray-700/50 hover:text-gray-200' }}">
                        <span>💈</span>
                        <span>Daftar Barber</span>
                    </a>
                </nav>
            </div>

            <div class="p-4 border-t border-gray-700/50 space-y-3">
                <a href="/profile" class="block px-3 py-2 rounded-lg text-sm transition group
                    {{ request()->routeIs('profile') ? 'bg-gray-700/50 text-white border border-gray-600/50 shadow' : 'text-gray-400 hover:bg-gray-750 hover:text-gray-200' }}">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-sm font-semibold group-hover:text-blue-400 transition">{{ Auth::user()->name }}</div>
                            <div class="text-[10px] text-gray-500 group-hover:text-gray-400">Pengaturan Akun</div>
                        </div>
                        <span class="text-xs text-gray-500 group-hover:text-gray-300">⚙️</span>
                    </div>
                </a>

                <button type="button"
                        onclick="if(confirm('Apakah Anda yakin ingin keluar dari aplikasi admin Mr. Klimis?')) { Livewire.dispatch('trigger-breeze-logout'); }"
                        class="w-full px-3 py-2 rounded-lg text-xs font-semibold bg-red-500/10 hover:bg-red-500/20 text-red-400 border border-red-500/20 transition cursor-pointer text-left flex items-center space-x-2">
                    <span>🚪</span>
                    <span>Keluar Aplikasi</span>
                </button>
            </div>
        </aside>

        <main class="flex-1 overflow-y-auto h-screen p-8">
            {{ $slot }}
        </main>

    </div>

    <livewire:breeze-logout-bridge />

</body>
</html>
