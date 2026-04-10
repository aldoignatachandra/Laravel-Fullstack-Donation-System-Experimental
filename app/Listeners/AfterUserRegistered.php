<?php

namespace App\Listeners;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Log;

class AfterUserRegistered
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Registered $event): void
    {
        $user = $event->user;
        $user->assignRole(User::ROLE_DONOR);

        Log::info('User registered', [
            'user_id' => $user->id,
            'email' => $user->email,
        ]);
    }
}
