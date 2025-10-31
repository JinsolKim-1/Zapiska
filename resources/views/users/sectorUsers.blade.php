<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $sector->department_name }} | Zapiska</title>
    @vite(['resources/css/sectorUsers.css'])
</head>
<body>
    @include('users.includes.mainsidebar')

    <main class="main-content">
        <!-- Header -->
        <header class="dashboard-header">
            <h1>{{ $sector->department_name }}</h1>
            <div class="manager-info-header">
                <span><strong>Manager:</strong> {{ $sector->manager ? $sector->manager->username : 'None assigned' }}</span>
                <a href="{{ route('users.editManager', $sector->sector_id) }}" class="edit-manager-btn">Edit Manager</a>
            </div>
        </header>

        <!-- Add User Button -->
        <div class="add-user-container">
            <a href="{{ route('users.addUserForm', $sector->sector_id) }}" class="add-user-btn">+ Add User</a>
        </div>

        <!-- Users Table -->
        <section class="users-table-section">
            <table class="users-table">
                <thead>
                    <tr>
                        <th>Register Date</th>
                        <th>Name</th>
                        <th>Username</th>
                        <th>Contact Number</th>
                        <th>Role</th>
                        <th>Email</th>
                        <th>Kick Out</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sector->users as $user)
                    <tr>
                        <td>{{ $user->created_at->format('Y-m-d') }}</td>
                        <td>
                            {{ $user->firstname || $user->lastname 
                                ? $user->firstname . ' ' . $user->lastname 
                                : $user->username }}
                        </td>
                        <td>{{ $user->username }}</td>
                        <td>{{ $user->contact ?? 'N/A' }}</td>
                        <td>{{ $user->role ? $user->role->role_name : 'No role' }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            <form action="{{ route('users.kickUser', $user->user_id) }}" method="POST" onsubmit="return confirm('Are you sure you want to kick this user?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="kick-btn">×</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </section>
    </main>
</body>
</html>
