<header class="sticky top-0 z-50 bg-white/80 backdrop-blur-md border-b border-slate-200/80 transition-all duration-300">
    <div class="mx-auto w-full max-w-7xl px-3 sm:px-6 lg:px-8 flex h-16 items-center justify-between">
        <a href="{{ route('home') }}" class="flex items-center gap-2">
            <span class="inline-flex h-9 w-9 items-center justify-center rounded-2xl bg-emerald-600 text-white font-bold">D</span>
            <span class="text-sm font-semibold tracking-tight text-slate-900">DonasiKita</span>
        </a>
        <nav class="hidden md:flex items-center gap-2">
            @php
                $isHome = request()->routeIs('home');
            @endphp
            <a href="{{ $isHome ? '#campaigns' : route('home') . '#campaigns' }}" class="inline-flex items-center justify-center rounded-2xl px-4 py-2 text-sm font-semibold transition hover:bg-slate-50 border border-slate-200 text-slate-700">Campaigns</a>
            <a href="{{ $isHome ? '#cara-kerja' : route('home') . '#cara-kerja' }}" class="inline-flex items-center justify-center rounded-2xl px-4 py-2 text-sm font-semibold transition hover:bg-slate-50 border border-slate-200 text-slate-700">Cara Kerja</a>
            <a href="{{ $isHome ? '#tentang' : route('home') . '#tentang' }}" class="inline-flex items-center justify-center rounded-2xl px-4 py-2 text-sm font-semibold transition hover:bg-slate-50 border border-slate-200 text-slate-700">Tentang</a>

            @auth
                <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center rounded-2xl px-4 py-2 text-sm font-semibold shadow-sm transition hover:shadow md:text-sm bg-emerald-600 text-white hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500/30">Mulai Galang Dana</a>
            @endauth

            @guest
                <a href="{{ route('login') }}" class="ml-2 inline-flex items-center justify-center rounded-2xl px-4 py-2 text-sm font-semibold transition hover:bg-slate-50 border border-slate-200 text-slate-700">Masuk</a>
                <a href="{{ route('register') }}" class="inline-flex items-center justify-center rounded-2xl px-4 py-2 text-sm font-semibold shadow-sm transition hover:shadow md:text-sm bg-emerald-600 text-white hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500/30 ml-2">Mulai Galang Dana</a>
            @endguest

            @auth
                <div class="ml-2 relative group">
                    <button type="button" class="inline-flex items-center gap-2 rounded-2xl px-3 py-2 text-sm font-semibold border border-slate-200 bg-white text-slate-700 hover:bg-slate-50">
                        <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-emerald-600 text-white font-bold">
                            {{ strtoupper(mb_substr(auth()->user()->name ?? 'U', 0, 1)) }}
                        </span>
                        <span class="max-w-[10rem] truncate">{{ auth()->user()->name }}</span>
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div class="absolute right-0 mt-2 w-48 rounded-2xl border border-slate-200 bg-white shadow-lg hidden group-hover:block group-focus-within:block">
                        <a href="{{ route('settings.profile') }}" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">Profil</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full text-left block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">Keluar</button>
                        </form>
                    </div>
                </div>
            @endauth
        </nav>
        <div class="md:hidden flex items-center gap-2">
            @guest
                <a href="{{ route('login') }}" class="hidden sm:inline-flex items-center justify-center rounded-2xl px-3 py-2 text-xs sm:text-sm font-semibold border border-slate-200 text-slate-700">Masuk</a>
                <a href="{{ route('register') }}" class="inline-flex items-center justify-center rounded-2xl px-3 py-2 text-xs sm:text-sm font-semibold bg-emerald-600 text-white">Daftar</a>
                <a href="{{ route('register') }}" class="hidden sm:inline-flex items-center justify-center rounded-2xl px-3 py-2 text-xs sm:text-sm font-semibold bg-emerald-600 text-white">Galang Dana</a>
            @endguest
            @auth
                <a href="{{ route('settings.profile') }}" class="hidden sm:inline-flex items-center justify-center rounded-2xl px-3 py-2 text-xs sm:text-sm font-semibold border border-slate-200 text-slate-700">{{ auth()->user()->name }}</a>
                <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center rounded-2xl px-3 py-2 text-xs sm:text-sm font-semibold bg-emerald-600 text-white">Galang Dana</a>
            @endauth
            <button class="inline-flex items-center justify-center rounded-2xl px-3 py-2 text-xs sm:text-sm font-semibold shadow-sm transition hover:shadow bg-slate-900 text-white hover:bg-black focus:outline-none focus:ring-2 focus:ring-slate-900/30" aria-label="Menu">☰</button>
        </div>
    </div>

</header>
