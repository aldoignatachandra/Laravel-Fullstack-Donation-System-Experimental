<section class="relative overflow-hidden bg-gradient-to-b from-white to-gray-50 dark:from-slate-900 dark:to-slate-800">
    <div class="mx-auto w-full max-w-7xl px-4 sm:px-6 lg:px-8 py-12 sm:py-16 lg:py-20">
        <div class="grid gap-10 lg:grid-cols-2 lg:items-center">
            <div>
                <div class="inline-flex items-center gap-2 rounded-full border border-emerald-200 dark:border-emerald-700 bg-emerald-50 dark:bg-emerald-900/30 px-3 py-1 text-xs text-emerald-700 dark:text-emerald-300">
                    <span class="inline-block h-2 w-2 animate-pulse rounded-full bg-emerald-500"></span>
                    Donasi transparan & cepat
                </div>
                <h1 class="mt-4 text-3xl font-extrabold tracking-tight text-slate-900 dark:text-white sm:text-4xl lg:text-5xl">
                    Bantu Mereka Hari Ini, <span class="underline decoration-4 decoration-emerald-500">Dampaknya Nyata</span>
                </h1>
                <p class="mt-3 max-w-xl text-base text-slate-600 dark:text-slate-300 sm:text-lg">
                    Temukan campaign pilihan, donasi dalam hitungan detik, dan pantau perkembangan secara real-time.
                </p>
                <div class="mt-6 flex flex-col gap-3 sm:flex-row">
                    <div class="relative flex-1">
                        <input
                            type="text"
                            class="w-full rounded-2xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-800 px-4 py-3 pr-10 text-sm text-slate-900 dark:text-white outline-none ring-emerald-500/10 focus:ring-2 focus:ring-emerald-500 placeholder-slate-500 dark:placeholder-slate-400"
                            placeholder="Cari campaign: pendidikan, kesehatan, bencana..."
                            wire:model.live="search"
                            aria-label="Cari campaign"
                        />
                        <span class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 dark:text-slate-500">⌕</span>
                    </div>
                    <button class="inline-flex items-center justify-center rounded-2xl px-4 py-2 text-sm font-semibold shadow-sm transition hover:shadow md:text-sm bg-emerald-600 dark:bg-emerald-500 text-white hover:bg-emerald-700 dark:hover:bg-emerald-600 focus:outline-none focus:ring-2 focus:ring-emerald-500/30">Donasi Cepat</button>
                </div>

                <div class="mt-8 flex items-center gap-6 text-sm text-slate-600 dark:text-slate-300">
                    <div class="flex items-center gap-2">
                        <span class="inline-block h-2 w-2 rounded-full bg-emerald-600"></span>
                        120k+ donatur aktif
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="inline-block h-2 w-2 rounded-full bg-emerald-600"></span>
                        Rp45M+ dana tersalurkan
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="inline-block h-2 w-2 rounded-full bg-emerald-600"></span>
                        Audit & laporan publik
                    </div>
                </div>
            </div>
            <div class="relative">
                <div class="relative rounded-3xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-800 p-2 shadow-xl ring-1 ring-indigo-50 dark:ring-slate-700">
                    <img
                        src="{{ asset('images/landing.jpg') }}"
                        alt="Ilustrasi donasi dan relawan"
                        class="h-full w-full rounded-2xl object-cover"
                    />
                </div>
            </div>
        </div>
    </div>
</section>
