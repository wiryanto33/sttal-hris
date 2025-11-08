<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureUserIsActive
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        $status = optional($user?->userDetail)->status;
        if ($user && !$user->hasRole('superadmin') && $status !== 'active') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('login')->withErrors([
                'email' => __('Akun Anda belum aktif. Silakan hubungi administrator.'),
            ]);
        }

        return $next($request);
    }
}
