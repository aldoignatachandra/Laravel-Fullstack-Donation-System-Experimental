<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'DonasiKita - Platform Donasi Online Transparan' }}</title>
    <meta name="description" content="{{ $description ?? 'Temukan campaign pilihan, donasi dalam hitungan detik, dan pantau perkembangan secara real-time.' }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @fluxAppearance

    @stack('styles')
    @livewireStyles


</head>
<body class="h-full bg-white dark:bg-slate-900 text-gray-900 dark:text-white">
<x-navbar />

{{ $slot }}


@stack('scripts')
@livewireScripts 

</body>
</html>
