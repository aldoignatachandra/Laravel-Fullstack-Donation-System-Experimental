<?php

namespace App\Filament\Pages\Auth;

use App\Models\User;
use Filament\Auth\Http\Responses\Contracts\LoginResponse;
use Filament\Auth\Pages\Login as BaseLogin;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Validation\ValidationException;

class Login extends BaseLogin
{
    public function getHeading(): string|Htmlable
    {
        return 'Admin Panel';
    }

    public function authenticate(): ?LoginResponse
    {
        // Call parent authenticate
        $response = parent::authenticate();

        // Check if user has super_admin role after successful login
        $user = auth()->user();
        if ($user && ! $user->hasRole(User::ROLE_SUPER_ADMIN)) {
            // Logout the user (but don't invalidate session to preserve CSRF token)
            auth()->logout();

            // Throw validation error
            throw ValidationException::withMessages([
                'data.email' => 'Anda tidak memiliki akses ke panel admin.',
            ]);
        }

        return $response;
    }
}
