<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Departments | Zapiska</title>
  @vite(['resources/css/departments.css'])
</head>
<body>
  <div class="layout">
    @include('users.includes.mainsidebar')

    <main class="main-content">
      <header class="dashboard-header">
        <h1>Departments & Budget Overview</h1>
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

      <section class="dashboard-grid departments-grid">
        <!-- Charts Row -->
        <div class="card">
          <h3>No. of Assets per Department</h3>
          <canvas id="assetPieChart"></canvas>
        </div>
        <div class="card">
          <h3>Budget Used per Department</h3>
          <canvas id="budgetBarChart"></canvas>
        </div>

        <!-- Table Row -->
        <div class="card wide">
          <h3>Department Summary</h3>
          <div class="table-wrapper">
            <table>
              <thead>
                <tr>
                  <th>Department</th>
                  <th>No. of Assets</th>
                  <th>Requisitions</th>
                  <th>Budget Used</th>
                  <th>% Budget Used</th>
                  <th>Budget Remaining</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>IT Department</td>
                  <td>120</td>
                  <td>25</td>
                  <td>₱150,000</td>
                  <td>75%</td>
                  <td>₱50,000</td>
                </tr>
                <tr>
                  <td>Finance</td>
                  <td>80</td>
                  <td>15</td>
                  <td>₱100,000</td>
                  <td>50%</td>
                  <td>₱100,000</td>
                </tr>
                <tr>
                  <td>HR</td>
                  <td>50</td>
                  <td>10</td>
                  <td>₱60,000</td>
                  <td>60%</td>
                  <td>₱40,000</td>
                </tr>
                <tr>
                  <td>Logistics</td>
                  <td>70</td>
                  <td>12</td>
                  <td>₱90,000</td>
                  <td>45%</td>
                  <td>₱110,000</td>
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
    // PIE CHART - No. of Assets per Department
    new Chart(document.getElementById('assetPieChart'), {
      type: 'pie',
      data: {
        labels: ['IT', 'Finance', 'HR', 'Logistics'],
        datasets: [{
          data: [120, 80, 50, 70],
          backgroundColor: ['#3b82f6', '#10b981', '#f59e0b', '#8b5cf6']
        }]
      },
      options: {
        plugins: { legend: { position: 'bottom' } },
        maintainAspectRatio: false
      }
    });

    // BAR CHART - Budget Used per Department
    new Chart(document.getElementById('budgetBarChart'), {
      type: 'bar',
      data: {
        labels: ['IT', 'Finance', 'HR', 'Logistics'],
        datasets: [{
          label: 'Budget Used (₱)',
          data: [150000, 100000, 60000, 90000],
          backgroundColor: ['#10b981', '#3b82f6', '#f59e0b', '#8b5cf6'],
          borderRadius: 6
        }]
      },
      options: {
        plugins: { legend: { display: false } },
        maintainAspectRatio: false,
        scales: {
          y: { beginAtZero: true, ticks: { callback: v => '₱' + v.toLocaleString() } }
        }
      }
    });
  </script>
</body>
</html>
