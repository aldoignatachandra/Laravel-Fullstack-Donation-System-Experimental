@php use App\Filament\Resources\Donations\Helpers\DonationHelper; @endphp

<div class="flex w-full flex-1 flex-col gap-4 rounded-xl">
            <!-- Page Header -->
            <div class="mb-8">
                <flux:heading size="xl">Daftar Donasi Saya</flux:heading>
                <flux:text class="mt-2 text-zinc-600 dark:text-zinc-400">
                    Kelola dan pantau semua donasi yang telah Anda berikan
                </flux:text>
            </div>

        <!-- Search and Filter Section -->
        <div class="mb-8">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div class="flex-1 max-w-md">
                    <flux:input
                        wire:model.live.debounce.300ms="search"
                        placeholder="Cari berdasarkan nama campaign..."
                        icon="magnifying-glass"
                        class="w-full"
                    />
                </div>
                <div class="flex gap-3">
                    <flux:select
                        wire:model.live="statusFilter"
                        placeholder="Filter Status"
                        class="min-w-[200px]"
                    >
                        <option value="">Semua Status</option>
                        <option value="{{ \App\Models\Donation::STATUS_PENDING }}">Menunggu Pembayaran</option>
                        <option value="{{ \App\Models\Donation::STATUS_PAID }}">Berhasil</option>
                        <option value="{{ \App\Models\Donation::STATUS_FAILED }}">Gagal</option>
                        <option value="{{ \App\Models\Donation::STATUS_CANCELLED }}">Dibatalkan</option>
                    </flux:select>

                    @if($search || $statusFilter)
                        <flux:button
                            variant="ghost"
                            wire:click="clearFilters"
                            icon="x-mark"
                            class="whitespace-nowrap"
                        >
                            Reset Filter
                        </flux:button>
                    @endif
                </div>
            </div>
        </div>

        <!-- Donations List -->
        <div class="space-y-6">
            @forelse($donations as $donation)
                <div class="rounded-lg border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-800">
                    <div class="flex flex-col gap-6 sm:flex-row sm:items-start sm:justify-between">
                        <!-- Campaign Info -->
                        <div class="flex-1 space-y-4">
                            <div>
                                <flux:heading size="lg" class="text-zinc-900 dark:text-zinc-100">
                                    {{ $donation->campaign->title }}
                                </flux:heading>

                                @if($donation->message)
                                    <flux:callout class="mt-3" variant="subtle">
                                        <flux:callout.text>
                                            <span class="font-medium">Pesan:</span> {{ $donation->message }}
                                        </flux:callout.text>
                                    </flux:callout>
                                @endif
                            </div>

                            <!-- Donation Details -->
                            <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                                <div class="flex items-center gap-2">
                                    <flux:icon.calendar-days class="h-4 w-4 text-zinc-400 dark:text-zinc-500" />
                                    <flux:text size="sm" class="text-zinc-600 dark:text-zinc-400">
                                        <span class="font-medium">Tanggal:</span> {{ $this->formatDate($donation->created_at) }}
                                    </flux:text>
                                </div>

                                @if($donation->paid_at)
                                    <div class="flex items-center gap-2">
                                        <flux:icon.check-circle class="h-4 w-4 text-emerald-500 dark:text-emerald-400" />
                                        <flux:text size="sm" class="text-zinc-600 dark:text-zinc-400">
                                            <span class="font-medium">Dibayar:</span> {{ $this->formatDate($donation->paid_at) }}
                                        </flux:text>
                                    </div>
                                @endif

                                <div class="flex items-center gap-2">
                                    <flux:icon.identification class="h-4 w-4 text-zinc-400 dark:text-zinc-500" />
                                    <flux:text size="sm" class="text-zinc-600 dark:text-zinc-400">
                                        <span class="font-medium">Order ID:</span> {{ $donation->order_id }}
                                    </flux:text>
                                </div>
                            </div>
                        </div>

                        <!-- Amount and Actions -->
                        <div class="flex flex-col items-end gap-4 sm:items-end">
                            <div class="text-right">
                                <flux:heading size="2xl" class="text-zinc-900 dark:text-zinc-100">
                                    {{ \App\Helper\NumberHelper::formatIDR($donation->amount) }}
                                </flux:heading>

                                @php
                                    $statusColor = DonationHelper::getDonationStatusColor($donation->status);
                                    $statusLabel = DonationHelper::getDonationStatusLabel($donation->status);
                                    
                                    $colorClasses = match($statusColor) {
                                        'success' => 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400',
                                        'warning' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400',
                                        'danger' => 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400',
                                        'secondary' => 'bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400',
                                        default => 'bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400',
                                    };
                                    
                                    $indonesianLabels = match($statusLabel) {
                                        'Paid' => 'Berhasil',
                                        'Pending' => 'Pending',
                                        'Failed' => 'Gagal',
                                        'Cancelled' => 'Dibatalkan',
                                        default => 'Tidak diketahui',
                                    };
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $colorClasses }} mt-2">
                                    {{ $indonesianLabels }}
                                </span>
                            </div>

                            @if($donation->status === \App\Models\Donation::STATUS_PENDING)
                                <flux:button
                                    variant="primary"
                                    size="sm"
                                    icon="credit-card"
                                    onclick="window.open('{{ route('donation.payment', ['order_id' => $donation->order_id]) }}', '_blank')"
                                >
                                    Lanjutkan Pembayaran
                                </flux:button>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-16">
                    <div class="mx-auto h-16 w-16 text-zinc-400 dark:text-zinc-500 mb-4">
                        <flux:icon.currency-dollar class="h-16 w-16" />
                    </div>

                    <flux:heading size="lg" class="text-zinc-900 dark:text-zinc-100 mb-2">
                        @if($search || $statusFilter)
                            Tidak ada donasi yang ditemukan
                        @else
                            Belum ada donasi
                        @endif
                    </flux:heading>

                    <flux:text class="text-zinc-500 dark:text-zinc-400 mb-6 max-w-md mx-auto">
                        @if($search || $statusFilter)
                            Tidak ada donasi yang sesuai dengan filter yang dipilih. Coba ubah filter atau kata kunci pencarian Anda.
                        @else
                            Anda belum melakukan donasi apapun. Mulai berdonasi untuk mendukung campaign yang Anda pedulikan.
                        @endif
                    </flux:text>

                    @if($search || $statusFilter)
                        <flux:button variant="ghost" wire:click="clearFilters" icon="x-mark">
                            Reset Filter
                        </flux:button>
                    @else
                        <flux:button variant="primary" href="{{ route('home') }}" icon="heart">
                            Jelajahi Campaign
                        </flux:button>
                    @endif
                </div>
            @endforelse
        </div>

            <!-- Pagination -->
            @if($donations->hasPages())
                    {{ $donations->links() }}
            @endif
</div>
