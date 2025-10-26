<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sidebar | Zapiska</title>
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    @vite(['resources/css/sidebar.css'])
</head>
<body>
    <div class="sidebar">
        <h2>Zapiska</h2>

        <a href="{{ route('superadmin.dashboard') }}" 
           class="{{ request()->is('superadmin/dashboard') ? 'active' : '' }}">
            <i class="bx bx-home"></i> Dashboard
        </a>

        <a href="{{ route('superadmin.users') }}" 
           class="{{ request()->is('superadmin/users') ? 'active' : '' }}">
            <i class="bx bx-user"></i> Users
        </a>

        <a href="{{ route('superadmin.companies') }}" 
            class="{{ request()->is('superadmin/companies') ? 'active' : '' }}">
            <i class="bx bx-buildings"></i> Companies
        </a>

        <form method="POST" action="{{ route('superadmin.logout') }}">
            @csrf
            <button type="submit" class="logout">
                <i class="bx bx-log-out"></i> Logout
            </button>
        </form>
    </div>
</body>
</html>
