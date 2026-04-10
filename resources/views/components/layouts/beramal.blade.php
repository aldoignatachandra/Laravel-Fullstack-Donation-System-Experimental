<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'DonasiKita - Platform Donasi Online Transparan' }}</title>
    <meta name="description" content="{{ $description ?? 'Temukan campaign pilihan, donasi dalam hitungan detik, dan pantau perkembangan secara real-time.' }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Force light mode - prevent dark mode from being applied --}}
    <script>
        // Prevent dark mode by ensuring 'dark' class is not added
        // and set Flux theme to light explicitly
        document.documentElement.classList.remove('dark');
        document.documentElement.setAttribute('data-flux-theme', 'light');
    </script>

    @stack('styles')
    @livewireStyles


</head>
<body class="min-h-screen bg-white text-gray-900">
<x-navbar />

{{ $slot }}


@stack('scripts')
@livewireScripts 

</body>
</html>
