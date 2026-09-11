<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureActiveAccount
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return $next($request);
        }

        // Determine whether this user's account is in a usable state.
        // Trainers use 'approved' as their active status (not 'active').
        // Trainees and Admins use 'active'.
        $accountUsable = $user->isTrainer()
            ? $user->isApproved()          // trainer: status must be 'approved'
            : $user->isActive();           // trainee/admin: status must be 'active'

        if (! $accountUsable) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            $statusMessage = match ($user->status) {
                'pending'   => 'Your trainer account is awaiting admin approval. Please wait for the approval email.',
                'rejected'  => 'Your trainer registration request has been rejected. Please contact the administrator for further information.',
                'suspended' => 'Your account has been suspended. Please contact the administrator.',
                default     => 'Your account is not active. Please contact the administrator.',
            };

            return redirect()->route('login')->withErrors(['email' => $statusMessage]);
        }

        return $next($request);
    }
}
