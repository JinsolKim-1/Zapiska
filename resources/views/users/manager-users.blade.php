<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Team | Zapiska</title>
    @vite(['resources/css/users.css'])
</head>
<body>
@include('users.includes.mainsidebar')

<main class="main-content">
    <header class="dashboard-header">
        <h1>My Department</h1>
        <div class="user-info">
            <div class="user-details">
                <div class="user-name">{{ Auth::user()->username }}</div>
                <span class="role">{{ Auth::user()->role->role_name }}</span>
            </div>
            <div class="avatar"><i class="bx bxs-user"></i></div>
        </div>
    </header>

    @if($sector)
        <section class="department-card">
            <h2>{{ $sector->department_name }}</h2>
            <div class="manager-info">
                <strong>Manager:</strong> {{ Auth::user()->username }}
            </div>

            <div class="users-list">
                @forelse($employees as $employee)
                    <div class="user-item">
                        {{ $employee->username }} ({{ $employee->role->role_name ?? 'No role' }})
                    </div>
                @empty
                    <p>No employees found in this department.</p>
                @endforelse
            </div>
        </section>
    @else
        <p style="margin-top:2rem;">You are not currently assigned to any department.</p>
    @endif
</main>

</body>
</html>
