<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zapiska</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel="stylesheet">
    @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body>
    <!-- <div class="">
        <h1>Zapiska</h1>
    </div> -->
    
    <div class="container">
        <div class="form-box register">
            <form action="">
                <h1>CREATE ACCOUNT</h1>
                <div class="input-box">
                    <label for="Username">Username</label>
                    <input type="text" placeholder="Username" required>
                    <i class='bx bxs-user'></i>
                </div>

                <div class="input-box">
                    <label for="email">email</label>
                    <input type="email" placeholder="Email" required>
                    <i class='bx bx-envelope' ></i>
                </div>

                <div class="input-box">
                    <label for="password">password</label>
                    <input type="password" placeholder="Password" required>
                    <i class='bx bx-lock-alt' ></i>
                </div>

                <div class="input-box">
                    <label for="confirmation">confirm password</label>
                    <input type="password" placeholder="Confirm Password" required>
                    <i class='bx bx-lock'></i>
                </div>

                <button type="submit" class="btn">Register</button>
            </form>
        </div>
        
        <div class="form-box login">
            <form action="">
                <h1>Sign In</h1>
                <div class="input-box">
                    <label for="Username">Username</label>
                    <input type="text" placeholder="Username" required>
                    <i class='bx bxs-user'></i>
                </div>

                <div class="input-box">
                    <label for="password">password</label>
                    <input type="password" placeholder="Password" required>
                    <i class='bx bx-lock-alt' ></i>
                </div>
                <div class="forgot-pass">
                    <a href="">Forgot Password?</a>
                </div>
                <button type="submit" class="btn">Login</button>

            </form>
        </div>
        
        <div class="toggle-box">
            <div class="toggle-panel toggle-left">
                <h1>Welcome Back!</h1>
                <p>Already have an account? <br>
                Log In to manage your assets and requisitions</p>
                <button class="btn login-btn">Sign In</button>
            </div>

            <div class="toggle-panel toggle-right">
                <h1>Welcome!</h1>
                <p>Don't have an account? <br>
                Sign Up to start managing your assets and requisitions</p>
                <button class="btn register-btn">Sign Up</button>
            </div>
        </div>
    </div>
</body>
</html>