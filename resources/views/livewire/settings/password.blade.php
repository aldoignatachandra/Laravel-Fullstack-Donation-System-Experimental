<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.dashboard')] class extends Component
{
    public string $current_password = '';

    public string $password = '';

    public string $password_confirmation = '';

    public function updatePassword(): void
    {
        try {
            $validated = $this->validate([
                'current_password' => ['required', 'string', 'current_password'],
                'password' => ['required', 'string', Password::defaults(), 'confirmed'],
            ]);
        } catch (ValidationException $e) {
            $this->reset('current_password', 'password', 'password_confirmation');
            throw $e;
        }

        Auth::user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        $this->reset('current_password', 'password', 'password_confirmation');
        $this->dispatch('password-updated');
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
            {{-- Password Update Form --}}
            <div class="bg-white rounded-3xl shadow-lg border border-slate-200/60 p-8">
                <div class="mb-6">
                    <h2 class="text-xl font-bold text-slate-900">Update Password</h2>
                    <p class="text-slate-600 mt-1">Pastikan akun Anda menggunakan password yang panjang dan acak untuk tetap aman</p>
                </div>

                <form wire:submit="updatePassword" class="space-y-6 max-w-lg">
                    {{-- Current Password --}}
                    <div>
                        <label for="current_password" class="block text-sm font-semibold text-slate-700 mb-2">Password Saat Ini</label>
                        <input wire:model="current_password" type="password" id="current_password" required autocomplete="current-password"
                            class="w-full px-4 py-3 rounded-2xl border border-slate-200 bg-white text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all"
                            placeholder="Masukkan password saat ini">
                        @error('current_password')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- New Password --}}
                    <div>
                        <label for="password" class="block text-sm font-semibold text-slate-700 mb-2">Password Baru</label>
                        <input wire:model="password" type="password" id="password" required autocomplete="new-password"
                            class="w-full px-4 py-3 rounded-2xl border border-slate-200 bg-white text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all"
                            placeholder="Minimal 8 karakter">
                        @error('password')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Confirm Password --}}
                    <div>
                        <label for="password_confirmation" class="block text-sm font-semibold text-slate-700 mb-2">Konfirmasi Password</label>
                        <input wire:model="password_confirmation" type="password" id="password_confirmation" required autocomplete="new-password"
                            class="w-full px-4 py-3 rounded-2xl border border-slate-200 bg-white text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all"
                            placeholder="Ulangi password baru">
                        @error('password_confirmation')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Submit Button --}}
                    <div class="flex items-center gap-4">
                        <button type="submit" wire:loading.attr="disabled"
                            class="px-6 py-3 bg-emerald-600 text-white rounded-2xl font-semibold text-sm hover:bg-emerald-700 transition shadow-md disabled:opacity-50 disabled:cursor-not-allowed">
                            <span wire:loading.remove>Simpan Password</span>
                            <span wire:loading>Menyimpan...</span>
                        </button>

                        @if (session('status') === 'password-updated')
                            <span class="text-sm font-medium text-emerald-600">Password berhasil diupdate!</span>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>