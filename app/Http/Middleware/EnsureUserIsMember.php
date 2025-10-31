<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureUserIsMember
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        // Check if user has a company and role
        if (!$user || !$user->company_id || !$user->role_id) {
            return redirect()->route('welcmain')->with('error', 'You are not authorized to access this page.');
        }

        return $next($request);
    }
}
