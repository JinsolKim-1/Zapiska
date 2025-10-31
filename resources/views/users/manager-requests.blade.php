<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manager Requests | Zapiska</title>
    @vite(['resources/css/manager-requests.css'])
</head>
<body>
    <div class="layout">
        @include('users.includes.mainsidebar')

        <main class="main-content">
            <header class="dashboard-header">
                <h1>Sector Requests Overview</h1>
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

            <section class="requests-grid">
                <!-- Charts (Manager Overview) -->
                <div class="card">
                    <h3>Requester per Employee</h3>
                    <canvas id="requesterPieChart"></canvas>
                </div>
                <div class="card">
                    <h3>Status Summary</h3>
                    <canvas id="statusPieChart"></canvas>
                </div>

                <!-- Full-width Table -->
                <div class="card wide">
                    <h3>Sector Request Summary</h3>
                    <div class="table-wrapper">
                        <table>
                            <thead>
                                <tr>
                                    <th>Requester</th>
                                    <th>Asset</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Estimated Cost</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>John Doe</td>
                                    <td>Laptop</td>
                                    <td>2025-10-01</td>
                                    <td>Pending</td>
                                    <td>₱50,000</td>
                                    <td>
                                        <button class="approve-btn">Approve</button>
                                        <button class="reject-btn">Reject</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Jane Smith</td>
                                    <td>Printer</td>
                                    <td>2025-09-20</td>
                                    <td>Approved</td>
                                    <td>₱12,000</td>
                                    <td>
                                        <button class="approve-btn">Approve</button>
                                        <button class="reject-btn">Reject</button>
                                    </td>
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
        new Chart(document.getElementById('requesterPieChart'), {
            type: 'pie',
            data: {
                labels: ['John Doe', 'Jane Smith'],
                datasets: [{
                    data: [4, 6],
                    backgroundColor: ['#3b82f6', '#f97316']
                }]
            },
            options: { plugins: { legend: { position: 'bottom' } } }
        });

        new Chart(document.getElementById('statusPieChart'), {
            type: 'pie',
            data: {
                labels: ['Pending', 'Approved', 'Rejected'],
                datasets: [{
                    data: [3, 5, 2],
                    backgroundColor: ['#fbbf24', '#10b981', '#ef4444']
                }]
            },
            options: { plugins: { legend: { position: 'bottom' } } }
        });
    </script>
</body>
</html>
