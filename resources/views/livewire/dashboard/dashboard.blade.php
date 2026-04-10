<div class="flex  flex-1 flex-col gap-4 rounded-xl">
    <div class="mb-8">
        <flux:heading size="xl">Dashboard</flux:heading>
        <flux:text class="mt-2 text-zinc-600 dark:text-zinc-400">
            Selamat datang di dashboard Anda
        </flux:text>
    </div>
    <!-- Summary Cards -->
    <div class="grid auto-rows-min gap-4 md:grid-cols-3">
        <!-- Total Donasi dalam Rupiah -->
        <div class="relative overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 p-6">
            <div class="text-left">
                <p class="text-sm font-medium text-neutral-600 dark:text-neutral-400 mb-2">Total Donasi</p>
                <p class="text-2xl font-bold text-neutral-900 dark:text-white">
                    {{ App\Helper\NumberHelper::formatIDR($totalDonationAmount) }}
                </p>
            </div>
        </div>

        <!-- Total Jumlah Donasi -->
        <div class="relative overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 p-6">
            <div class="text-left">
                <p class="text-sm font-medium text-neutral-600 dark:text-neutral-400 mb-2">Jumlah Donasi Terkonfirmasi</p>
                <p class="text-2xl font-bold text-neutral-900 dark:text-white">
                    {{ $totalDonationCount }}
                </p>
            </div>
        </div>

        <!-- Rata-rata Donasi -->
        <div class="relative overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 p-6">
            <div class="text-left">
                <p class="text-sm font-medium text-neutral-600 dark:text-neutral-400 mb-2">Rata-rata Donasi</p>
                <p class="text-2xl font-bold text-neutral-900 dark:text-white">
                    {{ $totalDonationCount > 0 ? App\Helper\NumberHelper::formatIDR($totalDonationAmount / $totalDonationCount) : '0' }}
                </p>
            </div>
        </div>
    </div>

    <!-- Daftar 5 Donasi Terbaru -->
    <div class="relative h-full flex-1 overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800">
        <div class="p-6">
            <h3 class="text-lg font-semibold text-neutral-900 dark:text-white mb-4">5 Donasi Terbaru</h3>

            @if($recentDonations->count() > 0)
                <div class="space-y-3">
                    @foreach($recentDonations as $donation)
                        <div class="p-4 rounded-lg border border-neutral-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-700/50">
                            <div class="flex items-start justify-between gap-4">
                                <!-- Left Section - Campaign Info -->
                                <div class="flex-1 min-w-0">
                                    <h4 class="font-medium text-neutral-900 dark:text-white mb-1">
                                        {{ $donation->campaign->title ?? 'Campaign tidak ditemukan' }}
                                    </h4>
                                    <p class="text-sm text-neutral-600 dark:text-neutral-400 mb-2">
                                        {{ $donation->created_at->format('d M Y, H:i') }}
                                    </p>
                                    @if($donation->message)
                                        <p class="text-sm text-neutral-500 dark:text-neutral-500 italic">
                                            "{{ Str::limit($donation->message, 50) }}"
                                        </p>
                                    @endif
                                </div>
                                
                                <!-- Right Section - Amount & Status -->
                                <div class="flex flex-col items-end text-right space-y-2">
                                    <p class="font-semibold text-neutral-900 dark:text-white text-lg">
                                        {{ App\Helper\NumberHelper::formatIDR($donation->amount) }}
                                    </p>
                                    @php
                                        $statusColor = \App\Filament\Resources\Donations\Helpers\DonationHelper::getDonationStatusColor($donation->status);
                                        $statusLabel = \App\Filament\Resources\Donations\Helpers\DonationHelper::getDonationStatusLabel($donation->status);
                                        
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
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $colorClasses }}">
                                        {{ $indonesianLabels }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-neutral-900 dark:text-white">Belum ada donasi</h3>
                    <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">Mulai berdonasi untuk melihat riwayat donasi Anda di sini.</p>
                </div>
            @endif
        </div>
    </div>
    </div>
</div>
