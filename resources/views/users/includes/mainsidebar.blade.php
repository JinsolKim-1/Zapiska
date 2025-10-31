
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sidebar | Zapiska</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    @vite(['resources/css/mainsidebar.css'])
</head>
<body>
    <div class="sidebar">

        @php
            $companyName = Auth::user()->company->company_name ?? 'Zapiska';
        @endphp
        
        <h2>{{ $companyName }}</h2>


        {{-- 🔹 Check user role --}}
        @php
            $role = Auth::user()->role ? Auth::user()->role->category : 'guest';
        @endphp


        {{-- 🔸 ADMIN MENU --}}
        @if ($role === 'admin')
            <h3 class="sidebar-section">ADMIN</h3>
            <a href="{{ route('users.dashboard') }}" class="{{ request()->is('users/dashboard') ? 'active' : '' }}">
                <i class="bx bx-bar-chart"></i> Analytics
            </a>
           <a href="{{ route('users.departments') }}" class="{{ request()->is('users/departments') ? 'active' : '' }}">
                <i class="bx bx-building"></i> Departments
            </a>
            <a href="{{ route('users.assets') }}" class="{{ request()->is('usersassets') ? 'active' : '' }}">
                <i class='bx bx-package'></i> Assets
            </a>
            <a href="{{ route('users.inventory.index') }}" class="{{ request()->is('users/inventory') ? 'active' : '' }}">
                <i class="bx bx-box"></i> Inventory
            </a>
            <a href="{{ route('users.requests') }}" class="{{ request()->is('users/requests') ? 'active' : '' }}">
                <i class="bx bx-notepad"></i> Requests
            </a>
            <a href="{{ route('users.receipts') }}" class="{{ request()->is('users/receipts') ? 'active' : '' }}">
                <i class="bx bx-receipt"></i> Receipts
            </a>
            <a href="{{ route('users.users') }}" class="{{ request()->is('users/users') ? 'active' : '' }}">
                <i class="bx bx-user"></i> Users
            </a>
            <a href="{{ route('users.invite') }}" class="{{ request()->is('users/invite') ? 'active' : '' }}">
                <i class="bx bx-envelope"></i> Invite
            </a>

            <div class="sidebar-divider"></div>

            <a href="{{ route('users.settings') }}" class="{{ request()->is('users/settings') ? 'active' : '' }}">
                <i class="bx bx-cog"></i> Settings
            </a>
            <a href="{{ route('logout') }}"
            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="bx bx-log-out"></i> Sign Out
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        @endif

        {{-- 🔸 MANAGER MENU --}}
        @if ($role === 'manager')
            <h3 class="sidebar-section">MANAGER</h3>
            <a href="{{ route('manager.analytics') }}" class="{{ request()->is('manager/analytics') ? 'active' : '' }}">
                <i class="bx bx-bar-chart"></i> Analytics
            </a>

            <a href="{{ route('manager.assets') }}" class="{{ request()->is('manager/assets') ? 'active' : '' }}">
                <i class='bx bx-package'></i> Assets
            </a>

            <a href="{{ route('manager.inventory') }}" class="{{ request()->is('manager/inventory') ? 'active' : '' }}">
                <i class="bx bx-box"></i> Inventory
            </a>

            <a href="{{ route('manager.requests') }}" class="{{ request()->is('manager/requests') ? 'active' : '' }}">
                <i class="bx bx-notepad"></i> Requests
            </a>

            <a href="{{ route('manager.receipts') }}" class="{{ request()->is('manager/receipts') ? 'active' : '' }}">
                <i class="bx bx-receipt"></i> Receipts
            </a>

            <a href="{{ route('manager.users') }}" class="{{ request()->is('manager/users') ? 'active' : '' }}">
                <i class="bx bx-user"></i> My Team
            </a>

            <div class="sidebar-divider"></div>

            <a href="{{ route('logout') }}"
            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="bx bx-log-out"></i> Sign Out
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        @endif

        {{-- 🔸 EMPLOYEE MENU --}}
        @if ($role === 'employee')
            <h3 class="sidebar-section">EMPLOYEE</h3>

            <a href="{{ route('employee.dashboard') }}" class="{{ request()->is('employee/dashboard') ? 'active' : '' }}">
                <i class="bx bx-bar-chart"></i> Analytics
            </a>

            <a href="{{ route('employee.assets') }}" class="{{ request()->is('employee/assets') ? 'active' : '' }}">
                <i class='bx bx-package'></i> Assets
            </a>

            <a href="{{ route('employee.inventory') }}" class="{{ request()->is('employee/inventory') ? 'active' : '' }}">
                <i class="bx bx-box"></i> Inventory
            </a>

            <a href="{{ route('employee.myRequests') }}" class="{{ request()->is('employee/my-requests') ? 'active' : '' }}">
                <i class="bx bx-notepad"></i> My Requests
            </a>

            <a href="{{ route('employee.receipts') }}" class="{{ request()->is('employee/receipts') ? 'active' : '' }}">
                <i class="bx bx-receipt"></i> Receipts
            </a>

            <div class="sidebar-divider"></div>

            <a href="{{ route('logout') }}"
            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="bx bx-log-out"></i> Sign Out
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        @endif
    </div>
</body>
</html>