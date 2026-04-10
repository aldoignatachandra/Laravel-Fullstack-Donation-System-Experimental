@php
    use App\Helper\CampaignHelper;
@endphp

<section id="campaigns" class="bg-white dark:bg-slate-900">
    <div class="mx-auto w-full max-w-7xl px-4 sm:px-6 lg:px-8 py-12 sm:py-16">
        <div class="mb-6 flex flex-col gap-3 sm:items-center sm:justify-between sm:flex-row">
            <div>
                <h2 class="text-xl sm:text-2xl font-semibold tracking-tight text-gray-900 dark:text-white">Campaign Pilihan</h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-slate-300">Dikurasi harian agar kamu mudah menemukan dampak terbaik.</p>
            </div>
            <a href="#"
               class="inline-flex items-center justify-center rounded-2xl px-4 py-2 text-sm font-semibold transition hover:bg-gray-50 dark:hover:bg-slate-800 border border-gray-200 dark:border-slate-600 text-gray-700 dark:text-slate-300">Lihat
                Semua</a>
        </div>
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
            @forelse($campaigns as $campaign)
                <a href="{{ route('campaign.show', $campaign->slug) }}" wire:navigate>

                    <div
                        class="group flex flex-col overflow-hidden rounded-3xl border border-gray-200 dark:border-slate-600 bg-white dark:bg-slate-800 shadow-sm transition hover:shadow-md">
                        <div class="relative aspect-[16/9] overflow-hidden">
                            <img
                                src="{{ CampaignHelper::getImageUrl($campaign->image) }}"
                                alt="{{ $campaign->title }}"
                                class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"
                                loading="lazy"
                            />
                            <div class="absolute left-3 top-3 flex gap-2">
                                <span
                                    class="inline-flex items-center rounded-full border px-2.5 py-1 text-xs font-medium text-gray-700 dark:text-slate-300 border-gray-200 dark:border-slate-600 bg-white dark:bg-slate-700">{{ $campaign->category->name ?? 'Umum' }}</span>
                                <span
                                    class="inline-flex items-center rounded-full border px-2.5 py-1 text-xs font-medium text-gray-700 dark:text-slate-300 border-gray-200 dark:border-slate-600 bg-white dark:bg-slate-700">{{ $campaign->user->city ?? 'Indonesia' }}</span>
                            </div>
                        </div>
                        <div class="flex flex-1 flex-col gap-3 p-4">
                            <h3 class="line-clamp-2 text-base font-semibold leading-snug text-gray-900 dark:text-white">{{ $campaign->title }}</h3>
                            <p class="line-clamp-2 text-sm text-gray-600 dark:text-slate-300">{{ $campaign->description }}</p>

                            <div class="mt-1 space-y-2">
                                <div class="relative h-2 w-full overflow-hidden rounded-full bg-gray-100 dark:bg-slate-700">
                                    @php
                                        $percentage = CampaignHelper::getProgressPercent($campaign->total_donations, $campaign->target_amount);
                                    @endphp
                                    <div
                                        class="absolute left-0 top-0 h-full rounded-full bg-gray-900 dark:bg-emerald-500 transition-all"
                                        style="width: {{ $percentage }}%"
                                        aria-valuemin="0"
                                        aria-valuemax="100"
                                        aria-valuenow="{{ $percentage }}"
                                        role="progressbar"
                                    ></div>
                                </div>
                                <div class="flex items-center justify-between text-xs text-gray-600 dark:text-slate-300">
                                <span>
                                    Terkumpul <span
                                        class="font-semibold text-gray-900 dark:text-white">
                                        {{ \App\Helper\NumberHelper::formatIDR($campaign->total_donations)  }}
                                    </span>
                                </span>
                                    <span>Target
                                    {{ \App\Helper\NumberHelper::formatIDR($campaign->target_amount)  }}</span>
                                </div>
                            </div>

                            <div class="mt-auto flex items-center justify-between">
                                <div class="flex items-center gap-2 text-xs text-gray-600 dark:text-slate-300">
                                    <span
                                        class="inline-flex items-center rounded-full bg-gray-50 dark:bg-slate-700 px-2 py-1 font-medium border border-gray-200 dark:border-slate-600">{{ number_format($percentage, 0) }}%</span>

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
                    <p class="text-gray-500 dark:text-slate-400">Belum ada campaign yang tersedia saat ini.</p>
                </div>
            @endforelse
        </div>

        @if($campaigns->hasPages())
            <div class="mt-8">
                {{ $campaigns->links() }}
            </div>
        @endif
    </div>
</section>
