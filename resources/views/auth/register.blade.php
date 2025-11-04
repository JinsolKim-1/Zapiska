<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account</title>
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

        <div class="form-box register">
            <form action="{{ route('register.post') }}" method="POST">
                @csrf
                <h1>Join Us!</h1>
                <p class="welcome-text">Create your account to start managing your assets</p>

                <!-- Username -->
                <div class="input-box @error('username') has-error @enderror">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" value="{{ old('username') }}" placeholder="Enter your username" 
                    autocomplete="off" required>
                    <i class='bx bxs-user'></i>
                    @error('username')
                        <small class="error-text">{{ $message }}</small>
                    @enderror
                </div>

                <!-- Email -->
                <div class="input-box @error('email') has-error @enderror">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="Enter your email" 
                    autocomplete="off" required>
                    <i class='bx bx-envelope'></i>
                    @error('email')
                        <small class="error-text">{{ $message }}</small>
                    @enderror
                </div>

                <!-- Password -->
                <div class="input-box @error('password') has-error @enderror">
                    <label for="password">Password</label>
                    <div class="password-wrapper">
                        <input type="password" id="password" name="password" placeholder="Enter your password" required onpaste="return false;">
                        <i class='bx bx-lock-alt'></i>
                        <button type="button" class="toggle-password"><i class='bx bx-show'></i></button>
                    </div>
                    @error('password')
                        <small class="error-text">{{ $message }}</small>
                    @enderror
                </div>

                <!-- Password Strength -->
                <div class="password-strength">
                    <div class="strength-bar"></div>
                    <small class="strength-text"></small>
                </div>

                <ul class="password-checklist">
                    <li id="length" class="invalid">At least 8 characters</li>
                    <li id="upper" class="invalid">At least one uppercase letter</li>
                    <li id="lower" class="invalid">At least one lowercase letter</li>
                    <li id="number" class="invalid">At least one number</li>
                    <li id="special" class="invalid">At least one special character</li>
                </ul>

                <!-- Confirm Password -->
                <div class="input-box">
                    <label for="password_confirmation">Confirm Password</label>
                    <div class="password-wrapper">
                        <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Confirm your password" required onpaste="return false;">
                        <i class='bx bx-lock'></i>
                        <button type="button" class="toggle-password"><i class='bx bx-show'></i></button>
                    </div>
                        @error('password_confirmation')
                            <small class="error-text">{{ $message }}</small>
                        @enderror
                </div>

                <button type="submit" class="btn">Sign Up</button>

                <p class="switch-text">
                    Already have an account? <a href="{{ route('login') }}">Sign In</a>
                </p>
            </form>

            <a href="{{ route('home') }}" class="back-home-btn">
                <i class='bx bx-left-arrow-alt'></i> Back to Home
            </a>
        </div>
    </div>
</body>
</html>
