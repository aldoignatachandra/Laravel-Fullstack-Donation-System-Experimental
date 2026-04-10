<!-- Main Content -->
<div class="mx-auto w-full max-w-7xl px-4 sm:px-6 lg:px-8 py-8 bg-white dark:bg-slate-900 min-h-screen">
    <div class="grid gap-8 lg:grid-cols-3">
        <!-- Left Column - Campaign Details -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Campaign Header -->
            <div class="bg-white dark:bg-slate-800 rounded-3xl border border-gray-200 dark:border-slate-600 p-0 shadow-sm overflow-hidden">
                <!-- Cover Image -->
                <div class="w-full h-100 bg-gray-100 dark:bg-slate-700">
                    <img
                        src="{{ \App\Helper\CampaignHelper::getImageUrl($campaign->image) }}"
                        alt="{{ $campaign->title }}"
                        class="w-full h-full object-cover"
                    />
                </div>
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-2">
                            @if($campaign->category)
                                <span
                                    class="inline-flex items-center rounded-full border px-2.5 py-1 text-xs font-medium text-gray-700 dark:text-slate-300 border-gray-200 dark:border-slate-600 bg-white dark:bg-slate-700">
                                        {{ $campaign->category->name }}
                                    </span>
                            @endif
                            @if($campaign->is_featured)
                                <span
                                    class="inline-flex items-center rounded-full border px-2.5 py-1 text-xs font-medium text-amber-700 dark:text-amber-300 border-amber-200 dark:border-amber-700 bg-amber-50 dark:bg-amber-900/30">
                                        ⭐ Featured
                                    </span>
                            @endif
                        </div>
                        <div class="flex items-center gap-2">
                            @php
                                $statusBadge = \App\Helper\CampaignHelper::getStatusBadge($campaign->status);
                            @endphp
                            <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium {{ $statusBadge['class'] }}">
                                <i class="{{ $statusBadge['icon'] }} mr-1"></i>
                                {{ $statusBadge['text'] }}
                            </span>
                        </div>
                    </div>

                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">{{ $campaign->title }}</h1>
                    <div class="prose prose-slate dark:prose-invert max-w-none text-gray-600 dark:text-slate-300">
                        {!! $campaign->description !!}
                    </div>

                    <!-- Campaign Creator Info -->
                    @if($campaign->user)
                        <div class="mt-4 pt-4 border-t border-gray-100 dark:border-slate-600">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-gray-200 dark:bg-slate-600 rounded-full flex items-center justify-center">
                                    <span
                                        class="text-gray-600 dark:text-slate-300 font-semibold">{{ strtoupper(mb_substr($campaign->user->name ?? 'Admin', 0, 1)) }}</span>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">Dibuat
                                        oleh {{ $campaign->user->name ?? 'Admin' }}</p>
                                    <p class="text-xs text-gray-500 dark:text-slate-400">
                                        Mulai {{ $campaign->start_date ? $campaign->start_date->format('d M Y') : 'Tidak ditentukan' }}</p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>



            <!-- Update Terbaru -->
            <div class="bg-white dark:bg-slate-800 rounded-3xl border border-gray-200 dark:border-slate-600 p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Update Terbaru</h2>
                @if(($campaign->articles ?? collect())->isEmpty())
                    <p class="text-sm text-gray-600 dark:text-slate-300">Belum ada update untuk campaign ini.</p>
                @else
                    <div class="space-y-4">
                        @foreach($campaign->articles as $article)
                            <div class="p-4 border border-gray-100 dark:border-slate-600 rounded-2xl hover:border-gray-200 dark:hover:border-slate-500 transition bg-white dark:bg-slate-700">
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <h3 class="font-medium text-gray-900 dark:text-white">{{ $article->title }}</h3>
                                        <p class="text-xs text-gray-500 dark:text-slate-400 mt-1">
                                            {{ $article->created_at?->format('d M Y') }}
                                            @if($article->author)
                                                • oleh {{ $article->author->name }}
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                <p class="mt-2 text-sm text-gray-600 dark:text-slate-300">{{ \Illuminate\Support\Str::limit(strip_tags($article->content), 180) }}</p>
                                <button
                                    type="button"
                                    wire:click="showArticle({{ $article->id }})"
                                    class="mt-3 text-emerald-600 dark:text-emerald-400 hover:text-emerald-700 dark:hover:text-emerald-300 text-sm font-medium"
                                >
                                    Baca selengkapnya
                                </button>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Attachments -->
            @if($campaign->attachments && $campaign->attachments->count() > 0)
                <div class="bg-white dark:bg-slate-800 rounded-3xl border border-gray-200 dark:border-slate-600 p-6 shadow-sm">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Dokumen & Lampiran</h2>
                    <div class="space-y-3">
                        @foreach($campaign->attachments as $attachment)
                            <div class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-slate-700 rounded-2xl">
                                <div class="w-10 h-10 bg-gray-200 dark:bg-slate-600 rounded-full flex items-center justify-center">
                                    <span class="text-gray-600 dark:text-slate-300 text-sm">📎</span>
                                </div>
                                <div class="flex-1">
                                    <p class="font-medium text-gray-900 dark:text-white">{{ $attachment->original_name ?? 'File' }}</p>
                                    <p class="text-sm text-gray-600 dark:text-slate-300">{{ $attachment->file_size ?? 'Unknown size' }}</p>
                                </div>
                                <a href="{{ asset('storage/' . $attachment->file_path) }}"
                                   target="_blank"
                                   class="text-emerald-600 dark:text-emerald-400 hover:text-emerald-700 dark:hover:text-emerald-300 text-sm font-medium">
                                    Download
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>

        <!-- Right Column - Donation Sidebar -->
        <div class="lg:col-span-1">
            <div class="sticky-sidebar space-y-6">
                <!-- Donation Card -->
                <div class="bg-white dark:bg-slate-800 rounded-3xl border border-gray-200 dark:border-slate-600 p-6 shadow-sm">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Lakukan Donasi</h3>

                    <!-- Progress Bar -->
                    <div class="mb-6">
                        @php
                            $progressData = \App\Helper\CampaignHelper::getProgressData($campaign);
                        @endphp
                        <div class="flex items-center justify-between text-sm mb-2">
                            <span class="text-gray-600 dark:text-slate-300">Progress</span>
                            <span class="font-semibold text-gray-900 dark:text-white">{{ $progressData['formatted_percent'] }}%</span>
                        </div>
                        <div class="relative h-3 w-full overflow-hidden rounded-full bg-gray-100 dark:bg-slate-700">
                            <div
                                class="absolute left-0 top-0 h-full rounded-full {{ $progressData['progress_class'] }} transition-all"
                                style="width: {{ $progressData['percent'] }}%"
                            ></div>
                        </div>
                        <div class="flex items-center justify-between text-xs text-gray-600 dark:text-slate-300 mt-2">
                            <span>Terkumpul <span
                                    class="font-semibold text-gray-900 dark:text-white">{{ \App\Helper\NumberHelper::formatIDR($campaign->total_donations) }}</span></span>
                            <span>Target <span
                                    class="font-semibold text-gray-900 dark:text-white">
                                    {{ \App\Helper\NumberHelper::formatIDR($campaign->target_amount) }}
                                </span></span>
                        </div>
                    </div>


                    <!-- Donation Button -->
                    <a href="{{ route('campaign.donate', $campaign->slug) }}"
                        wire:navigate
                       class="w-full bg-emerald-600 dark:bg-emerald-500 text-white py-4 px-6 rounded-2xl font-semibold text-lg hover:bg-emerald-700 dark:hover:bg-emerald-600 transition focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 inline-block text-center">
                        Donasi Sekarang
                    </a>

                    <!-- Campaign Info -->
                    <div class="mt-6 pt-6 border-t border-gray-100 dark:border-slate-600 space-y-3">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-600 dark:text-slate-300">Status</span>
                            @php
                                $statusBadge = \App\Helper\CampaignHelper::getStatusBadge($campaign->status);
                            @endphp
                            <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium {{ $statusBadge['class'] }}">
                                <i class="{{ $statusBadge['icon'] }} mr-1"></i>
                                {{ $statusBadge['text'] }}
                            </span>
                        </div>
                        @if($campaign->end_date)
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-600 dark:text-slate-300">Sisa Waktu</span>
                                <span class="font-semibold text-gray-900 dark:text-white">{{ $progressData['days_left'] }} hari</span>
                            </div>
                        @endif
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-600 dark:text-slate-300">Total Donatur</span>
                            <span
                                class="font-semibold text-gray-900 dark:text-white">{{ number_format($campaign->donations_count, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-600 dark:text-slate-300">Kategori</span>
                            <span
                                class="font-semibold text-gray-900 dark:text-white capitalize">{{ $campaign->category->name ?? 'N/A' }}</span>
                        </div>
                        @if($campaign->is_featured)
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-600 dark:text-slate-300">Featured</span>
                                <span class="inline-flex items-center text-amber-600 dark:text-amber-400">
                                        ⭐
                                    </span>
                            </div>
                        @endif
                        @if($campaign->start_date)
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-600 dark:text-slate-300">Mulai</span>
                                <span
                                    class="font-semibold text-gray-900 dark:text-white">{{ $campaign->start_date->format('d M Y') }}</span>
                            </div>
                        @endif
                        @if($campaign->end_date)
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-600 dark:text-slate-300">Berakhir</span>
                                <span
                                    class="font-semibold text-gray-900 dark:text-white">{{ $campaign->end_date->format('d M Y') }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Recent Donors -->
                <div class="bg-white dark:bg-slate-800 rounded-3xl border border-gray-200 dark:border-slate-600 p-6 shadow-sm">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Donatur Terbaru</h3>
                        <span class="text-sm text-gray-500 dark:text-slate-400">{{ $campaign->donations_count }} donatur</span>
                    </div>

                    <div class="space-y-3">
                        @forelse($campaign->donations->take(5) as $donation)
                            <div
                                class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-slate-700 rounded-2xl hover:bg-gray-100 dark:hover:bg-slate-600 transition-colors">
                                <div
                                    class="w-12 h-12 bg-gradient-to-br from-emerald-400 to-emerald-600 rounded-full flex items-center justify-center flex-shrink-0">
                                    <span
                                        class="text-white font-bold text-sm">{{ strtoupper(mb_substr($donation->donor_name ?? 'A', 0, 1)) }}</span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 mb-1">
                                        <p class="font-semibold text-gray-900 dark:text-white truncate">{{ $donation->donor_name ?? 'Anonymous' }}</p>
                                        @if($donation->is_anonymous)
                                            <span class="text-xs text-gray-500 dark:text-slate-400">(Anonim)</span>
                                        @endif
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <p class="text-sm font-bold text-emerald-600 dark:text-emerald-400">
                                            Rp {{ number_format($donation->amount, 0, ',', '.') }}</p>
                                        <div class="flex items-center gap-1 text-xs text-gray-500 dark:text-slate-400">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                      d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                                                      clip-rule="evenodd"></path>
                                            </svg>
                                            {{ $donation->created_at->diffForHumans() }}
                                        </div>
                                    </div>
                                    @if($donation->message)
                                        <p class="text-xs text-gray-600 dark:text-slate-300 mt-1 line-clamp-2 italic">
                                            "{{ $donation->message }}"</p>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8">
                                <div
                                    class="w-16 h-16 bg-gray-100 dark:bg-slate-700 rounded-full flex items-center justify-center mx-auto mb-3">
                                    <svg class="w-8 h-8 text-gray-400 dark:text-slate-500" fill="none" stroke="currentColor"
                                         viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                </div>
                                <p class="text-sm text-gray-500 dark:text-slate-400 mb-1">Belum ada donasi</p>
                                <p class="text-xs text-gray-400 dark:text-slate-500">Jadilah yang pertama berdonasi!</p>
                            </div>
                        @endforelse
                    </div>

                    @if($campaign->donations_count > 5)
                        <div class="mt-4 pt-4 border-t border-gray-100 dark:border-slate-600">
                            <button
                                class="w-full text-center text-sm text-emerald-600 dark:text-emerald-400 hover:text-emerald-700 dark:hover:text-emerald-300 font-medium">
                                Lihat Semua Donatur ({{ $campaign->donations_count }})
                            </button>
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </div>

    <!-- Article Modal Component -->
    <x-article-modal
        :is-open="$isArticleModalOpen"
        :title="$articleModal['title'] ?? null"
        :created-at="$articleModal['created_at'] ?? null"
        :author="$articleModal['author'] ?? null"
        :content="$articleModal['content'] ?? null"
        close-action="closeArticle"
    />

</div>

