<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add User to {{ $sector->department_name }} | Zapiska</title>
    @vite(['resources/css/addUser.css'])
</head>
<body>
@include('users.includes.mainsidebar')

<div class="sector-users-container">
    <div class="header">
        <h2>Add Users to <span>{{ $sector->department_name }}</span></h2>
        <a href="{{ route('users.sector.users', $sector->sector_id) }}" class="back-btn">← Back</a>
    </div>

    @if(session('success'))
        <div class="alert success">{{ session('success') }}</div>
    @endif

    @if($availableUsers->isEmpty())
        <p class="no-users">No available users to assign.</p>
    @else
        <table class="user-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Assign Role</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($availableUsers as $user)
                    <tr>
                        <td>{{ trim($user->firstname . ' ' . $user->lastname) ?: 'N/A' }}</td>
                        <td>{{ $user->username }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            <form action="{{ route('users.assignUserToSector', ['sector' => $sector->sector_id, 'user' => $user->user_id]) }}" method="POST">
                                @csrf
                                <select name="role_id" required>
                                    <option value="">Select Role</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->role_id }}">{{ $role->role_name }}</option>
                                    @endforeach
                                </select>
                        </td>
                        <td>
                                <button type="submit" class="assign-btn">Assign</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

</div>
</body>
</html>
