<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 Not Found - DonasiKita</title>
    <meta name="description" content="Halaman yang Anda cari tidak ditemukan.">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Force light mode --}}
    <script>
        document.documentElement.classList.remove('dark');
        document.documentElement.setAttribute('data-flux-theme', 'light');
    </script>
</head>

<body class="min-h-screen bg-slate-50">
    <div class="min-h-screen w-full flex flex-col items-center justify-center p-4 sm:p-6"
        style="background-image: radial-gradient(#e2e8f0 1px, transparent 1px); background-size: 24px 24px;">

        {{-- Logo --}}
        <div class="mb-8">
            <a href="{{ route('home') }}" class="flex items-center gap-2">
                <span
                    class="inline-flex h-10 w-10 items-center justify-center rounded-2xl bg-emerald-600 text-white font-bold text-lg">D</span>
                <span class="text-lg font-semibold tracking-tight text-slate-900">DonasiKita</span>
            </a>
        </div>

        {{-- Error Card --}}
        <div class="w-full max-w-md bg-white rounded-3xl border border-slate-200 p-8 sm:p-10 shadow-xl text-center">

            {{-- Error Icon --}}
            <div class="mb-6">
                <div class="w-20 h-20 bg-amber-100 rounded-full flex items-center justify-center mx-auto">
                    <svg class="w-10 h-10 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10" stroke-width="2"></circle>
                        <path d="M8 15s1.5-2 4-2 4 2 4 2" stroke-width="2" stroke-linecap="round"></path>
                        <line x1="9" y1="9" x2="9.01" y2="9" stroke-width="2"
                            stroke-linecap="round"></line>
                        <line x1="15" y1="9" x2="15.01" y2="9" stroke-width="2"
                            stroke-linecap="round"></line>
                    </svg>
                </div>
            </div>

            {{-- Error Code --}}
            <h1 class="text-7xl font-extrabold text-slate-900 mb-2">404</h1>

            {{-- Error Title --}}
            <h2 class="text-2xl font-bold text-slate-900 mb-3">Halaman Tidak Ditemukan</h2>

            {{-- Error Message --}}
            <p class="text-slate-600 mb-8 leading-relaxed">
                Oops! Halaman yang Anda cari tidak ada atau telah dipindahkan. Mari kembali ke jalur yang benar.
            </p>

            {{-- Action Buttons --}}
            <div class="flex flex-col gap-3">
                <a href="{{ route('home') }}"
                    class="w-full py-3.5 px-6 rounded-2xl bg-emerald-600 text-white font-semibold text-sm hover:bg-emerald-700 transition-colors flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                        </path>
                    </svg>
                    Kembali ke Beranda
                </a>

                <a href="{{ route('home') }}#campaigns"
                    class="w-full py-3.5 px-6 rounded-2xl border border-slate-300 text-slate-700 font-semibold text-sm hover:bg-slate-50 transition-colors">
                    Lihat Campaign
                </a>
            </div>

            {{-- Search Suggestion --}}
            <div class="mt-8 pt-6 border-t border-slate-100">
                <p class="text-xs text-slate-500 mb-2">Mencari sesuatu?</p>
                <a href="{{ route('home') }}" class="text-sm font-semibold text-emerald-600 hover:text-emerald-700">
                    Cari Campaign Donasi
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
