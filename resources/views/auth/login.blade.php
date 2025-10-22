<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In</title>
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
            <form action="{{ route('login.post') }}" method="POST">
                @csrf
                <h1>Welcome Back!</h1>
                <p class="welcome-text">Sign in to manage your assets and requisitions</p>

                {{-- USERNAME --}}
                <div class="input-box @error('username_login') has-error @enderror">
                    <label for="username_login">Username</label>
                    <input 
                        type="text" 
                        id="username_login"
                        name="username_login" 
                        value="{{ old('username_login') }}" 
                        placeholder="Enter your username" 
                        required
                    >
                    <i class='bx bxs-user'></i>
                    @error('username_login')
                        <small class="error-text">{{ $message }}</small>
                    @enderror
                </div>

                <div class="input-box @error('password_login') has-error @enderror">
                    <label for="password_login">Password</label>
                    <input 
                        type="password" 
                        id="password_login"
                        name="password_login" 
                        placeholder="Enter your password" 
                        required
                    >
                    <button type="button" class="toggle-password">
                        <i class='bx bx-show'></i>
                    </button>
                    <i class='bx bx-lock-alt'></i>
                    @error('password_login')
                        <small class="error-text">{{ $message }}</small>
                    @enderror
                </div>
                <div class="remember-forgot">
                    <label class="remember-label">
                        <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                        <span>Remember Me</span>
                    </label>
                    <a href="{{route('password.request')}}" class="forgot-pass">Forgot Password?</a>
                </div>

                <button type="submit" class="btn">Sign In</button>

                <p class="switch-text">
                    Don’t have an account? <a href="{{ route('register') }}">Sign Up</a>
                </p>
            </form>
            <a href="{{ route('home') }}" class="back-home-btn">
                <i class='bx bx-left-arrow-alt'></i> Back to Home
            </a>
        </div>
    </div>
</body>
</html>
