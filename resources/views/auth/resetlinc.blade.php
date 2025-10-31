<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Set New Password</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
<div class="container">
    <div class="background-animations">
        <div class="blob blob1"></div>
        <div class="blob blob2"></div>
        <div class="particle particle1"></div>
        <div class="particle particle2"></div>
        <div class="particle particle3"></div>
        <div class="particle particle4"></div>
    </div>

    <div class="form-box login">
        <form action="{{ route('password.update') }}" method="POST">
            @csrf
            <h1>Set New Password</h1>
            <p class="welcome-text">Choose a strong password for your account</p>

            <!-- Hidden token from email link -->
            <input type="hidden" name="token" value="{{ $token }}">

            <!-- Email -->
            <div class="input-box @error('email') has-error @enderror">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" value="{{ old('email', $request->email ?? '') }}" 
                autocomplete="off" required>
                <i class='bx bx-envelope'></i>
                @error('email')<small class="error-text">{{ $message }}</small>@enderror
            </div>

            <!-- Password -->
            <div class="input-box @error('password') has-error @enderror">
                <label for="password">New Password</label>
                <input type="password" name="password" id="password" placeholder="Enter new password" required>
                <button type="button" class="toggle-password">
                    <i class='bx bx-show'></i>
                </button>
                <i class='bx bx-lock-alt'></i>
                @error('password')<small class="error-text">{{ $message }}</small>@enderror
            </div>

            <div class="password-strength">
                <div class="strength-bar"></div>
                <small class="strength-text"></small>
            </div>
            <!-- Password Confirmation -->
            <div class="input-box @error('password_confirmation') has-error @enderror">
                <label for="password_confirmation">Confirm Password</label>
                <input type="password" name="password_confirmation" id="password_confirmation" placeholder="Confirm new password" required>
                <button type="button" class="toggle-password">
                    <i class='bx bx-show'></i>
                </button>
                <i class='bx bx-lock-alt'></i>
                @error('password_confirmation')<small class="error-text">{{ $message }}</small>@enderror
            </div>

            <button type="submit" class="btn">Reset Password</button>

            <p class="switch-text mt-4">
                Remembered your password? <a href="{{ route('login') }}">Sign In</a>
            </p>
        </form>

        <a href="{{ route('home') }}" class="back-home-btn">
            <i class='bx bx-left-arrow-alt'></i> Back to Home
        </a>
    </div>
</div>
</body>
</html>
