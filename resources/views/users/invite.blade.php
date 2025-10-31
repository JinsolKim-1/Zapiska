<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invite User | Zapiska</title>
    @vite(['resources/css/invite.css'])
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
</head>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invite User | Zapiska</title>
    @vite(['resources/css/invite.css'])
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
</head>
<body>
    @include('users.includes.mainsidebar')
    <main class="main-content">
        <header class="dashboard-header">
            <div class="header-left">
                <h1>Invite Users</h1>
                <p class="header-subtitle">Invite new users to join your company by assigning them a role and category.</p>
            </div>

            <div class="user-info">
                <div class="user-details">
                    <div class="user-name">{{ Auth::user()->username }}</div>
                    <span class="role">{{ Auth::user()->role->role_name ?? 'No Role' }}</span>
                </div>
                <div class="avatar">
                    <i class="bx bxs-user"></i>
                </div>
            </div>
        </header>

        <main class="invite-section">
            <div class="invite-container">


                <form action="{{ route('users.sendInvite') }}" method="POST" class="invite-form">
                    @csrf

                    <div class="form-group">
                        <label for="email">Invitee Email</label>
                       <input type="email" name="email" id="email" placeholder="Enter user's email" autocomplete="off" required>
                    </div>

                    <div class="form-group">
                        <label for="category">Select Role Category</label>
                        <select name="category" id="category" required>
                            <option value="">-- Select Category --</option>
                            <option value="admin">admin</option>
                            <option value="manager">manager</option>
                            <option value="employee">employee</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="role_name">Role Name</label>
                        <input type="text" name="role_name" id="role_name" placeholder="e.g. Supplier Manager" autocomplete="off" required>
                    </div>

                    <button type="submit" class="invite-btn">Send Invitation</button>
                    
                @if(session('success'))
                    <div class="alert success">{{ session('success') }}</div>
                @elseif(session('error'))
                    <div class="alert error">{{ session('error') }}</div>
                @endif  
                </form>
            </div>
          
    </main>

        <script>
        document.addEventListener('DOMContentLoaded', () => {
            const alert = document.querySelector('.invite-container .alert');
            if(alert){
                // Trigger the show class after DOM load
                setTimeout(() => alert.classList.add('show'), 50);

                // Auto-hide after 4s
                setTimeout(() => alert.classList.remove('show'), 4000);
            }
        });
    </script>
</body>
</html>
