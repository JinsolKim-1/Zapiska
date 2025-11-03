<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manager | Analytics</title>
  @vite(['resources/css/userdashboard.css'])
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
</head>
<body>
  <div class="layout">
    @include('users.includes.mainsidebar')

    <main class="main-content">
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

      <section class="dashboard-grid">
        <!-- Row 1 -->
        <div class="card blue"><h3>Pending Requests</h3><p>36</p></div>
        <div class="card green"><h3>Approved</h3><p>50</p></div>
        <div class="card cyan"><h3>Funds Received</h3><p>₱880</p></div>
        <div class="card yellow"><h3>Paid Invoices</h3><p>₱1,900</p></div>

        <!-- Row 2 -->
        <div class="card purple"><h3>Monthly Total</h3><p>₱5,430</p></div>
        <div class="card red"><h3>Saved</h3><p>₱1,200</p></div>
        <div class="card"><h3>Asset Categories</h3><canvas id="assetCategoriesChart"></canvas></div>
        <div class="card"><h3>Asset Status</h3><canvas id="assetStatusChart"></canvas></div>

        <!-- Row 3 -->
        <div class="left-column">
          <div class="card wide"><h3>Overall Approved</h3><canvas id="overallApprovedChart" class="tall"></canvas></div>
          <div class="card wide"><h3>Overall Requests</h3><canvas id="overallRequestsChart" class="tall"></canvas></div>
        </div>

        <!-- Staff Table -->
        <div class="card staff-table">
          <div class="header-live">
            <h3>Team Requests</h3>
            <div class="live"><div class="dot"></div>Live</div>
          </div>
          <div class="table-wrapper">
            <table>
              <thead>
                <tr><th>Employee</th><th>Request</th><th>Status</th></tr>
              </thead>
              <tbody>
                <tr><td>Maria Cruz</td><td>Printer</td><td><span class="status pending">Pending</span></td></tr>
                <tr><td>Juan Dela Cruz</td><td>Monitor</td><td><span class="status approved">Approved</span></td></tr>
                <tr><td>Ella Santos</td><td>Desk Chair</td><td><span class="status rejected">Rejected</span></td></tr>
              </tbody>
            </table>
          </div>
        </div>
      </section>
    </main>
  </div>

  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
    // PIE - Asset Categories
    new Chart(document.getElementById('assetCategoriesChart'), {
      type: 'pie',
      data: {
        labels: ['Computers', 'Furniture', 'Electronics', 'Misc'],
        datasets: [{
          data: [35, 25, 25, 15],
          backgroundColor: ['#3b82f6', '#10b981', '#f59e0b', '#8b5cf6']
        }]
      },
      options: { plugins: { legend: { position: 'bottom' } }, maintainAspectRatio: false }
    });

    // DOUGHNUT - Asset Status
    new Chart(document.getElementById('assetStatusChart'), {
      type: 'doughnut',
      data: {
        labels: ['Active', 'Damaged', 'Lost'],
        datasets: [{
          data: [70, 20, 10],
          backgroundColor: ['#10b981', '#facc15', '#ef4444']
        }]
      },
      options: { plugins: { legend: { position: 'bottom' } }, maintainAspectRatio: false }
    });

    // BAR - Overall Approved
    new Chart(document.getElementById('overallApprovedChart'), {
      type: 'bar',
      data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May'],
        datasets: [{
          label: 'Approved Requests',
          data: [10, 14, 9, 15, 11],
          backgroundColor: '#10b981',
          borderRadius: 6
        }]
      },
      options: {
        plugins: { legend: { display: false } },
        maintainAspectRatio: false,
        scales: { y: { beginAtZero: true } }
      }
    });

    // LINE - Overall Requests
    new Chart(document.getElementById('overallRequestsChart'), {
      type: 'line',
      data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May'],
        datasets: [{
          label: 'Requests',
          data: [6, 10, 8, 16, 12],
          borderColor: '#3b82f6',
          backgroundColor: 'rgba(59,130,246,0.1)',
          tension: 0.4,
          fill: true
        }]
      },
      options: {
        plugins: { legend: { display: false } },
        maintainAspectRatio: false,
        scales: { y: { beginAtZero: true } }
      }
    });
  </script>
</body>
</html>
