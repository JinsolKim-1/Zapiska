<?php

namespace App\Http\Controllers;

use Intervention\Image\Facades\Image;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\EmailVerification;
use App\Models\LoginAttempt;
use App\Models\PasswordReset;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerificationCodeMail;
use App\Mail\ResetPasswordMail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Storage;

class AuthController extends Controller
{
    private int $resetTokenExpiry = 10; // minutes
    private int $verificationCodeExpiry = 3; // minutes
    private int $maxLoginAttempts = 5; // per minute

    // 🔹 Show login form
    public function showLoginForm() {
        return view('auth.login');
    }

    // 🔹 Show registration form
    public function showRegisterForm() {
        return view('auth.register');
    }

    // 🔹 Show forgot password form
    public function showForgotPasswordForm() {
        return view('auth.passresetver');
    }

    // 🔹 Send password reset link
    public function sendResetLink(Request $request) {
        $request->validate(['email' => 'required|email|exists:users,email']);

        $user = User::where('email', $request->email)->first();

        $plainToken = Str::random(60);
        $hashedToken = Hash::make($plainToken);

        // Save/update token
        PasswordReset::updateOrCreate(
            ['email' => $user->email],
            ['res_token' => $hashedToken, 'res_created_at' => now()]
        );

        $resetLink = url('/password/reset/' . $plainToken . '?email=' . urlencode($user->email));

        // Queue email to prevent blocking
        Mail::to($user->email)->send(new ResetPasswordMail($user, $resetLink));

        return back()->with('success', 'Reset link sent to your email.');
    }

    // 🔹 Show reset password form
    public function showResetForm(Request $request, $token) {
        $email = $request->query('email');
        return view('auth.resetlinc', ['token' => $token, 'email' => $email]);
    }

    // 🔹 Handle password reset
    public function resetPassword(Request $request) {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'token' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $reset = PasswordReset::where('email', $request->email)->first();

        if (!$reset || !Hash::check($request->token, $reset->res_token)) {
            return back()->withErrors(['email' => 'Session expired']);
        }

        if ($reset->res_created_at->addMinutes($this->resetTokenExpiry)->lt(now())) {
            return back()->withErrors(['email' => 'Session expired']);
        }

        $user = User::where('email', $request->email)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        $reset->delete(); // Remove token after use

        return redirect()->route('login')->with('success', 'Password has been reset successfully!');
    }

