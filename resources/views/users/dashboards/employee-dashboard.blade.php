<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Employee | Dashboard</title>
  @vite(['resources/css/userdashboard.css'])
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
</head>
<body>
  <div class="layout">
    @include('users.includes.mainsidebar')

    <main class="main-content">
      <!-- Dashboard Header -->
      <header class="dashboard-header">
        <h1>Analytics</h1>
        <div class="user-info">
          <div class="user-details">
            <div class="user-name">{{ Auth::user()->username }}</div>
            <span class="role">{{ Auth::user()->role->role_name }}</span>
          </div>
          <div class="avatar"><i class="bx bxs-user"></i></div>
        </div>
      </header>

      <!-- Dashboard Grid -->
      <section class="dashboard-grid">
        <!-- Row 1 -->
        <div class="card blue"><h3>My Pending Requests</h3><p>{{ $pendingRequests ?? 0 }}</p></div>
        <div class="card green"><h3>Approved</h3><p>₱{{ number_format($approvedTotal ?? 0,2) }}</p></div>
        <div class="card cyan"><h3>Funds Received</h3><p>₱{{ number_format($receivedTotal ?? 0,2) }}</p></div>
        <div class="card yellow"><h3>Rejected</h3><p>₱{{ number_format($rejectedTotal ?? 0,2) }}</p></div>

        <!-- Row 2 -->
        <div class="card purple"><h3>Month Total</h3><p>₱{{ number_format($monthTotal ?? 0,2) }}</p></div>
        <div class="card red"><h3>Saved</h3><p>₱{{ number_format($savedFunds ?? 0,2) }}</p></div>
        <div class="card"><h3>Request Status</h3><canvas id="requestStatusChart"></canvas></div>
        <div class="card"><h3>Monthly Requests</h3><canvas id="monthlyRequestsChart"></canvas></div>

        <!-- Row 3 -->
        <div class="left-column">
          <div class="card wide"><h3>Overall Approved</h3><canvas id="overallApprovedChart" class="tall"></canvas></div>
          <div class="card wide"><h3>Overall Requests</h3><canvas id="overallRequestsChart" class="tall"></canvas></div>
        </div>

        <!-- Staff Table -->
        <div class="card staff-table">
          <div class="header-live">
            <h3>My Requests</h3>
            <div class="live"><div class="dot"></div>Live</div>
          </div>
          <div class="table-wrapper">
            <table>
              <thead>
                <tr><th>Request</th><th>Status</th><th>Date</th><th>Cost</th></tr>
              </thead>
              <tbody>
                @foreach($requests ?? [] as $request)
                <tr>
                  <td>{{ $request->asset_name }}</td>
                  <td><span class="status {{ strtolower($request->status) }}">{{ $request->status }}</span></td>
                  <td>{{ \Carbon\Carbon::parse($request->created_at)->format('M d') }}</td>
                  <td>₱{{ number_format($request->estimated_cost,2) }}</td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </section>
    </main>
  </div>

  <!-- Charts Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
    // Doughnut - Request Status
    new Chart(document.getElementById('requestStatusChart'), {
      type: 'doughnut',
      data: {
        labels: ['Pending','Approved','Rejected'],
        datasets: [{
          data: [{{ $pendingRequests ?? 0 }}, {{ $approvedRequests ?? 0 }}, {{ $rejectedRequests ?? 0 }}],
          backgroundColor: ['#3b82f6','#10b981','#ef4444']
        }]
      },
      options: { plugins: { legend: { position: 'bottom' } }, maintainAspectRatio: false }
    });

    // Bar - Monthly Requests
    new Chart(document.getElementById('monthlyRequestsChart'), {
      type: 'bar',
      data: {
        labels: ['Jan','Feb','Mar','Apr','May'],
        datasets: [{
          label: 'Requests',
          data: [5,2,3,4,1],
          backgroundColor: '#3b82f6',
          borderRadius: 6
        }]
      },
      options: { plugins: { legend: { display: false } }, maintainAspectRatio: false, scales: { y: { beginAtZero: true } } }
    });

    // Line - Overall Approved
    new Chart(document.getElementById('overallApprovedChart'), {
      type: 'line',
      data: {
        labels: ['Jan','Feb','Mar','Apr','May'],
        datasets: [{
          label: 'Approved Requests',
          data: [10,14,9,15,11],
          borderColor: '#10b981',
          backgroundColor: 'rgba(16,185,129,0.2)',
          tension: 0.4,
          fill: true
        }]
      },
      options: { plugins: { legend: { display: false } }, maintainAspectRatio: false, scales: { y: { beginAtZero: true } } }
    });

    // Line - Overall Requests
    new Chart(document.getElementById('overallRequestsChart'), {
      type: 'line',
      data: {
        labels: ['Jan','Feb','Mar','Apr','May'],
        datasets: [{
          label: 'Requests',
          data: [6,10,8,16,12],
          borderColor: '#3b82f6',
          backgroundColor: 'rgba(59,130,246,0.1)',
          tension: 0.4,
          fill: true
        }]
      },
      options: { plugins: { legend: { display: false } }, maintainAspectRatio: false, scales: { y: { beginAtZero: true } } }
    });
  </script>
</body>
</html>
