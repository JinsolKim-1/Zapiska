<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Requests | Zapiska</title>
    @vite(['resources/css/employee-requests.css'])
</head>
<body>
    <div class="layout">
        @include('users.includes.mainsidebar')

        <main class="main-content">
            <header class="dashboard-header">
                <h1>My Requests</h1>
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
                <div class="card wide">
                    <h3>Request History</h3>
                    <div class="table-wrapper">
                        <table>
                            <thead>
                                <tr>
                                    <th>Asset Requested</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Estimated Cost</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Laptop</td>
                                    <td>2025-10-01</td>
                                    <td>Approved</td>
                                    <td>₱50,000</td>
                                </tr>
                                <tr>
                                    <td>Mouse</td>
                                    <td>2025-10-10</td>
                                    <td>Pending</td>
                                    <td>₱500</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </main>
    </div>
</body>
</html>
