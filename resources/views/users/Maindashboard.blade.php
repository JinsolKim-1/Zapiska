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

    <main class="main-content">
        <header class="dashboard-header">
            <h1>Analytics</h1>
            <div class="user-info">
                <div class="user-details">
                    <div class="user-name">{{ Auth::user()->username }}</div>
                    <span class="role">{{ Auth::user()->role->role_name }}</span>
                </div>
                <div class="avatar">
                    <i class="bx bxs-user"></i>
                </div>
            </div>
        </header>

      <section class="dashboard-grid">
        <!-- Row 1 -->
        <div class="card blue">
          <h3>Pending Requests</h3>
          <p>786</p>
        </div>
        <div class="card green">
          <h3>Approved</h3>
          <p>₱3,456</p>
        </div>
        <div class="card cyan">
          <h3>Funds Received</h3>
          <p>₱123</p>
        </div>
        <div class="card yellow">
          <h3>Paid Invoices</h3>
          <p>₱1,234</p>
        </div>

        <!-- Row 2 -->
        <div class="card purple">
          <h3>Month Total</h3>
          <p>₱5,678</p>
        </div>
        <div class="card red">
          <h3>Saved</h3>
          <p>₱987</p>
        </div>
        <div class="card">
          <h3>Asset Categories</h3>
          <canvas id="assetCategoriesChart"></canvas>
        </div>
        <div class="card">
          <h3>Asset Status</h3>
          <canvas id="assetStatusChart"></canvas>
        </div>

        <!-- Row 3: Overall Approved + Requests on left, Staff Requests table on right -->
        <div class="left-column">
          <div class="card wide">
            <h3>Overall Approved</h3>
            <canvas id="overallApprovedChart" class="tall"></canvas>
          </div>
          <div class="card wide">
            <h3>Overall Requests</h3>
            <canvas id="overallRequestsChart" class="tall"></canvas>
          </div>
        </div>

        <div class="card staff-table">
          <div class="header-live">
            <h3>Staff Requests</h3>
            <div class="live"><div class="dot"></div>Live</div>
          </div>

          <div class="table-wrapper">
            <table>
              <thead>
                <tr>
                  <th>Name</th>
                  <th>Request</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>John Doe</td>
                  <td>New Monitor</td>
                  <td><span class="status pending">Pending</span></td>
                </tr>
                <tr>
                  <td>Jane Smith</td>
                  <td>Office Chair</td>
                  <td><span class="status approved">Approved</span></td>
                </tr>
                <tr>
                  <td>Kevin Cruz</td>
                  <td>Mouse</td>
                  <td><span class="status rejected">Rejected</span></td>
                </tr>
                <tr>
                  <td>Anne Tan</td>
                  <td>Laptop</td>
                  <td><span class="status approved">Approved</span></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </section>
    </main>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
    // PIE - Asset Categories
    new Chart(document.getElementById('assetCategoriesChart'), {
      type: 'pie',
      data: {
        labels: ['Computers', 'Furniture', 'Vehicles', 'Others'],
        datasets: [{
          data: [40, 25, 20, 15],
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
          data: [60, 25, 15],
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
          data: [12, 19, 7, 14, 10],
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
          data: [5, 15, 10, 20, 18],
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
