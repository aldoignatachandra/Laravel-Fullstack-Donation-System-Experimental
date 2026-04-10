<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Dashboard - DonasiKita' }}</title>
    <meta name="description" content="Dashboard pengguna DonasiKita">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Force light mode --}}
    <script>
        document.documentElement.classList.remove('dark');
        document.documentElement.setAttribute('data-flux-theme', 'light');
    </script>

    @stack('styles')
    @livewireStyles
</head>
<body class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-emerald-50">

    {{-- Navbar - Matches login/register exactly --}}
    <header class="sticky top-0 z-50 bg-white/80 backdrop-blur-md border-b border-slate-200/80">
        <div class="mx-auto w-full max-w-7xl px-4 sm:px-6 lg:px-8 flex h-16 items-center justify-between">
            {{-- Logo - Links to homepage --}}
            <a href="{{ route('home') }}" class="flex items-center gap-2">
                <span class="inline-flex h-9 w-9 items-center justify-center rounded-2xl bg-emerald-600 text-white font-bold">D</span>
                <span class="text-sm font-semibold tracking-tight text-slate-900">DonasiKita</span>
            </a>

            {{-- Desktop Navigation --}}
            <nav class="hidden md:flex items-center gap-2">
                <a href="{{ route('dashboard') }}" 
                   class="inline-flex items-center justify-center rounded-2xl px-4 py-2 text-sm font-semibold transition {{ request()->routeIs('dashboard') ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'text-slate-700 hover:bg-slate-50 border border-transparent' }}">
                    Dashboard
                </a>
                <a href="{{ route('donations') }}" 
                   class="inline-flex items-center justify-center rounded-2xl px-4 py-2 text-sm font-semibold transition {{ request()->routeIs('donations') ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'text-slate-700 hover:bg-slate-50 border border-transparent' }}">
                    Donasi Saya
                </a>
            </nav>

            {{-- User Menu --}}
            <div class="flex items-center gap-3">
                {{-- Mobile Menu Button --}}
                <button onclick="document.getElementById('mobile-menu').classList.toggle('hidden')" 
                        class="md:hidden inline-flex items-center justify-center rounded-2xl p-2 text-slate-600 hover:bg-slate-100 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>

                {{-- Desktop User Dropdown --}}
                <div class="hidden md:block relative">
                    <button onclick="document.getElementById('user-dropdown').classList.toggle('hidden')" 
                            class="inline-flex items-center gap-2 rounded-2xl px-3 py-2 text-sm font-semibold border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 transition">
                        <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-emerald-600 text-white font-bold text-sm">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </span>
                        <span class="max-w-[100px] truncate">{{ auth()->user()->name }}</span>
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    
                    {{-- Dropdown Menu --}}
                    <div id="user-dropdown" class="hidden absolute right-0 mt-2 w-56 rounded-2xl border border-slate-200 bg-white shadow-xl">
                        <div class="p-3">
                            <div class="px-3 py-2">
                                <p class="text-sm font-semibold text-slate-900 truncate">{{ auth()->user()->name }}</p>
                                <p class="text-xs text-slate-500 truncate">{{ auth()->user()->email }}</p>
                            </div>
                            <hr class="my-2 border-slate-100">
                            <a href="{{ route('settings.profile') }}" class="flex items-center gap-2 px-3 py-2 text-sm text-slate-700 hover:bg-slate-50 rounded-xl transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                Pengaturan
                            </a>
                            <form method="POST" action="{{ route('logout') }}" class="w-full">
                                @csrf
                                <button type="submit" class="w-full flex items-center gap-2 px-3 py-2 text-sm text-red-600 hover:bg-red-50 rounded-xl transition mt-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                    </svg>
                                    Keluar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Mobile Menu --}}
        <div id="mobile-menu" class="hidden md:hidden border-t border-slate-200 bg-white">
            <div class="px-4 py-4 space-y-1">
                <div class="px-3 py-2 mb-2">
                    <p class="text-sm font-semibold text-slate-900">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-slate-500">{{ auth()->user()->email }}</p>
                </div>
                <a href="{{ route('dashboard') }}" class="block px-3 py-2.5 text-sm font-semibold rounded-xl {{ request()->routeIs('dashboard') ? 'bg-emerald-50 text-emerald-700' : 'text-slate-700 hover:bg-slate-50' }}">
                    Dashboard
                </a>
                <a href="{{ route('donations') }}" class="block px-3 py-2.5 text-sm font-semibold rounded-xl {{ request()->routeIs('donations') ? 'bg-emerald-50 text-emerald-700' : 'text-slate-700 hover:bg-slate-50' }}">
                    Donasi Saya
                </a>
                <a href="{{ route('settings.profile') }}" class="block px-3 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 rounded-xl">
                    Pengaturan
                </a>
                <hr class="my-2 border-slate-100">
                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <button type="submit" class="w-full text-left px-3 py-2.5 text-sm font-semibold text-red-600 hover:bg-red-50 rounded-xl">
                        Keluar
                    </button>
                </form>
            </div>
        </div>
    </header>

    {{-- Main Content --}}
    <main class="flex-1">
        {{ $slot }}
    </main>

    @stack('scripts')
    @livewireScripts
</body>
</html>