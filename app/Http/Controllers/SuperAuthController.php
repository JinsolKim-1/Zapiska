<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use App\Models\SuperAdmin;

class SuperAuthController extends Controller
{
    protected $maxAttempts = 5;
    protected $decayMinutes = 15;

    public function showLoginForm()
    {
        return view('superadmin.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'super_email' => 'required|email',
            'super_password' => 'required|string',
        ]);

        $email = (string) $request->input('super_email');
        $key = $this->throttleKey($request);

        if (RateLimiter::tooManyAttempts($key, $this->maxAttempts)) {
            throw ValidationException::withMessages([
                'super_email' => ["Too many login attempts. Try again later"],
            ])->status(429);
        }

        $super = SuperAdmin::where('super_email', $email)->first();

        if (! $super || ! Hash::check($request->input('super_password'), $super->super_password)) {
            RateLimiter::hit($key, $this->decayMinutes * 60);
            throw ValidationException::withMessages([
                'super_email' => ['Invalid credentials.'],
            ]);
        }

        if ($super->status !== 'active') {
            throw ValidationException::withMessages([
                'super_email' => ['Account inactive. Contact support.'],
            ]);
        }

        RateLimiter::clear($key);
        Auth::guard('superadmin')->login($super, true);
        $request->session()->regenerate();

        return redirect()->intended(route('superadmin.dashboard'));
    }

    public function logout(Request $request)
    {
        Auth::guard('superadmin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('superadmin.login');
    }

    protected function throttleKey(Request $request)
    {
        return 'superadmin-login|' . sha1($request->input('super_email') . '|' . $request->ip());
    }
}
