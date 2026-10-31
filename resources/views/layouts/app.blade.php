<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Lumina Grand') — Sistem Operasional Hotel</title>
    
    <!-- Google Fonts: Plus Jakarta Sans & Yellowtail -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Yellowtail&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS CDN with Lumina Grand Config -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', '-apple-system', 'BlinkMacSystemFont', 'sans-serif'],
                        script: ['"Yellowtail"', 'cursive'],
                    },
                    colors: {
                        navy: {
                            950: '#070D18',
                            900: '#0A1526',
                            800: '#0F1D38',
                            700: '#172B52',
                        },
                        gold: {
                            300: '#E6C587',
                            400: '#D4A373',
                            500: '#C69B56',
                            600: '#A97D38',
                        },
                        warm: {
                            50: '#FCFAF7',
                            100: '#F7F4EE',
                            200: '#EFEAE0',
                        },
                        charcoal: '#0A1526',
                        canvas: '#FCFAF7',
                        surface: '#FFFFFF',
                        borderWarm: '#EFEAE0',
                        frame: '#F7F4EE',
                    },
                    borderRadius: {
                        'xl': '12px',
                        '2xl': '16px',
                        '3xl': '24px',
                        '4xl': '32px',
                    },
                    boxShadow: {
                        'card': '0 1px 3px 0 rgba(15, 29, 56, 0.05), 0 1px 2px -1px rgba(15, 29, 56, 0.05)',
                        'hover': '0 20px 25px -5px rgba(15, 29, 56, 0.08), 0 8px 10px -6px rgba(15, 29, 56, 0.04)',
                        'float': '0 20px 40px -15px rgba(15, 29, 56, 0.08), 0 0 1px 1px rgba(15, 29, 56, 0.05)',
                    }
                }
            }
        }
    </script>
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; padding: 0 !important; }
            .canvas-container { border: none !important; box-shadow: none !important; border-radius: 0 !important; background: white !important; }
            .print-only { display: block !important; }
        }
        .script-accent {
            font-family: 'Yellowtail', cursive;
            font-weight: 400;
            color: #C69B56;
        }
    </style>
