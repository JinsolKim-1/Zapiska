<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ZAPISKA | Home</title>
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

  @vite(['resources/css/home.css'])
</head>
<body>
  <header class="navbar">
    <div class="navbar-container">
      <div class="navbar-left">
        <a href="{{route('home')}}" class="navbar-logo">ZAPISKA<span>.</span></a>
      </div>

      <ul class="navbar-center">
        <li><a href="#">Features</a></li>
        <li><a href="#">Pricing</a></li>
        <li><a href="#">Contact</a></li>
      </ul>

      <div class="navbar-right">
            <a href="{{ route('register') }}" class="btn-get-started">
                Get Started <i class='bx bx-chevron-right'></i>
            </a>
      </div>
    </div>
  </header>

  <main class="home">
    <div class="home-overlay"></div>
        <div class="home-content">
            <h1>Asset Management <br><span>with Pre-Requisition System</span></h1>
            <p>
                Streamline asset tracking and simplify purchase requests with an efficient,
                transparent, and accountable solution for modern organizations.
            </p>
            <div class="home-buttons">
                <a href="{{ route('register') }}" class="btn-primary">Get Started</a>
                <a href="{{route('login')}}" class="btn-secondary">Log In</a>
            </div>
        </div>
  </main>
</body>
</html>
