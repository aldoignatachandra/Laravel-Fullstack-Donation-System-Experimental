<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'DonasiKita - Platform Donasi Online' }}</title>
    <meta name="description" content="Masuk atau daftar ke DonasiKita untuk mulai berdonasi atau menggalang dana.">

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

    {{-- Navbar --}}
    <header class="fixed top-0 left-0 right-0 z-50 bg-white/80 backdrop-blur-md border-b border-slate-200/80">
        <div class="mx-auto w-full max-w-7xl px-4 sm:px-6 lg:px-8 flex h-16 items-center justify-between">
            <a href="{{ route('home') }}" class="flex items-center gap-2">
                <span
                    class="inline-flex h-9 w-9 items-center justify-center rounded-2xl bg-emerald-600 text-white font-bold">D</span>
                <span class="text-sm font-semibold tracking-tight text-slate-900">DonasiKita</span>
            </a>
            <a href="{{ route('home') }}"
                class="inline-flex items-center justify-center rounded-2xl px-4 py-2 text-sm font-semibold transition hover:bg-slate-50 border border-slate-200 text-slate-700">
                ← Kembali
            </a>
        </div>
    </header>

    {{-- Main Content --}}
    <main class="pt-16 min-h-screen flex items-center justify-center p-4 sm:p-6 lg:p-8">
        <div class="w-full max-w-md">
            {{-- Card Container --}}
            <div class="bg-white rounded-3xl shadow-xl border border-slate-200/60 p-8 sm:p-10">
                {{ $slot }}
            </div>
        </div>
    </main>

    {{-- Footer --}}
    <footer class="py-6 text-center">
        <p class="text-sm text-slate-500">
            © {{ date('Y') }} DonasiKita. Platform donasi online transparan.
        </p>
    </footer>

    @stack('scripts')
    @livewireScripts
</body>

</html>
