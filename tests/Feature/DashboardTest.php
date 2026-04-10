<?php

use App\Livewire\Dashboard\Dashboard;
use App\Models\Campaign;
use App\Models\Donation;
use App\Models\User;
use Livewire\Livewire;

test('guests are redirected to the login page', function () {
    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('login'));
});

test('authenticated users can visit the dashboard', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get(route('dashboard'));
    $response->assertStatus(200);
});

test('dashboard displays donation summary for authenticated user', function () {
    $user = User::factory()->create();
    $campaign = Campaign::factory()->create();

    // Create some donations for the user
    $paidDonations = Donation::factory()->paid()->count(3)->create([
        'user_id' => $user->id,
        'campaign_id' => $campaign->id,
        'amount' => 100000,
    ]);

    $pendingDonation = Donation::factory()->pending()->create([
        'user_id' => $user->id,
        'campaign_id' => $campaign->id,
        'amount' => 50000,
    ]);

    $this->actingAs($user);

    Livewire::test(Dashboard::class)
        ->assertSee('IDR') // Check for IDR currency
        ->assertSee('300,000.00') // Total amount (3 * 100000)
        ->assertSee('3') // Total count
        ->assertSee('100,000.00') // Average amount
        ->assertSee('Donasi Terbaru')
        ->assertSee($campaign->title);
});

test('dashboard shows empty state when user has no donations', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    Livewire::test(Dashboard::class)
        ->assertSee('IDR') // Check for IDR currency
        ->assertSee('0.00') // Check for amount
        ->assertSee('0')
        ->assertSee('Belum ada donasi')
        ->assertSee('Mulai berdonasi untuk melihat riwayat donasi Anda di sini.');
});

test('dashboard only shows paid donations in summary', function () {
    $user = User::factory()->create();
    $campaign = Campaign::factory()->create();

    // Create paid donation
    Donation::factory()->paid()->create([
        'user_id' => $user->id,
        'campaign_id' => $campaign->id,
        'amount' => 100000,
    ]);

    // Create pending donation (should not be counted in summary)
    Donation::factory()->pending()->create([
        'user_id' => $user->id,
        'campaign_id' => $campaign->id,
        'amount' => 50000,
    ]);

    $this->actingAs($user);

    Livewire::test(Dashboard::class)
        ->assertSee('IDR') // Check for IDR currency
        ->assertSee('100,000.00') // Only paid donation amount
        ->assertSee('1') // Only paid donation count
        ->assertSee('100,000.00'); // Average of paid donations only
});

test('dashboard shows recent donations with correct status', function () {
    $user = User::factory()->create();
    $campaign = Campaign::factory()->create();

    $paidDonation = Donation::factory()->paid()->create([
        'user_id' => $user->id,
        'campaign_id' => $campaign->id,
        'amount' => 100000,
        'message' => 'Semoga bermanfaat',
    ]);

    $pendingDonation = Donation::factory()->pending()->create([
        'user_id' => $user->id,
        'campaign_id' => $campaign->id,
        'amount' => 50000,
    ]);

    $this->actingAs($user);

    Livewire::test(Dashboard::class)
        ->assertSee('Berhasil') // Status for paid donation
        ->assertSee('Pending') // Status for pending donation
        ->assertSee('Semoga bermanfaat') // Message from paid donation (without quotes)
        ->assertSee('IDR') // Check for IDR currency
        ->assertSee('100,000.00') // Amount format
        ->assertSee('50,000.00'); // Amount format
});
