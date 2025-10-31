<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    @vite(['resources/css/app.css'])
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
        <form action="{{ route('password.email') }}" method="POST">
            @csrf
            <h1>Reset Password</h1>
            <p class="welcome-text">Enter your email to receive a reset link</p>

            <div class="input-box @error('email') has-error @enderror">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" placeholder="Enter your email" value="{{ old('email') }}" 
                autocomplete="off" required>
                <i class='bx bx-envelope'></i>
                @error('email')<small class="error-text">{{ $message }}</small>@enderror
            </div>

            @if(session('success'))
                <div class="text-green-400 text-sm mb-2">
                    {{ session('success') }}
                </div>
            @endif

            <button type="submit" class="btn">Send Reset Link</button>

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
