<?php

declare(strict_types=1);

use App\Listeners\AfterUserRegistered;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

uses(TestCase::class);

it('logs when user is registered', function () {
    Log::spy();

    $user = User::factory()->make([
        'id' => 123,
        'email' => 'john@example.com',
    ]);

    $listener = new AfterUserRegistered;

    $listener->handle(new Registered($user));

    Log::shouldHaveReceived('info')->once()->with(
        'User registered',
        [
            'user_id' => 123,
            'email' => 'john@example.com',
        ]
    );
});
