<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />

  <title>Login | Zapiska</title>

  {{-- Security Headers --}}
  <meta http-equiv="Content-Security-Policy" content="
      default-src 'self';
      script-src 'self' https://cdn.jsdelivr.net;
      style-src 'self' 'unsafe-inline' https://fonts.googleapis.com;
      font-src 'self' https://fonts.gstatic.com;
      img-src 'self' data:;
      object-src 'none';
      base-uri 'self';
      form-action 'self';
  ">
  <meta http-equiv="Referrer-Policy" content="no-referrer" />
  <meta http-equiv="X-Content-Type-Options" content="nosniff" />
  <meta http-equiv="X-Frame-Options" content="DENY" />

  @vite(['resources/css/login.css'])
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@500;700&family=Inter:wght@400;500;700&display=swap" rel="stylesheet" />

  {{-- Prevent autocomplete on sensitive fields --}}
  <meta name="robots" content="noindex, nofollow">
</head>

<body>
  <noscript>
    <div class="noscript-warning">
      JavaScript is disabled in your browser. For security, please enable it to continue.
    </div>
  </noscript>

  <div class="superadmin-login-container">
    <div class="login-card" role="main" aria-labelledby="loginTitle">
      <div class="login-header">
        <h1 id="loginTitle">Admin Access</h1>
        <p>Secure Portal</p>
      </div>

      <form method="POST" action="{{ route('superadmin.login.post') }}" autocomplete="off" novalidate>
        @csrf

        <div class="input-group">
          <label for="super_email">Email Address</label>
          <input
            type="email"
            id="super_email"
            name="super_email"
            inputmode="email"
            spellcheck="false"
            required
            autocomplete="off"
            placeholder="mail@example.com"
          />
        </div>

        <div class="input-group">
          <label for="super_password">Password</label>
          <input
            type="password"
            id="super_password"
            name="super_password"
            required
            minlength="8"
            autocomplete="new-password"
            placeholder="••••••••"
          />
        </div>

        <button type="submit" class="login-btn" aria-label="Sign In">Sign In</button>

        <div class="footer-text">
          <small>⚠ Authorized personnel only — all access attempts are logged.</small>
        </div>
      </form>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/disable-autofill@1.1.1/dist/disable-autofill.min.js" defer></script>
</body>
</html>
