<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complete Your Profile</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script>
        window.deleteTempUserUrl = "{{ route('delete.temp.user') }}";
        window.csrfToken = "{{ csrf_token() }}";
    </script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/post-verification.css', 'resources/js/delete_temp.js'])
</head>
<body>
    <div class="container">
        <!-- Background animations -->
        <div class="background-animations">
            <div class="blob blob1"></div>
            <div class="blob blob2"></div>
            <div class="particle particle1"></div>
            <div class="particle particle2"></div>
            <div class="particle particle3"></div>
            <div class="particle particle4"></div>
        </div>

        <div class="form-box register">
            <form action="{{ route('post.verification.post') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <h1>Complete Your Profile</h1>
                <p class="welcome-text">Finish setting up your account</p>

                <!-- First Name -->
                <div class="input-box @error('firstname') has-error @enderror">
                    <label for="firstname">First Name</label>
                    <input type="text" id="firstname" name="firstname" placeholder="Enter your first name" value="{{ old('firstname') }}" required>
                    <i class='bx bxs-user'></i>
                    @error('firstname')<small class="error-text">{{ $message }}</small>@enderror
                </div>

                <!-- Last Name -->
                <div class="input-box @error('lastname') has-error @enderror">
                    <label for="lastname">Last Name</label>
                    <input type="text" id="lastname" name="lastname" placeholder="Enter your last name" value="{{ old('lastname') }}" required>
                    <i class='bx bxs-user'></i>
                    @error('lastname')<small class="error-text">{{ $message }}</small>@enderror
                </div>

                <!-- Contact -->
                <div class="input-box @error('contact') has-error @enderror">
                    <label for="contact">Contact</label>
                    <input type="text" id="contact" name="contact" placeholder="Enter your contact number" value="{{ old('contact') }}" required>
                    <i class='bx bx-phone'></i>
                    @error('contact')<small class="error-text">{{ $message }}</small>@enderror
                </div>

                <!-- Profile Image -->
                <div class="input-box @error('profile') has-error @enderror">
                    <label for="profile">Profile Image</label>
                    <input type="file" id="profile" name="profile" accept="image/*" required>
                    <i class='bx bx-image'></i>
                    @error('profile')<small class="error-text">{{ $message }}</small>@enderror
                </div>

                <!-- Verification Code -->
                <div class="input-box @error('verification_code') has-error @enderror">
                    <label for="verification_code">Verification Code</label>
                    <input type="text" id="verification_code" name="verification_code" placeholder="Enter the code sent to your email" value="{{ old('verification_code') }}" required>
                    <i class='bx bx-key'></i>
                    @error('verification_code')<small class="error-text">{{ $message }}</small>@enderror
                </div>

                <button type="submit" class="btn">Verify & Complete</button>
            </form>

            <!-- Resend Code -->
            <div class="mt-2">
                <button type="button" id="resend-btn" class="btn btn-secondary">
                    Resend Verification Code
                </button>
                <div id="resend-message" class="text-sm mt-2"></div>
            </div>

            <a href="{{ route('home') }}" id="back-home" class="back-home-btn">
                <i class='bx bx-left-arrow-alt'></i> Back to Home
            </a>
        </div>
    </div>
</body>
</html>