</head>
<body class="bg-warm-100 text-navy-900 min-h-screen p-2 sm:p-5 lg:p-6 flex justify-center items-start font-sans antialiased selection:bg-gold-300/30 selection:text-navy-950">

    <!-- Main Outer Framed Canvas Container -->
    <div class="canvas-container w-full max-w-[1550px] bg-warm-50 rounded-3xl sm:rounded-4xl shadow-xl border border-warm-200 overflow-hidden flex flex-col min-h-[94vh]">

        <!-- Top Header & Navigation Bar -->
        <header class="bg-warm-50/90 backdrop-blur-md px-4 sm:px-8 py-4 border-b border-warm-200 no-print sticky top-0 z-40">
            <div class="flex flex-wrap items-center justify-between gap-4">
                
                <!-- Brand / Logo (Left) -->
                @php
                    $brandTarget = url('/');
                    if (auth()->check()) {
                        $brandTarget = match(auth()->user()->peran) {
                            'Tamu' => route('pelanggan.kamar'),
                            'Manajer' => route('manajer.kamar.index'),
                            'Resepsionis' => route('resepsionis.dashboard'),
                            'Petugas Kebersihan' => route('kebersihan.index'),
                            default => route('pelanggan.kamar'),
                        };
                    }
                @endphp
                <div class="flex items-center space-x-3">
                    <a href="{{ $brandTarget }}" class="flex items-center space-x-3 group">
                        <div class="w-10 h-10 rounded-xl bg-navy-900 border border-gold-500/30 flex items-center justify-center text-gold-300 shadow-sm group-hover:bg-navy-800 transition">
                            <!-- Architectural Geometric Hotel Icon -->
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                        <div>
                            <span class="font-bold text-lg tracking-wider text-navy-900 block leading-tight">LUMINA GRAND</span>
                            <span class="text-[10px] uppercase font-bold text-gold-600 tracking-widest block">Operasional &bull; HotelOps</span>
                        </div>
                    </a>
                </div>

                <!-- Navigation Tabs (Center Pill Bar) -->
                @auth
                    <nav class="flex items-center bg-warm-100/90 p-1.5 rounded-full border border-warm-200 text-xs font-semibold shadow-inner">
                        @if(auth()->user()->peran === 'Tamu')
                            <a href="{{ route('pelanggan.kamar') }}" 
                               class="px-4 py-2 rounded-full transition relative {{ request()->routeIs('pelanggan.kamar') || request()->routeIs('pelanggan.index') ? 'bg-white text-navy-900 shadow-sm font-bold' : 'text-navy-700/70 hover:text-navy-900' }}">
                                Katalog Kamar
                                @if(request()->routeIs('pelanggan.kamar') || request()->routeIs('pelanggan.index'))
                                    <span class="absolute bottom-1 left-4 right-4 h-0.5 bg-gold-500 rounded-full"></span>
                                @endif
                            </a>
                            <a href="{{ route('booking.form') }}" 
                               class="px-4 py-2 rounded-full transition relative {{ request()->routeIs('booking.form') ? 'bg-white text-navy-900 shadow-sm font-bold' : 'text-navy-700/70 hover:text-navy-900' }}">
                                Formulir Pemesanan
                                @if(request()->routeIs('booking.form'))
                                    <span class="absolute bottom-1 left-4 right-4 h-0.5 bg-gold-500 rounded-full"></span>
                                @endif
                            </a>
                            <a href="{{ route('pelanggan.dashboard') }}" 
                               class="px-4 py-2 rounded-full transition relative {{ request()->routeIs('pelanggan.dashboard') || request()->routeIs('pelanggan.riwayat') ? 'bg-white text-navy-900 shadow-sm font-bold' : 'text-navy-700/70 hover:text-navy-900' }}">
                                Riwayat Reservasi
                                @if(request()->routeIs('pelanggan.dashboard') || request()->routeIs('pelanggan.riwayat'))
                                    <span class="absolute bottom-1 left-4 right-4 h-0.5 bg-gold-500 rounded-full"></span>
                                @endif
                            </a>
                            <a href="{{ route('pelanggan.profil') }}" 
                               class="px-4 py-2 rounded-full transition relative {{ request()->routeIs('pelanggan.profil*') ? 'bg-white text-navy-900 shadow-sm font-bold' : 'text-navy-700/70 hover:text-navy-900' }}">
                                Profil Saya
                                @if(request()->routeIs('pelanggan.profil*'))
                                    <span class="absolute bottom-1 left-4 right-4 h-0.5 bg-gold-500 rounded-full"></span>
                                @endif
                            </a>
                        @endif

                        @if(auth()->user()->peran === 'Resepsionis' || auth()->user()->peran === 'Manajer')
                            <a href="{{ route('resepsionis.dashboard') }}" 
                               class="px-4 py-2 rounded-full transition relative {{ request()->routeIs('resepsionis.dashboard') ? 'bg-white text-navy-900 shadow-sm font-bold' : 'text-navy-700/70 hover:text-navy-900' }}">
                                Denah Kamar
                                @if(request()->routeIs('resepsionis.dashboard'))
                                    <span class="absolute bottom-1 left-4 right-4 h-0.5 bg-gold-500 rounded-full"></span>
                                @endif
                            </a>
                            <a href="{{ route('resepsionis.pemesanan.index') }}" 
                               class="px-4 py-2 rounded-full transition relative {{ request()->routeIs('resepsionis.pemesanan.*') ? 'bg-white text-navy-900 shadow-sm font-bold' : 'text-navy-700/70 hover:text-navy-900' }}">
                                Reservasi &amp; Kasir
                                @if(request()->routeIs('resepsionis.pemesanan.*'))
                                    <span class="absolute bottom-1 left-4 right-4 h-0.5 bg-gold-500 rounded-full"></span>
                                @endif
                            </a>
                            <a href="{{ route('resepsionis.tamu.index') }}" 
                               class="px-4 py-2 rounded-full transition relative {{ request()->routeIs('resepsionis.tamu.*') ? 'bg-white text-navy-900 shadow-sm font-bold' : 'text-navy-700/70 hover:text-navy-900' }}">
                                Buku Tamu
                                @if(request()->routeIs('resepsionis.tamu.*'))
                                    <span class="absolute bottom-1 left-4 right-4 h-0.5 bg-gold-500 rounded-full"></span>
                                @endif
                            </a>
                        @endif

                        @if(auth()->user()->peran === 'Petugas Kebersihan' || auth()->user()->peran === 'Manajer')
                            <a href="{{ route('kebersihan.index') }}" 
                               class="px-4 py-2 rounded-full transition relative {{ request()->routeIs('kebersihan.*') ? 'bg-white text-navy-900 shadow-sm font-bold' : 'text-navy-700/70 hover:text-navy-900' }}">
                                Kebersihan Kamar
                                @if(request()->routeIs('kebersihan.*'))
                                    <span class="absolute bottom-1 left-4 right-4 h-0.5 bg-gold-500 rounded-full"></span>
                                @endif
                            </a>
                        @endif

                        @if(auth()->user()->peran === 'Manajer')
                            <div class="relative" x-data="{ open: false }">
                                <button @click="open = !open" 
                                    class="px-4 py-2 rounded-full transition flex items-center space-x-1 {{ request()->routeIs('manajer.*') ? 'bg-white text-navy-900 shadow-sm font-bold' : 'text-navy-700/70 hover:text-navy-900' }}">
                                    <span>Data Master</span>
                                    <svg class="w-3.5 h-3.5 ml-1 text-gold-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </button>
                                <div x-show="open" @click.away="open = false" x-cloak class="absolute left-0 mt-2 w-52 bg-white text-navy-900 rounded-2xl shadow-xl py-2 z-50 border border-warm-200">
                                    <a href="{{ route('manajer.kamar.index') }}" class="block px-4 py-2 text-xs font-semibold hover:bg-warm-100 hover:text-gold-600 transition">Data Master Kamar</a>
                                    <a href="{{ route('manajer.users.index') }}" class="block px-4 py-2 text-xs font-semibold hover:bg-warm-100 hover:text-gold-600 transition">Manajemen Staf</a>
                                </div>
                            </div>

                            <div class="relative" x-data="{ open: false }">
                                <button @click="open = !open" 
                                    class="px-4 py-2 rounded-full transition flex items-center space-x-1 {{ request()->routeIs('laporan.*') ? 'bg-white text-navy-900 shadow-sm font-bold' : 'text-navy-700/70 hover:text-navy-900' }}">
                                    <span>Laporan Hotel</span>
                                    <svg class="w-3.5 h-3.5 ml-1 text-gold-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </button>
                                <div x-show="open" @click.away="open = false" x-cloak class="absolute left-0 mt-2 w-56 bg-white text-navy-900 rounded-2xl shadow-xl py-2 z-50 border border-warm-200">
                                    <a href="{{ route('laporan.okupansi') }}" class="block px-4 py-2 text-xs font-semibold hover:bg-warm-100 hover:text-gold-600 transition">Laporan Okupansi Harian</a>
                                    <a href="{{ route('laporan.pendapatan') }}" class="block px-4 py-2 text-xs font-semibold hover:bg-warm-100 hover:text-gold-600 transition">Laporan Pendapatan Bulanan</a>
                                    <a href="{{ route('laporan.riwayat_tamu') }}" class="block px-4 py-2 text-xs font-semibold hover:bg-warm-100 hover:text-gold-600 transition">Laporan Riwayat Tamu</a>
                                </div>
                            </div>
                        @endif
                    </nav>
                @endauth

                <!-- Right Actions & User Profile -->
                <div class="flex items-center space-x-3">
                    @auth
                        <!-- User Profile Pill -->
                        @if(auth()->user()->peran === 'Tamu')
                            <a href="{{ route('pelanggan.profil') }}" title="Buka Profil Saya" class="flex items-center space-x-2.5 bg-white hover:bg-warm-100 py-1 px-3 rounded-full border border-warm-200 shadow-card transition group">
                                <div class="w-7 h-7 rounded-full bg-navy-800 text-gold-300 flex items-center justify-center text-xs font-bold uppercase group-hover:bg-gold-500 group-hover:text-navy-950 transition">
                                    {{ substr(auth()->user()->nama, 0, 1) }}
                                </div>
                                <div class="text-left hidden md:block">
                                    <div class="text-xs font-bold leading-none text-navy-900 group-hover:text-gold-700 transition">{{ auth()->user()->nama }}</div>
                                    <div class="text-[10px] text-gold-600 font-semibold mt-0.5 leading-none">{{ auth()->user()->peran }}</div>
                                </div>
                            </a>
                        @else
                            <div class="flex items-center space-x-2.5 bg-white py-1 px-3 rounded-full border border-warm-200 shadow-card">
                                <div class="w-7 h-7 rounded-full bg-navy-800 text-gold-300 flex items-center justify-center text-xs font-bold uppercase">
                                    {{ substr(auth()->user()->nama, 0, 1) }}
                                </div>
                                <div class="text-left hidden md:block">
                                    <div class="text-xs font-bold leading-none text-navy-900">{{ auth()->user()->nama }}</div>
                                    <div class="text-[10px] text-gold-600 font-semibold mt-0.5 leading-none">{{ auth()->user()->peran }}</div>
                                </div>
                            </div>
                        @endif

                        <!-- Logout Button -->
                        <form action="{{ route('logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" title="Keluar" class="w-9 h-9 rounded-full bg-rose-50 border border-rose-200 text-rose-700 hover:bg-rose-100 flex items-center justify-center text-xs transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                            </button>
                        </form>
                    @else
                        <div class="flex items-center space-x-2">
                            <a href="{{ route('login') }}" class="bg-navy-800 hover:bg-navy-700 text-white text-xs font-bold px-4 py-2 rounded-full shadow transition">
                                Masuk
                            </a>
                            <a href="{{ route('register') }}" class="bg-warm-100 hover:bg-warm-200 border border-warm-200 text-navy-900 text-xs font-bold px-4 py-2 rounded-full transition">
                                Daftar
                            </a>
                        </div>
                    @endauth
                </div>
            </div>
        </header>

        <!-- Flash Notifications -->
        <div class="px-4 sm:px-8 mt-4 w-full no-print">
            @if(session('success'))
                <div class="bg-emerald-50/90 border border-emerald-200 text-emerald-950 px-4 py-3 rounded-2xl shadow-card flex items-center justify-between text-xs font-semibold mb-3" x-data="{ show: true }" x-show="show">
                    <div class="flex items-center space-x-2">
                        <span class="w-5 h-5 rounded-full bg-emerald-600 text-white flex items-center justify-center">
                            <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                        </span>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button @click="show = false" class="text-emerald-600 hover:text-emerald-900 text-base">&times;</button>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-rose-50/90 border border-rose-200 text-rose-950 px-4 py-3 rounded-2xl shadow-card flex items-center justify-between text-xs font-semibold mb-3" x-data="{ show: true }" x-show="show">
                    <div class="flex items-center space-x-2">
                        <span class="w-5 h-5 rounded-full bg-rose-600 text-white flex items-center justify-center text-[10px] font-bold">!</span>
                        <span>{{ session('error') }}</span>
                    </div>
                    <button @click="show = false" class="text-rose-600 hover:text-rose-900 text-base">&times;</button>
                </div>
            @endif

            @if($errors->any())
                <div class="bg-amber-50/90 border border-amber-200 text-amber-950 px-4 py-3 rounded-2xl shadow-card text-xs mb-3">
                    <div class="font-bold mb-1">Periksa isian formulir:</div>
                    <ul class="list-disc list-inside space-y-0.5 text-amber-900">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        <!-- Main Body Canvas -->
        <main class="flex-1 px-4 sm:px-8 py-5">
            @yield('content')
        </main>

        <!-- Subtle Footer -->
        <footer class="px-8 py-4 border-t border-warm-200 text-center text-xs text-navy-800/60 no-print mt-auto bg-warm-100/60">
            &copy; {{ date('Y') }} Lumina Grand Hotel &amp; Resor &bull; Sistem Manajemen Operasional Hotel
        </footer>
    </div>

</body>
</html>

