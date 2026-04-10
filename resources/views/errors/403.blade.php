<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 Forbidden - DonasiKita</title>
    <meta name="description" content="Anda tidak memiliki akses ke halaman ini.">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Force light mode --}}
    <script>
        document.documentElement.classList.remove('dark');
        document.documentElement.setAttribute('data-flux-theme', 'light');
    </script>
</head>
<body class="min-h-screen bg-slate-50">
    <div class="min-h-screen w-full flex flex-col items-center justify-center p-4 sm:p-6" style="background-image: radial-gradient(#e2e8f0 1px, transparent 1px); background-size: 24px 24px;">

        {{-- Logo --}}
        <div class="mb-8">
            <a href="{{ route('home') }}" class="flex items-center gap-2">
                <span class="inline-flex h-10 w-10 items-center justify-center rounded-2xl bg-emerald-600 text-white font-bold text-lg">D</span>
                <span class="text-lg font-semibold tracking-tight text-slate-900">DonasiKita</span>
            </a>
        </div>

        {{-- Error Card --}}
        <div class="w-full max-w-md bg-white rounded-3xl border border-slate-200 p-8 sm:p-10 shadow-xl text-center">

            {{-- Error Icon --}}
            <div class="mb-6">
                <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto">
                    <svg class="w-10 h-10 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10" stroke-width="2"></circle>
                        <line x1="12" y1="8" x2="12" y2="12" stroke-width="2" stroke-linecap="round"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16" stroke-width="2" stroke-linecap="round"></line>
                    </svg>
                </div>
            </div>

            {{-- Error Code --}}
            <h1 class="text-7xl font-extrabold text-slate-900 mb-2">403</h1>

            {{-- Error Title --}}
            <h2 class="text-2xl font-bold text-slate-900 mb-3">Akses Ditolak</h2>

            {{-- Error Message --}}
            <p class="text-slate-600 mb-8 leading-relaxed">
                Maaf, Anda tidak memiliki izin untuk mengakses halaman ini. Silakan hubungi administrator jika Anda merasa ini adalah kesalahan.
            </p>

            {{-- Action Buttons --}}
            <div class="flex flex-col gap-3">
                @auth
                    @if(auth()->user()->hasRole('admin'))
                        <a href="{{ url('/admin') }}" class="w-full py-3.5 px-6 rounded-2xl bg-emerald-600 text-white font-semibold text-sm hover:bg-emerald-700 transition-colors flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            Ke Admin Panel
                        </a>
                    @else
                        <a href="{{ route('dashboard') }}" class="w-full py-3.5 px-6 rounded-2xl bg-emerald-600 text-white font-semibold text-sm hover:bg-emerald-700 transition-colors flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            Ke Dashboard Saya
                        </a>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="w-full py-3.5 px-6 rounded-2xl bg-emerald-600 text-white font-semibold text-sm hover:bg-emerald-700 transition-colors flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                        </svg>
                        Masuk
                    </a>
                @endauth

                <a href="{{ route('home') }}" class="w-full py-3.5 px-6 rounded-2xl border border-slate-300 text-slate-700 font-semibold text-sm hover:bg-slate-50 transition-colors">
                    Kembali ke Beranda
                </a>
            </div>

            {{-- Additional Help --}}
            <div class="mt-8 pt-6 border-t border-slate-100">
                <p class="text-xs text-slate-500 mb-2">Butuh bantuan?</p>
                <a href="mailto:support@donasikita.id" class="text-sm font-semibold text-emerald-600 hover:text-emerald-700">
                    Hubungi Tim Support
                </a>
            </div>
        </div>

        {{-- Footer --}}
        <div class="mt-8 text-center">
            <p class="text-xs text-slate-500">
                © {{ date('Y') }} DonasiKita. Platform donasi online transparan.
            </p>
        </div>
    </div>
</body>
</html>