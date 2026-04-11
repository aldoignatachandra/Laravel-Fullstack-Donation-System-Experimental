<?php

declare(strict_types=1);

use App\Listeners\AfterUserRegistered;
use App\Models\User;
use Database\Seeders\ShieldSeeder;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('logs when user is registered', function () {
    // Seed roles first
    $this->seed(ShieldSeeder::class);

    Log::spy();

    $user = User::factory()->create([
        'email' => 'john@example.com',
    ]);

    $listener = new AfterUserRegistered;

    $listener->handle(new Registered($user));

    Log::shouldHaveReceived('info')->once()->with(
        'User registered',
        [
            'user_id' => $user->id,
            'email' => 'john@example.com',
        ]
    );
});
