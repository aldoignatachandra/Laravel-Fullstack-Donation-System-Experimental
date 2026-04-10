@php use App\Filament\Resources\Donations\Helpers\DonationHelper; @endphp

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- Page Header --}}
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-slate-900">Daftar Donasi Saya</h1>
        <p class="text-slate-600 mt-2 text-lg">Kelola dan pantau semua donasi yang telah Anda berikan</p>
    </div>

    {{-- Search and Filter Section --}}
    <div class="bg-white rounded-3xl shadow-lg border border-slate-200/60 p-6 mb-8">
        <div class="flex flex-col lg:flex-row gap-4 lg:items-end">
            {{-- Search Input --}}
            <div class="flex-1 max-w-lg">
                <label class="block text-sm font-semibold text-slate-700 mb-2">Cari Campaign</label>
                <div class="relative">
                    <input wire:model.live.debounce.300ms="search" type="text"
                        placeholder="Cari berdasarkan nama campaign..."
                        class="w-full pl-10 pr-4 py-3 rounded-2xl border border-slate-200 bg-white text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-400" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
            </div>

            {{-- Filter and Reset Group --}}
            <div class="flex flex-col sm:flex-row gap-3 lg:items-end">
                {{-- Status Filter - Custom Dropdown --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Filter Status</label>
                    <div class="relative">
                        <select wire:model.live="statusFilter"
                            class="w-full sm:w-[220px] appearance-none px-4 py-3 pr-10 rounded-2xl border border-slate-200 bg-white text-slate-900 font-medium focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all cursor-pointer hover:border-slate-300">
                            <option value="">Semua Status</option>
                            <option value="{{ \App\Models\Donation::STATUS_PENDING }}">Menunggu Pembayaran</option>
                            <option value="{{ \App\Models\Donation::STATUS_PAID }}">Berhasil</option>
                            <option value="{{ \App\Models\Donation::STATUS_FAILED }}">Gagal</option>
                            <option value="{{ \App\Models\Donation::STATUS_CANCELLED }}">Dibatalkan</option>
                        </select>
                        <div class="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none">
                            <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- Reset Button - Shows when any filter is active --}}
                <div class="flex items-end" @if($search === '' && $statusFilter === '') style="display: none;" @endif>
                    <button wire:click="clearFilters" type="button"
                        class="inline-flex items-center justify-center gap-2 px-4 py-3 rounded-2xl border border-slate-200 text-slate-700 hover:bg-slate-50 hover:border-slate-300 transition-all font-semibold text-sm h-[50px] bg-white shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Reset Filter
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Donations List --}}
    <div class="space-y-6">
        @forelse($donations as $donation)
            @php
                $statusColor = DonationHelper::getDonationStatusColor($donation->status);
                $statusLabel = DonationHelper::getDonationStatusLabel($donation->status);

                $colorClasses = match ($statusColor) {
                    'success' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                    'warning' => 'bg-amber-100 text-amber-800 border-amber-200',
                    'danger' => 'bg-red-100 text-red-800 border-red-200',
                    'secondary' => 'bg-slate-100 text-slate-800 border-slate-200',
                    default => 'bg-slate-100 text-slate-800 border-slate-200',
                };

                $indonesianLabels = match ($statusLabel) {
                    'Paid' => 'Berhasil',
                    'Pending' => 'Menunggu Pembayaran',
                    'Failed' => 'Gagal',
                    'Cancelled' => 'Dibatalkan',
                    default => 'Tidak diketahui',
                };

                $statusIcon = match ($statusColor) {
                    'success'
                        => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>',
                    'warning'
                        => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
                    'danger'
                        => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>',
                    default
                        => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
                };
            @endphp

            <div
                class="bg-white rounded-3xl shadow-lg border border-slate-200/60 overflow-hidden hover:shadow-xl transition-shadow">
                {{-- Card Header with Status Badge --}}
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                    <div class="flex items-center gap-2 text-sm text-slate-500">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                            </path>
                        </svg>
                        {{ $this->formatDate($donation->created_at) }}
                    </div>
                    <div
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-sm font-semibold border {{ $colorClasses }}">
                        {!! $statusIcon !!}
                        {{ $indonesianLabels }}
                    </div>
                </div>

                {{-- Card Body --}}
                <div class="p-6">
                    <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-6">
                        {{-- Left: Campaign Info --}}
                        <div class="flex-1">
                            {{-- Campaign Title as Clickable Badge --}}
                            <a href="{{ route('campaign.show', $donation->campaign->slug) }}"
                                class="inline-flex items-center gap-2 px-3 py-1.5 bg-emerald-50 text-emerald-700 rounded-lg text-sm font-medium border border-emerald-200 hover:bg-emerald-100 transition mb-3">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z">
                                    </path>
                                </svg>
                                {{ $donation->campaign->title }}
                            </a>

                            @if ($donation->message)
                                <div class="mt-3 p-4 bg-slate-50 rounded-2xl border border-slate-100">
                                    <p class="text-sm text-slate-600 italic">
                                        "{{ $donation->message }}"
                                    </p>
                                </div>
                            @endif

                            {{-- Meta Info --}}
                            <div class="mt-4 flex flex-wrap items-center gap-4 text-sm text-slate-500">
                                <div class="flex items-center gap-1.5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3 3 0 00-3 3m0 0h12M9 14v3m0-3h12">
                                        </path>
                                    </svg>
                                    Order ID: {{ $donation->order_id }}
                                </div>
                                @if ($donation->paid_at)
                                    <div class="flex items-center gap-1.5 text-emerald-600">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Dibayar: {{ $this->formatDate($donation->paid_at) }}
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- Right: Amount & Action --}}
                        <div class="flex flex-col items-start lg:items-end gap-3 lg:text-right">
                            <div>
                                <p class="text-sm text-slate-500 mb-1">Jumlah Donasi</p>
                                <p class="text-3xl font-bold text-slate-900">
                                    {{ \App\Helper\NumberHelper::formatIDR($donation->amount) }}
                                </p>
                            </div>

                            @if ($donation->status === \App\Models\Donation::STATUS_PENDING)
                                <a href="{{ route('donation.payment', ['order_id' => $donation->order_id]) }}"
                                    target="_blank"
                                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-600 text-white rounded-xl font-semibold text-sm hover:bg-emerald-700 transition shadow-md">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z">
                                        </path>
                                    </svg>
                                    Lanjutkan Pembayaran
                                </a>
                            @elseif ($donation->status === \App\Models\Donation::STATUS_PAID)
                                <button wire:click="showDonationDetails({{ $donation->id }})"
                                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-100 text-emerald-700 rounded-xl font-semibold text-sm hover:bg-emerald-200 transition shadow-md border border-emerald-200">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                        </path>
                                    </svg>
                                    Detail Donasi
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-3xl shadow-lg border border-slate-200/60 p-12 text-center">
                <div class="w-20 h-20 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                        </path>
                    </svg>
                </div>

                <h3 class="text-xl font-bold text-slate-900 mb-2">
                    @if ($search || $statusFilter)
                        Tidak ada donasi yang ditemukan
                    @else
                        Belum ada donasi
                    @endif
                </h3>

                <p class="text-slate-600 mb-6 max-w-md mx-auto">
                    @if ($search || $statusFilter)
                        Tidak ada donasi yang sesuai dengan filter yang dipilih. Coba ubah filter atau kata kunci
                        pencarian Anda.
                    @else
                        Anda belum melakukan donasi apapun. Mulai berdonasi untuk mendukung campaign yang Anda
                        pedulikan.
                    @endif
                </p>

                @if ($search || $statusFilter)
                    <button wire:click="clearFilters"
                        class="inline-flex items-center gap-2 px-5 py-2.5 border border-slate-200 text-slate-700 rounded-2xl font-semibold text-sm hover:bg-slate-50 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Reset Filter
                    </button>
                @else
                    <a href="{{ route('home') }}#campaigns"
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-600 text-white rounded-2xl font-semibold text-sm hover:bg-emerald-700 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z">
                            </path>
                        </svg>
                        Jelajahi Campaign
                    </a>
                @endif
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if ($donations->hasPages())
        <div class="mt-8">
            <div class="flex items-center justify-between bg-white rounded-2xl shadow-lg border border-slate-200/60 px-4 py-3">
                <div class="flex items-center gap-2">
                    {{-- Previous --}}
                    @if ($donations->onFirstPage())
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
                        @foreach ($donations->getUrlRange(1, $donations->lastPage()) as $page => $url)
                            @if ($page == $donations->currentPage())
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
                    @if ($donations->hasMorePages())
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
                    Menampilkan <span class="font-semibold">{{ $donations->firstItem() }}-{{ $donations->lastItem() }}</span> dari <span class="font-semibold">{{ $donations->total() }}</span> donasi
                </div>
            </div>
        </div>
    @endif

    {{-- Compact Donation Details Modal --}}
    @if($selectedDonation)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            {{-- Backdrop --}}
            <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity" aria-hidden="true"
                wire:click="closeDonationDetails"></div>

            {{-- Modal Container --}}
            <div class="flex min-h-full items-center justify-center p-4">
                {{-- Modal Panel --}}
                <div class="relative bg-white rounded-2xl text-left overflow-hidden shadow-2xl w-full max-w-md">
                    {{-- Modal Header --}}
                    <div class="bg-emerald-600 px-5 py-3 flex items-center justify-between">
                        <h3 class="text-base font-bold text-white" id="modal-title">Detail Donasi</h3>
                        <button wire:click="closeDonationDetails"
                            class="text-white/80 hover:text-white transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    {{-- Modal Body - Compact Layout --}}
                    <div class="px-5 py-4">
                        {{-- Status & Campaign Row --}}
                        <div class="flex items-center justify-between mb-4">
                            <div
                                class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-100 text-emerald-800 rounded-full text-xs font-semibold border border-emerald-200">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                                Berhasil
                            </div>
                            <span class="text-xs text-slate-400">{{ $selectedDonation->order_id }}</span>
                        </div>

                        {{-- Campaign Badge Link --}}
                        <a href="{{ route('campaign.show', $selectedDonation->campaign->slug) }}"
                            class="block mb-4 group">
                            <span class="text-xs text-slate-500 block mb-1">Campaign</span>
                            <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-emerald-50 text-emerald-700 rounded-lg text-sm font-medium border border-emerald-200 group-hover:bg-emerald-100 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                </svg>
                                {{ $selectedDonation->campaign->title }}
                            </div>
                        </a>

                        {{-- Amount --}}
                        <div class="bg-emerald-50 rounded-xl p-4 text-center mb-4 border border-emerald-100">
                            <p class="text-xs text-slate-500 mb-1">Jumlah Donasi</p>
                            <p class="text-2xl font-bold text-emerald-700">
                                {{ \App\Helper\NumberHelper::formatIDR($selectedDonation->amount) }}
                            </p>
                        </div>

                        {{-- Compact Details Grid --}}
                        <div class="grid grid-cols-2 gap-3 text-sm">
                            <div class="bg-slate-50 rounded-lg p-3">
                                <span class="text-xs text-slate-400 block mb-1">Tanggal Donasi</span>
                                <span class="font-medium text-slate-700">{{ $this->formatDate($selectedDonation->created_at) }}</span>
                            </div>

                            <div class="bg-slate-50 rounded-lg p-3">
                                <span class="text-xs text-slate-400 block mb-1">Tanggal Bayar</span>
                                <span class="font-medium text-emerald-600">{{ $this->formatDate($selectedDonation->paid_at) }}</span>
                            </div>

                            <div class="bg-slate-50 rounded-lg p-3">
                                <span class="text-xs text-slate-400 block mb-1">Metode</span>
                                <span class="font-medium text-slate-700">Midtrans</span>
                            </div>

                            <div class="bg-slate-50 rounded-lg p-3">
                                <span class="text-xs text-slate-400 block mb-1">Status</span>
                                <span class="font-medium text-emerald-600">Lunas</span>
                            </div>

                            <div class="bg-slate-50 rounded-lg p-3">
                                <span class="text-xs text-slate-400 block mb-1">Anonim</span>
                                <span class="font-medium text-slate-700">{{ $selectedDonation->is_anonymous ? 'Ya' : 'Tidak' }}</span>
                            </div>

                            @if ($selectedDonation->message)
                                <div class="col-span-2 bg-slate-50 rounded-lg p-3">
                                    <span class="text-xs text-slate-400 block mb-1">Pesan</span>
                                    <p class="text-sm text-slate-600 italic">"{{ $selectedDonation->message }}"</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Modal Footer --}}
                    <div class="bg-slate-50 px-5 py-3 flex justify-between items-center border-t border-slate-100">
                        <a href="{{ route('campaign.show', $selectedDonation->campaign->slug) }}"
                            class="inline-flex items-center gap-1.5 text-sm text-emerald-600 font-medium hover:text-emerald-700 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                            </svg>
                            Lihat Campaign
                        </a>
                        <button wire:click="closeDonationDetails"
                            class="inline-flex items-center gap-1.5 px-4 py-2 bg-slate-900 text-white rounded-lg font-medium text-sm hover:bg-slate-800 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7"></path>
                            </svg>
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
