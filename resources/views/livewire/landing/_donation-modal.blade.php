<!-- Donation Modal -->
<div 
    x-show="showDonationModal" 
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 z-50 overflow-y-auto"
    style="display: none;"
>
    <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
        <!-- Backdrop -->
        <div 
            x-show="showDonationModal"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
            @click="showDonationModal = false"
        ></div>

        <!-- Modal Content -->
        <div 
            x-show="showDonationModal"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            class="relative transform overflow-hidden rounded-3xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg"
        >
            <!-- Header -->
            <div class="bg-emerald-600 px-6 py-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-white">Lakukan Donasi</h3>
                    <button 
                        @click="showDonationModal = false"
                        class="rounded-full p-1 text-white hover:bg-emerald-700 transition"
                    >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <p class="text-emerald-100 text-sm mt-1" x-text="`Campaign: ${selectedCampaign?.title || ''}`"></p>
            </div>

            <!-- Body -->
            <div class="px-6 py-6">
                <!-- Campaign Info -->
                <div class="flex items-center gap-4 mb-6 p-4 bg-gray-50 rounded-2xl">
                    <img 
                        :src="selectedCampaign?.image" 
                        :alt="selectedCampaign?.title"
                        class="w-16 h-16 rounded-2xl object-cover"
                    />
                    <div class="flex-1">
                        <h4 class="font-semibold text-gray-900 line-clamp-2" x-text="selectedCampaign?.title"></h4>
                        <p class="text-sm text-gray-600" x-text="selectedCampaign?.location"></p>
                    </div>
                </div>

                <!-- Donation Amount -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-3">Pilih Nominal Donasi</label>
                    <div class="grid grid-cols-3 gap-3 mb-4">
                        <template x-for="amount in quickAmounts" :key="amount">
                            <button
                                @click="selectedAmount = amount; customAmount = amount"
                                :class="`p-3 text-center rounded-2xl border transition ${
                                    selectedAmount === amount
                                        ? 'border-emerald-500 bg-emerald-50 text-emerald-700'
                                        : 'border-gray-200 bg-white text-gray-700 hover:border-gray-300'
                                }`"
                                x-text="formatIDR(amount)"
                            ></button>
                        </template>
                    </div>
                    <div class="relative">
                        <input
                            type="number"
                            x-model="customAmount"
                            @input="selectedAmount = customAmount"
                            placeholder="Atau masukkan nominal lain"
                            class="w-full px-4 py-3 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                        />
                        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 text-sm">IDR</span>
                    </div>
                </div>

                <!-- Donor Information -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-3">Informasi Donatur</label>
                    <div class="space-y-3">
                        <input
                            type="text"
                            x-model="donorName"
                            placeholder="Nama lengkap"
                            class="w-full px-4 py-3 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                        />
                        <input
                            type="email"
                            x-model="donorEmail"
                            placeholder="Email (opsional)"
                            class="w-full px-4 py-3 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                        />
                        <textarea
                            x-model="donorMessage"
                            placeholder="Pesan untuk penerima (opsional)"
                            rows="3"
                            class="w-full px-4 py-3 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 resize-none"
                        ></textarea>
                    </div>
                </div>

                <!-- Payment Method -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-3">Metode Pembayaran</label>
                    <div class="space-y-2">
                        <label class="flex items-center gap-3 p-3 border border-gray-200 rounded-2xl cursor-pointer hover:bg-gray-50">
                            <input type="radio" x-model="paymentMethod" value="bank_transfer" class="text-emerald-600 focus:ring-emerald-500">
                            <div class="flex-1">
                                <div class="font-medium text-gray-900">Transfer Bank</div>
                                <div class="text-sm text-gray-600">BCA, Mandiri, BNI, BRI</div>
                            </div>
                        </label>
                        <label class="flex items-center gap-3 p-3 border border-gray-200 rounded-2xl cursor-pointer hover:bg-gray-50">
                            <input type="radio" x-model="paymentMethod" value="e_wallet" class="text-emerald-600 focus:ring-emerald-500">
                            <div class="flex-1">
                                <div class="font-medium text-gray-900">E-Wallet</div>
                                <div class="text-sm text-gray-600">GoPay, OVO, DANA, LinkAja</div>
                            </div>
                        </label>
                        <label class="flex items-center gap-3 p-3 border border-gray-200 rounded-2xl cursor-pointer hover:bg-gray-50">
                            <input type="radio" x-model="paymentMethod" value="credit_card" class="text-emerald-600 focus:ring-emerald-500">
                            <div class="flex-1">
                                <div class="font-medium text-gray-900">Kartu Kredit/Debit</div>
                                <div class="text-sm text-gray-600">Visa, Mastercard, JCB</div>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Summary -->
                <div class="bg-gray-50 rounded-2xl p-4 mb-6">
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-600">Nominal Donasi:</span>
                        <span class="font-semibold text-gray-900" x-text="formatIDR(selectedAmount)"></span>
                    </div>
                    <div class="flex justify-between items-center text-sm mt-2">
                        <span class="text-gray-600">Biaya Admin:</span>
                        <span class="font-semibold text-gray-900">Rp 0</span>
                    </div>
                    <div class="border-t border-gray-200 mt-2 pt-2">
                        <div class="flex justify-between items-center">
                            <span class="font-semibold text-gray-900">Total:</span>
                            <span class="font-bold text-lg text-emerald-600" x-text="formatIDR(selectedAmount)"></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="bg-gray-50 px-6 py-4 flex gap-3">
                <button
                    @click="showDonationModal = false"
                    class="flex-1 px-4 py-3 border border-gray-300 text-gray-700 rounded-2xl font-medium hover:bg-gray-100 transition"
                >
                    Batal
                </button>
                <button
                    @click="processDonation"
                    :disabled="!canProcessDonation"
                    :class="`flex-1 px-4 py-3 rounded-2xl font-medium transition ${
                        canProcessDonation
                            ? 'bg-emerald-600 text-white hover:bg-emerald-700'
                            : 'bg-gray-300 text-gray-500 cursor-not-allowed'
                    }`"
                >
                    Lanjutkan Pembayaran
                </button>
            </div>
        </div>
    </div>
</div>
