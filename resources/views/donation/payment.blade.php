<x-layouts.beramal :title="__('Pembayaran Diterima')">
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col items-center justify-center min-h-[60vh]">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg rounded-xl border border-gray-200 dark:border-gray-700 max-w-md w-full">
                    <div class="p-8 text-center">
                        <div class="mx-auto h-16 w-16 text-green-500 mb-6">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-full h-full">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="text-2xl font-semibold text-gray-900 dark:text-white mb-2">
                            Terima kasih, pembayaran diterima!
                        </h3>
                        <p class="text-gray-600 dark:text-gray-400 mb-6 leading-relaxed">
                            Donasi Anda telah berhasil kami terima. Bukti dan rincian transaksi akan dikirimkan ke email Anda.
                        </p>
                        <div class="flex flex-col sm:flex-row gap-3 justify-center">
                            <a href="{{ route('donations') }}"
                               class="inline-flex items-center justify-center px-6 py-3 bg-blue-600 border border-transparent rounded-lg font-semibold text-sm text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150 w-full sm:w-auto">
                                Lihat Donasi Saya
                            </a>
                            <a href="{{ route('home') }}"
                               class="inline-flex items-center justify-center px-6 py-3 bg-gray-600 border border-transparent rounded-lg font-semibold text-sm text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150 w-full sm:w-auto">
                                Kembali ke Beranda
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.beramal>
