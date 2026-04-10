<x-layouts.dashboard :title="__('Dashboard')">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        {{-- Welcome Section --}}
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-slate-900">Selamat Datang, {{ auth()->user()->name }}! 👋</h1>
            <p class="text-slate-600 mt-2 text-lg">Berikut adalah ringkasan aktivitas donasi Anda.</p>
        </div>

        {{-- Stats Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            {{-- Total Donasi --}}
            <div class="bg-white rounded-3xl shadow-lg border border-slate-200/60 p-6 hover:shadow-xl transition-shadow">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-emerald-100 flex items-center justify-center">
                        <svg class="w-7 h-7 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-slate-600">Total Donasi</p>
                        <p class="text-2xl font-bold text-slate-900">Rp 0</p>
                    </div>
                </div>
            </div>

            {{-- Jumlah Donasi --}}
            <div class="bg-white rounded-3xl shadow-lg border border-slate-200/60 p-6 hover:shadow-xl transition-shadow">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-blue-100 flex items-center justify-center">
                        <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-slate-600">Jumlah Donasi</p>
                        <p class="text-2xl font-bold text-slate-900">0</p>
                    </div>
                </div>
            </div>

            {{-- Campaign Diikuti --}}
            <div class="bg-white rounded-3xl shadow-lg border border-slate-200/60 p-6 hover:shadow-xl transition-shadow">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-amber-100 flex items-center justify-center">
                        <svg class="w-7 h-7 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-slate-600">Campaign Diikuti</p>
                        <p class="text-2xl font-bold text-slate-900">0</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Quick Actions --}}
        <div class="bg-white rounded-3xl shadow-lg border border-slate-200/60 p-6 mb-8">
            <h2 class="text-xl font-bold text-slate-900 mb-4">Aksi Cepat</h2>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('home') }}#campaigns" class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-600 text-white rounded-2xl font-semibold text-sm hover:bg-emerald-700 transition shadow-md hover:shadow-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Mulai Donasi
                </a>
                <a href="{{ route('donations') }}" class="inline-flex items-center gap-2 px-5 py-2.5 border border-slate-200 text-slate-700 rounded-2xl font-semibold text-sm hover:bg-slate-50 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    Lihat Riwayat
                </a>
                <a href="{{ route('settings.profile') }}" class="inline-flex items-center gap-2 px-5 py-2.5 border border-slate-200 text-slate-700 rounded-2xl font-semibold text-sm hover:bg-slate-50 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    Pengaturan
                </a>
            </div>
        </div>

        {{-- Recent Activity --}}
        <div class="bg-white rounded-3xl shadow-lg border border-slate-200/60 p-6">
            <h2 class="text-xl font-bold text-slate-900 mb-4">Aktivitas Terbaru</h2>
            <div class="text-center py-16">
                <div class="w-20 h-20 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <p class="text-slate-600 text-lg">Belum ada aktivitas donasi</p>
                <p class="text-slate-500 mt-1">Mulai berdonasi untuk melihat riwayat Anda di sini.</p>
                <a href="{{ route('home') }}#campaigns" class="inline-flex items-center gap-2 mt-4 px-5 py-2.5 bg-emerald-600 text-white rounded-2xl font-semibold text-sm hover:bg-emerald-700 transition">
                    Jelajahi Campaign
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
            </div>
        </div>
    </div>
</x-layouts.dashboard>