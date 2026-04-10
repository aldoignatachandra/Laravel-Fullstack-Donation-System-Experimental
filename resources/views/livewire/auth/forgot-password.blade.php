<?php

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\URL;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.auth.custom')] class extends Component
{
    public string $email = '';

    public ?string $errorMessage = null;

    public ?string $successMessage = null;

    /**
     * Send a password reset link to the provided email address.
     */
    public function sendPasswordResetLink(): void
    {
        $this->validate([
            'email' => ['required', 'string', 'email'],
        ]);

        $this->errorMessage = null;
        $this->successMessage = null;

        // Check if user exists first
        $user = User::where('email', $this->email)->first();

        if (! $user) {
            $this->errorMessage = 'Email tidak terdaftar. Silakan periksa kembali alamat email Anda atau daftar akun baru.';

            return;
        }

        $status = Password::sendResetLink($this->only('email'));

        if ($status === Password::RESET_LINK_SENT) {
            $this->successMessage = 'Link reset password telah dikirim ke email Anda. Silakan periksa inbox Anda.';

            // Store debug info for local environment
            if (app()->environment('local')) {
                // Generate a new plain token and create the reset URL
                $plainToken = bin2hex(random_bytes(32));

                // Store the hashed token in the database
                DB::table('password_reset_tokens')->updateOrInsert(
                    ['email' => $this->email],
                    [
                        'email' => $this->email,
                        'token' => bcrypt($plainToken),
                        'created_at' => now(),
                    ]
                );

                // Generate the reset URL with the PLAIN token
                $resetUrl = URL::to('/reset-password/'.$plainToken.'?email='.urlencode($this->email));
                session()->flash('debug_reset_url', $resetUrl);
            }
        } else {
            $this->errorMessage = 'Terjadi kesalahan saat mengirim link reset password. Silakan coba lagi.';
        }
    }
}; ?>

<div>
    {{-- Header --}}
    <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-amber-100 mb-4">
            <svg class="w-8 h-8 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
            </svg>
        </div>
        <h1 class="text-2xl font-bold text-slate-900 mb-2">Lupa Kata Sandi?</h1>
        <p class="text-slate-600">Jangan khawatir, kami akan membantu Anda mengatur ulang</p>
    </div>

    {{-- Error Message --}}
    @if ($errorMessage)
        <div class="mb-6 p-4 rounded-2xl bg-red-50 border border-red-200">
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div class="flex-1">
                    <p class="text-sm font-medium text-red-800">Terjadi Kesalahan</p>
                    <p class="text-sm text-red-700 mt-1">{{ $errorMessage }}</p>
                </div>
                <button wire:click="$set('errorMessage', null)" class="text-red-400 hover:text-red-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
    @endif

    {{-- Success Message --}}
    @if ($successMessage)
        <div class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200">
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-emerald-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div class="flex-1">
                    <p class="text-sm font-medium text-emerald-800">Berhasil!</p>
                    <p class="text-sm text-emerald-700 mt-1">{{ $successMessage }}</p>
                </div>
                <button wire:click="$set('successMessage', null)" class="text-emerald-400 hover:text-emerald-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
    @endif

    {{-- Debug URL - Only in Local Environment --}}
    @if(app()->environment('local') && session('debug_reset_url'))
        <div class="mb-6 p-4 rounded-2xl bg-blue-50 border border-blue-200">
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path>
                </svg>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-blue-800 mb-2">DEBUG MODE - Reset URL:</p>
                    <a href="{{ session('debug_reset_url') }}" class="text-sm text-blue-600 hover:text-blue-800 break-all underline" target="_blank">
                        {{ session('debug_reset_url') }}
                    </a>
                    <p class="text-xs text-blue-500 mt-2">Klik link di atas untuk langsung reset password (khusus development)</p>
                </div>
            </div>
        </div>
    @endif

    <form wire:submit="sendPasswordResetLink" class="space-y-5">
        {{-- Email --}}
        <div>
            <label for="email" class="block text-sm font-semibold text-slate-700 mb-2">Alamat Email</label>
            <input
                wire:model="email"
                type="email"
                id="email"
                required
                autofocus
                placeholder="nama@email.com"
                class="w-full px-4 py-3 rounded-2xl border border-slate-200 bg-white text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all"
            >
            @error('email')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Submit Button --}}
        <button
            type="submit"
            wire:loading.attr="disabled"
            class="w-full py-3.5 px-4 rounded-2xl bg-emerald-600 text-white font-semibold text-sm hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:ring-offset-2 transition-all disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
        >
            <span wire:loading.remove>Kirim Link Reset Password</span>
            <span wire:loading>Mengirim...</span>
            <svg wire:loading.remove class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
            </svg>
            <svg wire:loading class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </button>
    </form>

    {{-- Back to Login --}}
    <div class="mt-8 text-center">
        <a href="{{ route('login') }}" wire:navigate class="inline-flex items-center gap-2 text-sm text-slate-600 hover:text-emerald-600 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Kembali ke halaman login
        </a>
    </div>

    {{-- Help Text --}}
    <div class="mt-8 pt-6 border-t border-slate-100">
        <div class="flex items-start gap-3 p-4 rounded-2xl bg-slate-50">
            <svg class="w-5 h-5 text-slate-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div class="text-sm text-slate-600">
                <p class="font-medium text-slate-700 mb-1">Butuh bantuan?</p>
                <p>Link reset password akan dikirim ke email terdaftar Anda. Periksa folder spam/junk jika tidak menemukannya.</p>
            </div>
        </div>
    </div>
</div>