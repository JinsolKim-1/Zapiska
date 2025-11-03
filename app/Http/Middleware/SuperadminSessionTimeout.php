<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class SuperadminSessionTimeout
{
    protected $timeout = 450;

    public function handle($request, Closure $next)
    {
        if (Auth::guard('superadmin')->check()) {

            $lastActivity = Session::get('superadmin_last_activity_time', now()->timestamp);

            if (now()->timestamp - $lastActivity > $this->timeout) {
                Auth::guard('superadmin')->logout();
                Session::forget('superadmin_last_activity_time');
                return redirect()->route('superadmin.login')
                                 ->with('message', 'You have been logged out due to inactivity.');
            }

            Session::put('superadmin_last_activity_time', now()->timestamp);
        }

        return $next($request);
    }
}
