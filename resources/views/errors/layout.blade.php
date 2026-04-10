<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error - DonasiKita</title>
    <meta name="description" content="Terjadi kesalahan.">

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
                <div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center mx-auto">
                    <svg class="w-10 h-10 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10" stroke-width="2"></circle>
                        <line x1="12" y1="8" x2="12" y2="12" stroke-width="2" stroke-linecap="round"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16" stroke-width="2" stroke-linecap="round"></line>
                    </svg>
                </div>
            </div>

            {{-- Error Code --}}
            <h1 class="text-6xl font-extrabold text-slate-900 mb-2">
                @yield('code', 'Error')
            </h1>

            {{-- Error Title --}}
            <h2 class="text-xl font-bold text-slate-900 mb-3">
                @yield('message', 'Terjadi Kesalahan')
            </h2>

            {{-- Error Description --}}
            <p class="text-slate-600 mb-8 leading-relaxed">
                Maaf, terjadi kesalahan saat memproses permintaan Anda. Silakan coba lagi atau hubungi support jika masalah berlanjut.
            </p>

            {{-- Action Buttons --}}
            <div class="flex flex-col gap-3">
                <button onclick="window.location.reload()" class="w-full py-3.5 px-6 rounded-2xl bg-emerald-600 text-white font-semibold text-sm hover:bg-emerald-700 transition-colors flex items-center justify-center gap-2 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Coba Lagi
                </button>

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