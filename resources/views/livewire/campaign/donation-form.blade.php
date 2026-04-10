<div class="min-h-screen bg-slate-50 py-8">
    <div class="mx-auto max-w-2xl px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center gap-3 mb-4">
                <a href="{{ route('campaign.show', $campaign->slug) }}"
                   class="flex items-center justify-center w-10 h-10 rounded-full bg-white border border-slate-200 hover:bg-slate-50 transition-colors">
                    <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15 19l-7-7 7-7"></path>
                    </svg>
                </a>
                <h1 class="text-2xl font-bold text-slate-900">Metode Pembayaran</h1>
            </div>
        </div>

        <!-- Campaign Info -->
        <div class="bg-white rounded-3xl border border-slate-200 p-6 mb-8 shadow-sm">
            <div class="flex gap-4">
                <div class="w-20 h-20 bg-slate-100 rounded-2xl overflow-hidden flex-shrink-0">
                    <img src="{{ \App\Helper\CampaignHelper::getImageUrl($campaign->image) }}"
                         alt="{{ $campaign->title }}"
                         class="w-full h-full object-cover">
                </div>
                <div class="flex-1">
                    <p class="text-sm text-slate-600 mb-1">Anda akan berdonasi untuk project:</p>
                    <h2 class="text-lg font-semibold text-slate-900 line-clamp-2">{{ $campaign->title }}</h2>
                </div>
            </div>
        </div>

        <!-- Donation Form -->
        @if ($errors->any())
            <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-2xl">
                <p class="font-semibold text-red-700">Terjadi beberapa kesalahan:</p>
                <ul class="mt-2 list-disc list-inside text-sm text-red-600">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Nominal Donasi -->
        <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-sm mb-4">
            <h3 class="text-lg font-semibold text-slate-900 mb-4">Nominal Donasi</h3>

            <div class="mb-4 p-3 bg-emerald-50 border border-emerald-200 rounded-2xl">
                <p class="text-sm text-slate-600 mb-1">Nominal donasi Anda sebesar</p>
                <div class="relative">
                    <span class="absolute left-0 top-1/2 transform -translate-y-1/2 text-emerald-600 font-semibold text-xl">Rp</span>
                    <input type="text"
                           wire:model="customAmount"
                           wire:blur="setCustomAmount"
                           placeholder="Masukkan nominal"
                           class="w-full pl-8 pr-4 py-2 bg-transparent text-xl font-bold text-emerald-600 border-none focus:outline-none focus:ring-0 placeholder-emerald-400">
                </div>
                <p class="text-xs text-slate-500 mt-2">Jumlah harus lebih besar dari Rp 10.000,-</p>
                @error('selectedAmount')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Predefined Amounts -->
            <div class="mb-3">
                <p class="text-sm text-slate-600 mb-3">Pilih nominal donasi:</p>
                <div class="grid grid-cols-2 gap-3">
                    @foreach($predefinedAmounts as $amount)
                        <button type="button"
                                wire:click="selectAmount({{ $amount }})"
                                class="flex items-center justify-between p-4 rounded-2xl border transition-all
                                       @if($selectedAmount == $amount)
                                           border-emerald-500 bg-emerald-50 text-emerald-700
                                       @else
                                           border-slate-200 bg-white text-slate-700 hover:border-slate-300
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
        <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-sm mb-4">
            <h3 class="text-lg font-semibold text-slate-900 mb-4">Doa & Pesan</h3>

            <div class="mb-3">
                <label class="block text-sm font-medium text-slate-700 mb-2">
                    Pesan Doa (Opsional)
                </label>

                <div x-data="{ charCount: '{{ strlen($message ?? '') }}' }"
                     x-init="charCount = $refs.message.value.length">
                    <textarea wire:model="message"
                              x-ref="message"
                              @input="charCount = $refs.message.value.length"
                              rows="4"
                              maxlength="500"
                              placeholder="Tuliskan doa atau pesan untuk campaign ini..."
                              class="w-full px-4 py-3 border border-slate-200 bg-white rounded-2xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 resize-none text-slate-900 placeholder-slate-400 @error('message') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror"></textarea>
                    <div class="flex justify-between items-center mt-2">
                        <p class="text-xs @error('message') text-red-500 @else text-slate-500 @enderror">
                            Maksimal 500 karakter
                        </p>
                        <p class="text-xs font-medium"
                           :class="{
                               'text-red-500': charCount > 500,
                               'text-amber-500': charCount > 450 && charCount <= 500,
                               'text-slate-400': charCount <= 450
                           }"
                           x-text="charCount + '/500'"></p>
                    </div>
                </div>
                @error('message')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center gap-3">
                <input type="checkbox"
                       wire:model="isAnonymous"
                       id="isAnonymous"
                       class="w-4 h-4 text-emerald-600 border-slate-300 rounded focus:ring-emerald-500 bg-white">
                <label for="isAnonymous" class="text-sm text-slate-700">
                    Sembunyikan nama saya (Donasi Anonim)
                </label>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="sticky bottom-0 bg-white border-t border-slate-200 p-6 -mx-4 sm:-mx-6 lg:-mx-8">
            <button type="button"
                    wire:click="proceedToPayment"
                    class="w-full bg-emerald-600 text-white py-4 px-6 rounded-2xl font-semibold text-lg hover:bg-emerald-700 transition focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed"
                    wire:loading.attr="disabled"
            >
                Lanjut Pembayaran
            </button>
        </div>
    </div>

    <!-- Error Modal -->
    @if($showErrorModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm" wire:click.self="closeErrorModal">
            <div class="bg-white rounded-3xl shadow-2xl max-w-md w-full p-6 transform transition-all">
                <!-- Modal Header -->
                <div class="text-center mb-6">
                    <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-2">{{ $errorModalTitle }}</h3>
                    <p class="text-slate-600">{{ $errorModalMessage }}</p>
                </div>

                <!-- Modal Footer -->
                <div class="flex justify-center">
                    <button wire:click="closeErrorModal"
                            class="px-6 py-3 bg-emerald-600 text-white rounded-2xl font-semibold hover:bg-emerald-700 transition focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2">
                        Mengerti
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>