<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.dashboard')] class extends Component
{
    public string $name = '';

    public string $email = '';

    public function mount(): void
    {
        $this->name = Auth::user()->name;
        $this->email = Auth::user()->email;
    }

    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($user->id),
            ],
        ]);

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        $this->dispatch('profile-updated', name: $user->name);
    }

    public function resendVerificationNotification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false));

            return;
        }

        $user->sendEmailVerificationNotification();
        Session::flash('status', 'verification-link-sent');
    }
}; ?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    {{-- Page Header --}}
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-slate-900">Pengaturan Profil</h1>
        <p class="text-slate-600 mt-2 text-lg">Kelola informasi profil dan keamanan akun Anda</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        {{-- Sidebar Navigation --}}
        <div class="lg:col-span-1">
            <div class="bg-white rounded-3xl shadow-lg border border-slate-200/60 p-4">
                <nav class="space-y-1">
                    <a href="{{ route('settings.profile') }}" class="flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-semibold {{ request()->routeIs('settings.profile') ? 'bg-emerald-50 text-emerald-700' : 'text-slate-700 hover:bg-slate-50' }} transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Profil
                    </a>
                    <a href="{{ route('settings.password') }}" class="flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-semibold {{ request()->routeIs('settings.password') ? 'bg-emerald-50 text-emerald-700' : 'text-slate-700 hover:bg-slate-50' }} transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                        Password
                    </a>
                </nav>
            </div>
        </div>

        {{-- Main Content --}}
        <div class="lg:col-span-3">
            {{-- Profile Information Form --}}
            <div class="bg-white rounded-3xl shadow-lg border border-slate-200/60 p-8">
                <div class="mb-6">
                    <h2 class="text-xl font-bold text-slate-900">Informasi Profil</h2>
                    <p class="text-slate-600 mt-1">Update nama dan alamat email Anda</p>
                </div>

                <form wire:submit="updateProfileInformation" class="space-y-6 max-w-lg">
                    {{-- Name --}}
                    <div>
                        <label for="name" class="block text-sm font-semibold text-slate-700 mb-2">Nama Lengkap</label>
                        <input wire:model="name" type="text" id="name" required autofocus autocomplete="name"
                            class="w-full px-4 py-3 rounded-2xl border border-slate-200 bg-white text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all"
                            placeholder="Nama lengkap Anda">
                        @error('name')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div>
                        <label for="email" class="block text-sm font-semibold text-slate-700 mb-2">Alamat Email</label>
                        <input wire:model="email" type="email" id="email" required autocomplete="email"
                            class="w-full px-4 py-3 rounded-2xl border border-slate-200 bg-white text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all"
                            placeholder="email@example.com">
                        @error('email')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror

                        @if (auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! auth()->user()->hasVerifiedEmail())
                            <div class="mt-4 p-4 bg-amber-50 rounded-2xl border border-amber-200">
                                <p class="text-sm text-amber-800">
                                    Alamat email Anda belum terverifikasi.
                                    <button wire:click.prevent="resendVerificationNotification" class="font-semibold underline hover:text-amber-900 ml-1">
                                        Klik di sini untuk mengirim ulang email verifikasi.
                                    </button>
                                </p>

                                @if (session('status') === 'verification-link-sent')
                                    <p class="mt-2 text-sm font-medium text-emerald-700">
                                        Link verifikasi baru telah dikirim ke email Anda.
                                    </p>
                                @endif
                            </div>
                        @endif
                    </div>

                    {{-- Submit Button --}}
                    <div class="flex items-center gap-4">
                        <button type="submit" wire:loading.attr="disabled"
                            class="px-6 py-3 bg-emerald-600 text-white rounded-2xl font-semibold text-sm hover:bg-emerald-700 transition shadow-md disabled:opacity-50 disabled:cursor-not-allowed">
                            <span wire:loading.remove>Simpan Perubahan</span>
                            <span wire:loading>Menyimpan...</span>
                        </button>

                        @if (session('status') === 'profile-updated')
                            <span class="text-sm font-medium text-emerald-600">Tersimpan!</span>
                        @endif
                    </div>
                </form>
            </div>

            {{-- Delete Account Section --}}
            <div class="mt-8 bg-white rounded-3xl shadow-lg border border-slate-200/60 p-8">
                <div class="mb-6">
                    <h2 class="text-xl font-bold text-slate-900">Hapus Akun</h2>
                    <p class="text-slate-600 mt-1">Setelah akun dihapus, semua data dan donasi Anda akan dihapus permanen. Harap berhati-hati.</p>
                </div>

                <button onclick="if(confirm('Apakah Anda yakin ingin menghapus akun? Tindakan ini tidak dapat dibatalkan.')) { document.getElementById('delete-account-form').submit(); }"
                    class="px-6 py-3 bg-red-600 text-white rounded-2xl font-semibold text-sm hover:bg-red-700 transition shadow-md">
                    Hapus Akun
                </button>

                <form id="delete-account-form" method="POST" action="{{ route('logout') }}" class="hidden">
                    @csrf
                </form>
            </div>
        </div>
    </div>
</div>