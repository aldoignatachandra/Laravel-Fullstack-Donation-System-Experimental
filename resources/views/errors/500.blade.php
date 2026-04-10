<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 Server Error - DonasiKita</title>
    <meta name="description" content="Terjadi kesalahan pada server.">

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
                        <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                        <line x1="12" y1="9" x2="12" y2="13" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></line>
                        <line x1="12" y1="17" x2="12.01" y2="17" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></line>
                    </svg>
                </div>
            </div>

            {{-- Error Code --}}
            <h1 class="text-7xl font-extrabold text-slate-900 mb-2">500</h1>

            {{-- Error Title --}}
            <h2 class="text-2xl font-bold text-slate-900 mb-3">Kesalahan Server</h2>

            {{-- Error Message --}}
            <p class="text-slate-600 mb-8 leading-relaxed">
                Terjadi kesalahan pada sistem kami. Kami sedang berusaha memperbaikinya. Silakan coba lagi dalam beberapa saat.
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
                <p class="text-xs text-slate-500 mb-2">Masalah berlanjut?</p>
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