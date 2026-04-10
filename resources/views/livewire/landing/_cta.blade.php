<section class="relative overflow-hidden">
    <div class="mx-auto w-full max-w-7xl px-4 sm:px-6 lg:px-8 py-12 sm:py-16">
        <div class="grid gap-6 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm sm:grid-cols-2">
            <div>
                <h3 class="text-2xl font-bold tracking-tight text-slate-900">Punya Inisiatif Baik?</h3>
                <p class="mt-2 text-sm text-slate-600">
                    Buat campaignmu sendiri dan mulai galang dana dalam hitungan menit.
                </p>
            </div>
            <div class="flex items-center justify-end gap-3">
                <a href="#cara-kerja" class="inline-flex items-center justify-center rounded-2xl px-4 py-2 text-sm font-semibold transition hover:bg-slate-50 border border-slate-200 text-slate-700">Baca Panduan</a>
                @auth
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center rounded-2xl px-4 py-2 text-sm font-semibold shadow-sm transition hover:shadow md:text-sm bg-emerald-600 text-white hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500/30">Mulai Galang Dana</a>
                @else
                    <a href="{{ route('register') }}" class="inline-flex items-center justify-center rounded-2xl px-4 py-2 text-sm font-semibold shadow-sm transition hover:shadow md:text-sm bg-emerald-600 text-white hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500/30">Mulai Galang Dana</a>
                @endauth
            </div>
        </div>
    </div>
</section>