    // 🔹 Handle registration
    public function register(Request $request) {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:100|unique:users,username',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => [
                'required', 'string', 'min:8',
                'regex:/^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]+$/',
                'confirmed'
            ],
        ], [
            'password.regex' => 'Password must contain at least one letter, one number, and one special character.'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'verification' => 'pending',
            'profile_complete' => 0,
        ]);

        Auth::login($user);
        
        if (session()->has('invite_token')) {
            $token = session('invite_token');
            $invite = \App\Models\Invitation::where('invite_token', $token)->first();

            if ($invite) {
                $user->company_id = $invite->company_id;
                $user->role_id = $invite->role_id;
                $user->save();

                $invite->update(['status' => 'approved']);
                session()->forget('invite_token');
            }
        }
        $this->sendVerificationCode($user);

        return redirect()->route('post.verification')
            ->with('success', 'Account created! Please complete your profile and verify your email.');
    }

    // 🔹 Handle login with rate limiting
    public function login(Request $request) {
        
        $request->validate([
            'username_login' => 'required|string',
            'password_login' => 'required|string',
        ]);

        $key = 'login-attempt:' . $request->ip();

        if (RateLimiter::tooManyAttempts($key, $this->maxLoginAttempts)) {
            $seconds = RateLimiter::availableIn($key);
            return back()->withErrors(['username_login' => "Too many attempts. Try again in {$seconds} seconds."]);
        }

        $credentials = [
            'username' => $request->username_login,
            'password' => $request->password_login,
        ];

        $loginSuccess = Auth::attempt($credentials, $request->filled('remember'));

        // Log every login attempt
        LoginAttempt::create([
            'username' => $request->username_login,
            'remote_ip' => $request->ip(),
            'success' => $loginSuccess ? 1 : 0,
        ]);

        RateLimiter::hit($key, 60); // limit per minute

        if (!$loginSuccess) {
            return back()->withErrors(['username_login' => 'Invalid username or password.'])->onlyInput('username_login');
        }

        // ✅ Regenerate session after successful login
        $request->session()->regenerate();
        $user = Auth::user();

        // ✅ If user is SuperAdmin
        if (isset($user->usertype) && $user->usertype === 'superadmin') {
            return redirect()->route('superadmin.dashboard');
        }

        // ✅ If user's email verification is pending
        if ($user->verification === 'pending') {
            return redirect()->route('post.verification')
                ->with('info', 'Please verify your email before accessing the system.');
        }

        // ✅ If user created a company
        if ($user->company) {
            // If company is verified → redirect to company dashboard
            if ($user->company->verification_status === 'verified') {
                return redirect()->route('company.dashboard', ['id' => $user->company->company_id]);
            }

            // If company still pending or rejected
            return redirect()->route('welcmain')
                ->with('error', 'Your company is still under review or not approved yet.');
        }

        // ✅ If user does not have a company
        return redirect()->route('welcmain');
    }

    // 🔹 Show post-verification form
    public function showPostVerificationForm() {
        return view('auth.verif');
    }

    // 🔹 Handle post-verification
    public function submitPostVerification(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'firstname' => 'required|string|max:100',
            'lastname' => 'required|string|max:100',
            'contact' => 'required|string|max:50',
            'profile' => 'required|image|mimes:jpeg,png,jpg|max:2048', // validated file type and size
            'verification_code' => 'required|string',
        ]);

        $verification = $user->latestVerification;

        if (!$verification || !Hash::check($request->verification_code, $verification->ver_code)) {
            return back()->withErrors(['verification_code' => 'Invalid verification code.']);
        }

        if ($verification->expire_at->lt(now())) {
            return back()->withErrors(['verification_code' => 'Invalid code']);
        }

        $verification->update(['verified_at' => now()]);

        // 🔹 Handle profile image securely
        $image = $request->file('profile');

        // Generate unique filename
        $filename = uniqid('profile_') . '.' . $image->getClientOriginalExtension();
        $request->profile->storeAs('profiles', $filename, 'private');
        $user->profile = $filename;

        $user->update([
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'contact' => $request->contact,
            'profile' => $filename,
            'verification' => 'verified',
            'profile_complete' => 1,
        ]);
        if ($user->company) {
            return redirect()->route('company.dashboard', ['id' => $user->company->company_id])
                ->with('success', 'Profile completed successfully!');
}

        return redirect()->route('welcmain')->with('success', 'Profile completed successfully!');
    }

    // 🔹 Resend verification code
    public function resendVerificationCode() {
        $user = Auth::user();
        if (!$user) return response()->json(['error' => 'User not authenticated.'], 401);

        $recentCode = EmailVerification::where('user_id', $user->user_id)
            ->where('expire_at', '>', now())
            ->latest()
            ->first();

        if ($recentCode) {
            $remaining = $recentCode->expire_at->timestamp - now()->timestamp;
            if ($remaining > 0) {
                return response()->json(['error' => "Please wait {$remaining} seconds before requesting a new code."]);
            }
        }

        try {
            $this->sendVerificationCode($user);
            return response()->json(['success' => 'A new verification code has been sent to your email.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to resend verification code.']);
        }
    }

    // 🔹 Delete temp user
    public function deleteTempUser() {
        $user = Auth::user();
        if ($user && !$user->profile_complete) {
            $user->delete();
            Auth::logout();
        }
        return response()->json(['success' => true]);
    }

    // 🔹 Logout
    public function logout(Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')->with('success', 'You have been logged out.');
    }

    // 🔹 Send verification code securely
    private function sendVerificationCode(User $user) {
        $plain = (string) random_int(100000, 999999);

        EmailVerification::create([
            'user_id' => $user->user_id,
            'ver_code' => Hash::make($plain),
            'expire_at' => now()->addMinutes($this->verificationCodeExpiry),
        ]);

        Mail::to($user->email)->send(new VerificationCodeMail($user, $plain));
    }

    public function create() {
        return view('company_register');
    }

}
