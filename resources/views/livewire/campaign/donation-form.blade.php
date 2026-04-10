<div class="min-h-screen bg-gray-50 dark:bg-slate-900 py-8">
    <div class="mx-auto max-w-2xl px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center gap-3 mb-4">
                <a href="#"
                   class="flex items-center justify-center w-10 h-10 rounded-full bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-600 hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors">
                    <svg class="w-5 h-5 text-gray-600 dark:text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15 19l-7-7 7-7"></path>
                    </svg>
                </a>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Metode Pembayaran</h1>
            </div>
        </div>

        <!-- Campaign Info -->
        <div class="bg-white dark:bg-slate-800 rounded-3xl border border-gray-200 dark:border-slate-600 p-6 mb-8 shadow-sm">
            <div class="flex gap-4">
                <div class="w-20 h-20 bg-gray-100 dark:bg-slate-700 rounded-2xl overflow-hidden flex-shrink-0">
                    <img src="{{ \App\Helper\CampaignHelper::getImageUrl($campaign->image) }}"
                         alt="{{ $campaign->title }}"
                         class="w-full h-full object-cover">
                </div>
                <div class="flex-1">
                    <p class="text-sm text-gray-600 dark:text-slate-300 mb-1">Anda akan berdonasi untuk project:</p>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white line-clamp-2">{{ $campaign->title }}</h2>
                </div>
            </div>
        </div>

        <!-- Donation Form -->
        @if ($errors->any())
            <div class="mb-4 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-2xl">
                <p class="font-semibold text-red-700 dark:text-red-300">Terjadi beberapa kesalahan:</p>
                <ul class="mt-2 list-disc list-inside text-sm text-red-600 dark:text-red-400">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Nominal Donasi -->
        <div class="bg-white dark:bg-slate-800 rounded-3xl border border-gray-200 dark:border-slate-600 p-6 shadow-sm mb-4">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Nominal Donasi</h3>

            <div class="mb-4 p-3 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-700 rounded-2xl">
                <p class="text-sm text-gray-600 dark:text-slate-300 mb-1">Nominal donasi Anda sebesar</p>
                <div class="relative">
                    <span class="absolute left-0 top-1/2 transform -translate-y-1/2 text-emerald-600 dark:text-emerald-400 font-semibold text-xl">Rp</span>
                    <input type="text"
                           wire:model="customAmount"
                           wire:blur="setCustomAmount"
                           placeholder="Masukkan nominal"
                           class="w-full pl-8 pr-4 py-2 bg-transparent text-xl font-bold text-emerald-600 dark:text-emerald-400 border-none focus:outline-none focus:ring-0 placeholder-emerald-400 dark:placeholder-emerald-500">
                </div>
                <p class="text-xs text-gray-500 dark:text-slate-400 mt-2">Jumlah harus lebih besar dari Rp 10.000,-</p>
                @error('selectedAmount')
                <p class="text-sm text-red-600 dark:text-red-400 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Predefined Amounts -->
            <div class="mb-3">
                <p class="text-sm text-gray-600 dark:text-slate-300 mb-3">Pilih nominal donasi:</p>
                <div class="grid grid-cols-2 gap-3">
                    @foreach($predefinedAmounts as $amount)
                        <button type="button"
                                wire:click="selectAmount({{ $amount }})"
                                class="flex items-center justify-between p-4 rounded-2xl border transition-all
                                       @if($selectedAmount == $amount)
                                           border-emerald-500 dark:border-emerald-400 bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300
                                       @else
                                           border-gray-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-gray-700 dark:text-slate-300 hover:border-gray-300 dark:hover:border-slate-500
                                       @endif">
                            <span class="font-semibold">Rp {{ number_format($amount, 0, ',', '.') }}</span>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9 5l7 7-7 7"></path>
                            </svg>
                        </button>
                    @endforeach
                </div>
            </div>

        </div>

        <!-- Doa & Pesan -->
        <div class="bg-white dark:bg-slate-800 rounded-3xl border border-gray-200 dark:border-slate-600 p-6 shadow-sm mb-4">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Doa & Pesan</h3>

            <div class="mb-3">
                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">
                    Pesan Doa (Opsional)
                </label>

                <textarea wire:model="message"
                          rows="4"
                          placeholder="Tuliskan doa atau pesan untuk campaign ini..."
                          class="w-full px-4 py-3 border border-gray-200 dark:border-slate-600 bg-white dark:bg-slate-700 rounded-2xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 resize-none text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-slate-500"></textarea>
                <div class="flex justify-between items-center mt-2">
                    <p class="text-xs text-gray-500 dark:text-slate-400">Maksimal 500 karakter</p>
                    <p class="text-xs text-gray-400 dark:text-slate-500">{{ strlen($message) }}/500</p>
                </div>
                @error('message')
                <p class="text-sm text-red-600 dark:text-red-400 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center gap-3">
                <input type="checkbox"
                       wire:model="isAnonymous"
                       id="isAnonymous"
                       class="w-4 h-4 text-emerald-600 dark:text-emerald-400 border-gray-300 dark:border-slate-600 rounded focus:ring-emerald-500 bg-white dark:bg-slate-700">
                <label for="isAnonymous" class="text-sm text-gray-700 dark:text-slate-300">
                    Sembunyikan nama saya (Donasi Anonim)
                </label>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="sticky bottom-0 bg-white dark:bg-slate-900 border-t border-gray-200 dark:border-slate-600 p-6 -mx-4 sm:-mx-6 lg:-mx-8">
            <button type="button"
                    wire:click="proceedToPayment"
                    class="w-full bg-emerald-600 dark:bg-emerald-500 text-white py-4 px-6 rounded-2xl font-semibold text-lg hover:bg-emerald-700 dark:hover:bg-emerald-600 transition focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed"
                    @disabled($selectedAmount < \App\Services\DonationService::MIN_DONATION_AMOUNT)
                    wire:loading.attr="disabled"
            >
                Lanjut Pembayaran
            </button>
        </div>
    </div>
</div>
