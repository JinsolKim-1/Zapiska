
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
        <h2>Zapiska</h2>

        {{-- 🔹 Check user role --}}
        @php
            $role = Auth::user()->usertype ?? 'guest';
        @endphp

        {{-- 🔸 ADMIN MENU --}}
        @if ($role === 'admin')
            <h3 class="sidebar-section">ADMIN</h3>
            <a href="{{ route('admin.analytics') }}" class="{{ request()->is('admin/analytics') ? 'active' : '' }}">
                <i class="bx bx-bar-chart"></i> Analytics
            </a>
            <a href="{{ route('admin.departments') }}" class="{{ request()->is('admin/departments') ? 'active' : '' }}">
                <i class="bx bx-building"></i> Departments
            </a>
            <a href="{{ route('admin.assets') }}" class="{{ request()->is('admin/assets') ? 'active' : '' }}">
                <i class="bx bx-box"></i> Assets
            </a>
            <a href="{{ route('admin.requests') }}" class="{{ request()->is('admin/requests') ? 'active' : '' }}">
                <i class="bx bx-notepad"></i> Requests
            </a>
            <a href="{{ route('admin.receipts') }}" class="{{ request()->is('admin/receipts') ? 'active' : '' }}">
                <i class="bx bx-receipt"></i> Receipts
            </a>
            <a href="{{ route('admin.users') }}" class="{{ request()->is('admin/users') ? 'active' : '' }}">
                <i class="bx bx-user"></i> Users
            </a>

            <div class="sidebar-divider"></div>

            <a href="{{ route('admin.settings') }}" class="{{ request()->is('admin/settings') ? 'active' : '' }}">
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
                <i class="bx bx-box"></i> Assets
            </a>
            <a href="{{ route('manager.requests') }}" class="{{ request()->is('manager/requests') ? 'active' : '' }}">
                <i class="bx bx-notepad"></i> Requests
            </a>
            <a href="{{ route('manager.receipts') }}" class="{{ request()->is('manager/receipts') ? 'active' : '' }}">
                <i class="bx bx-receipt"></i> Receipts
            </a>
            <a href="{{ route('manager.users') }}" class="{{ request()->is('manager/users') ? 'active' : '' }}">
                <i class="bx bx-user"></i> Users
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
            <a href="{{ route('employee.analytics') }}" class="{{ request()->is('employee/analytics') ? 'active' : '' }}">
                <i class="bx bx-bar-chart"></i> Analytics
            </a>
            <a href="{{ route('employee.assets') }}" class="{{ request()->is('employee/assets') ? 'active' : '' }}">
                <i class="bx bx-box"></i> Assets
            </a>
            <a href="{{ route('employee.requests') }}" class="{{ request()->is('employee/requests') ? 'active' : '' }}">
                <i class="bx bx-notepad"></i> Requests
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