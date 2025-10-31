{{-- resources/views/users/Maindashboard.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Main Dashboard</title>
  @vite(['resources/css/userdashboard.css'])
</head>
<body>
  <div class="layout">
    @include('users.includes.mainsidebar')

    {{-- Role-Based Dashboard Content --}}
    @if(Auth::user()->role->category === 'admin')
      @include('users.dashboards.admin-dashboard')
    @elseif(Auth::user()->role->category === 'manager')
      @include('users.dashboards.manager-dashboard')
    @elseif(Auth::user()->role->category === 'employee')
      @include('users.dashboards.employee-dashboard')
    @endif

  </div>
</body>
</html>
