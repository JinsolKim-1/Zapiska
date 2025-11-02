<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Department | Zapiska</title>
  @vite(['resources/css/manager-users.css'])
</head>
<body>
  @include('users.includes.mainsidebar')

  <main class="main-content">
    <header class="dashboard-header">
      <h1>MY DEPARTMENT
        @if($sector)
          <span style="font-weight:600; margin-left:12px; font-size:0.9rem; color:#374151;">| {{ $sector->department_name }}</span>
        @endif
      </h1>

      <div class="user-info">
        <div class="user-details">
          <div class="user-name">{{ Auth::user()->username }}</div>
          <span class="role">{{ Auth::user()->role->role_name }}</span>
        </div>
        <div class="avatar"><i class="bx bxs-user"></i></div>
      </div>
    </header>

    <section style="margin-top:1.5rem;">
      @if(!$sector)
        <div class="card">
          <p>You are not assigned as manager of any department yet.</p>
        </div>
      @else
        <div class="card">
          <h3 style="margin-bottom:0.75rem;">Department: {{ $sector->department_name }}</h3>
          <p style="margin-bottom:1rem;"><strong>Manager:</strong> {{ Auth::user()->username }}</p>

          <div class="table-wrapper">
            <table class="styled-table" style="width:100%; border-collapse:collapse;">
              <thead>
                <tr style="text-align:left; border-bottom:1px solid #e5e7eb;">
                  <th>Registered</th>
                  <th>Name</th>
                  <th>Username</th>
                  <th>Role</th>
                  <th>Email</th>
                  <th>Contact</th>
                </tr>
              </thead>
              <tbody>
                @forelse($employees as $employee)
                  <tr>
                    <td>{{ $employee->created_at ? $employee->created_at->format('Y-m-d') : '—' }}</td>
                    <td>
                      {{ ($employee->firstname || $employee->lastname) 
                          ? trim($employee->firstname . ' ' . $employee->lastname)
                          : $employee->username }}
                    </td>
                    <td>{{ $employee->username }}</td>
                    <td>{{ $employee->role->role_name ?? 'No role' }}</td>
                    <td>{{ $employee->email }}</td>
                    <td>{{ $employee->contact ?? 'N/A' }}</td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="6">No employees found in this department.</td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      @endif
    </section>
  </main>
</body>
</html>
