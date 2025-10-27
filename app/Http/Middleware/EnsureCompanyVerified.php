<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureCompanyVerified
{

    public function handle(Request $request, Closure $next)
    {
        if ($request->query()) {
            return redirect($request->url());
        }

        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login')->with('error', 'Please log in first.');
        }

        if (!$user->company_id || !$user->company) {
            return redirect()->route('welcmain')->with('error', 'You have no registered company.');
        }

        $company = $user->company;

        if ($company->verification_status !== 'verified') {
            return redirect()->route('welcmain')->with('error', 'Your company is still pending approval.');
        }

        return $next($request);
    }
}
