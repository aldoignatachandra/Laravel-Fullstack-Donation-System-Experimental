<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSuperAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Allow access to login page for everyone (including non-logged in users)
        if ($request->is('admin/login') || $request->is('admin')) {
            return $next($request);
        }

        // Only allow super_admin role for other admin pages
        if (! $user || ! $user->hasRole(User::ROLE_SUPER_ADMIN)) {
            // Redirect to home page
            return redirect()->route('home');
        }

        return $next($request);
    }
}
