@php
    use App\Helper\CampaignHelper;
@endphp

<section id="campaigns" class="bg-white">
    <div class="mx-auto w-full max-w-7xl px-4 sm:px-6 lg:px-8 py-12 sm:py-16">
        <div class="mb-6 flex flex-col gap-3 sm:items-center sm:justify-between sm:flex-row">
            <div>
                <h2 class="text-xl sm:text-2xl font-semibold tracking-tight text-slate-900">Campaign Pilihan</h2>
                <p class="mt-1 text-sm text-slate-600">Dikurasi harian agar kamu mudah menemukan dampak terbaik.</p>
            </div>
            <a href="#"
               class="inline-flex items-center justify-center rounded-2xl px-4 py-2 text-sm font-semibold transition hover:bg-slate-50 border border-slate-200 text-slate-700">Lihat
                Semua</a>
        </div>
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
            @forelse($campaigns as $campaign)
                <a href="{{ route('campaign.show', $campaign->slug) }}" wire:navigate>

                    <div
                        class="group flex flex-col overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm transition hover:shadow-md">
                        <div class="relative aspect-[16/9] overflow-hidden">
                            <img
                                src="{{ CampaignHelper::getImageUrl($campaign->image) }}"
                                alt="{{ $campaign->title }}"
                                class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"
                                loading="lazy"
                            />
                            <div class="absolute left-3 top-3 flex gap-2">
                                <span
                                    class="inline-flex items-center rounded-full border px-2.5 py-1 text-xs font-medium text-slate-700 border-slate-200 bg-white">{{ $campaign->category->name ?? 'Umum' }}</span>
                                <span
                                    class="inline-flex items-center rounded-full border px-2.5 py-1 text-xs font-medium text-slate-700 border-slate-200 bg-white">{{ $campaign->user->city ?? 'Indonesia' }}</span>
                            </div>
                        </div>
                        <div class="flex flex-1 flex-col gap-3 p-4">
                            <h3 class="line-clamp-2 text-base font-semibold leading-snug text-slate-900">{{ $campaign->title }}</h3>
                            <p class="line-clamp-2 text-sm text-slate-600">{{ $campaign->description }}</p>

                            <div class="mt-1 space-y-2">
                                <div class="relative h-2 w-full overflow-hidden rounded-full bg-slate-100">
                                    @php
                                        $percentage = CampaignHelper::getProgressPercent($campaign->total_donations, $campaign->target_amount);
                                    @endphp
                                    <div
                                        class="absolute left-0 top-0 h-full rounded-full bg-emerald-500 transition-all"
                                        style="width: {{ $percentage }}%"
                                        aria-valuemin="0"
                                        aria-valuemax="100"
                                        aria-valuenow="{{ $percentage }}"
                                        role="progressbar"
                                    ></div>
                                </div>
                                <div class="flex items-center justify-between text-xs text-slate-600">
                                <span>
                                    Terkumpul <span
                                        class="font-semibold text-slate-900">
                                        {{ \App\Helper\NumberHelper::formatIDR($campaign->total_donations)  }}
                                    </span>
                                </span>
                                    <span>Target
                                    {{ \App\Helper\NumberHelper::formatIDR($campaign->target_amount)  }}</span>
                                </div>
                            </div>

                            <div class="mt-auto flex items-center justify-between">
                                <div class="flex items-center gap-2 text-xs text-slate-600">
                                    <span
                                        class="inline-flex items-center rounded-full bg-slate-50 px-2 py-1 font-medium border border-slate-200">{{ number_format($percentage, 0) }}%</span>

                                    <span>{{ CampaignHelper::getDaysLeft($campaign->end_date) }} hari lagi</span>
                                    <span>•</span>
                                    <span>{{ number_format($campaign->donation_count) }} donatur</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            @empty
                <div class="col-span-full text-center py-12">
                    <p class="text-slate-500">Belum ada campaign yang tersedia saat ini.</p>
                </div>
            @endforelse
        </div>

        @if($campaigns->hasPages())
            <div class="mt-8">
                <div class="flex items-center justify-between bg-white rounded-2xl shadow-lg border border-slate-200/60 px-4 py-3">
                    <div class="flex items-center gap-2">
                        {{-- Previous --}}
                        @if ($campaigns->onFirstPage())
                            <span class="px-3 py-2 rounded-xl text-slate-400 cursor-not-allowed">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                </svg>
                            </span>
                        @else
                            <button wire:click="previousPage" wire:loading.attr="disabled"
                                class="px-3 py-2 rounded-xl text-slate-700 hover:bg-slate-100 transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                </svg>
                            </button>
                        @endif

                        {{-- Page Numbers --}}
                        <div class="flex items-center gap-1">
                            @foreach ($campaigns->getUrlRange(1, $campaigns->lastPage()) as $page => $url)
                                @if ($page == $campaigns->currentPage())
                                    <span class="px-4 py-2 rounded-xl bg-emerald-600 text-white font-semibold">
                                        {{ $page }}
                                    </span>
                                @else
                                    <button wire:click="gotoPage({{ $page }})"
                                        class="px-4 py-2 rounded-xl text-slate-700 hover:bg-slate-100 transition font-medium">
                                        {{ $page }}
                                    </button>
                                @endif
                            @endforeach
                        </div>

                        {{-- Next --}}
                        @if ($campaigns->hasMorePages())
                            <button wire:click="nextPage" wire:loading.attr="disabled"
                                class="px-3 py-2 rounded-xl text-slate-700 hover:bg-slate-100 transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </button>
                        @else
                            <span class="px-3 py-2 rounded-xl text-slate-400 cursor-not-allowed">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </span>
                        @endif
                    </div>

                    {{-- Page Info --}}
                    <div class="text-sm text-slate-600">
                        Menampilkan <span class="font-semibold">{{ $campaigns->firstItem() }}-{{ $campaigns->lastItem() }}</span> dari <span class="font-semibold">{{ $campaigns->total() }}</span> campaign
                    </div>
                </div>
            </div>
        @endif
    </div>
</section>