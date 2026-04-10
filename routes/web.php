<?php

use App\Livewire\Campaign\DonationForm;
use App\Livewire\Campaign\ShowCampaign;
use App\Livewire\Dashboard\Dashboard;
use App\Livewire\Dashboard\Donations;
use App\Livewire\LandingPage;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/', LandingPage::class)->name('home');
Route::get('/campaign/{slug}', ShowCampaign::class)->name('campaign.show');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::get('/campaign/{slug}/donate', DonationForm::class)->name('campaign.donate');
    Route::get('/donation/payment', function () {
        return view('donation.payment');
    })->name('donation.payment');

    Route::group([
        'prefix' => 'dashboard',
    ], function () {
        Route::get('/', Dashboard::class)->name('dashboard');
        Route::get('donations', Donations::class)->name('donations');
        Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
        Volt::route('settings/password', 'settings.password')->name('settings.password');
        Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
    });

});

require __DIR__.'/auth.php';
