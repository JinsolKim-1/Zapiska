<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SuperAdmin Dashboard | Zapiska</title>
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    @vite(['resources/css/superadmin.css', 'resources/js/superadmin.js'])
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <h2>Zapiska</h2>
        <a href="#" class="active"><i class="bx bx-home"></i> Dashboard</a>
        <a href="#"><i class="bx bx-user"></i> Users</a>
        <a href="#"><i class="bx bx-buildings"></i> Companies</a>
        <a href="#"><i class="bx bx-clipboard"></i> Logs</a>
        <a href="#"><i class="bx bx-cog"></i> Settings</a>
        <a href="#"><i class="bx bx-bar-chart"></i> Reports</a>
        <form method="POST" action="{{ route('superadmin.logout') }}">
            @csrf
            <button type="submit" class="logout"><i class="bx bx-log-out"></i> Logout</button>
        </form>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <header class="top-header">
            <div>
                <h1>Welcome back, {{ $superadmin->super_username ?? 'SuperAdmin' }}</h1>
                <p id="datetime"></p>
            </div>
            <div class="profile-badge">
                <div class="profile-avatar">
                    <img 
                        src="{{ $superadmin->profile ? asset('storage/' . $superadmin->profile) : asset('images/cat.png') }}" 
                        alt="Profile Picture">
                </div>
                <div class="profile-info">
                    <span class="profile-name">{{ $superadmin->super_username ?? 'Admin' }}</span>
                </div>
            </div>
        </header>

        <section class="summary-cards">
            <div class="card">
                <i class="bx bx-user"></i>
                <div class="card-info">
                    <h3>Total Users</h3>
                    <p id="totalUsers">1,204</p>
                </div>
            </div>

            <div class="card">
                <i class="bx bx-buildings"></i>
                <div class="card-info">
                    <h3>Companies</h3>
                    <p id="totalCompanies">56</p>
                </div>
            </div>

            <div class="card">
                <i class="bx bx-wifi"></i>
                <div class="card-info">
                    <h3>System Status</h3>
                    <p id="systemStatus" class="online">Online</p>
                </div>
            </div>
        </section>

        <section class="verification-panel">
            <h2>Pending Company Verifications</h2>
            <div class="company-list">
                <!-- Sample entry -->
                <div class="company-card">
                    <div class="company-info">
                        <h3>NovaTech Solutions</h3>
                        <p>Submitted by: <strong>johndoe</strong></p>
                        <p>Email: contact@novatech.com</p>
                        <p>Date: 2025-10-24</p>
                    </div>
                    <div class="actions">
                        <button class="approve-btn">Approve</button>
                        <button class="reject-btn">Reject</button>
                    </div>
                </div>
            </div>
        </section>

        <!-- Charts -->
        <section class="charts">
            <div class="chart-container">
                <h3>Company Verification Trend</h3>
                <canvas id="companyTrendChart"></canvas>
            </div>
            <div class="chart-container">
                <h3>User Growth Overview</h3>
                <canvas id="userGrowthChart"></canvas>
            </div>
        </section>


        <!-- Add SuperAdmin -->
        <button class="add-btn" id="openModal"><i class="bx bx-plus-circle"></i> Add Associate</button>
    </div>

    <!-- Add SuperAdmin Modal -->
    <div class="modal" id="superadminModal">
        <div class="modal-content">
            <span class="close" id="closeModal">&times;</span>
            <h2>Add New Associate</h2>

            <form id="addSuperadminForm" enctype="multipart/form-data">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="superadminUsername">Username</label>
                        <input type="text" id="superadminUsername" placeholder="Enter username" required>
                    </div>

                    <div class="form-group">
                        <label for="superadminEmail">Email</label>
                        <input type="email" id="superadminEmail" placeholder="Enter email address" required>
                    </div>

                    <div class="form-group">
                        <label for="superadminPassword">Password</label>
                        <input type="password" id="superadminPassword" placeholder="Enter password" required>
                    </div>

                    <div class="form-group">
                        <label for="firstName">First Name</label>
                        <input type="text" id="firstName" placeholder="Enter first name" required>
                    </div>

                    <div class="form-group">
                        <label for="lastName">Last Name</label>
                        <input type="text" id="lastName" placeholder="Enter last name" required>
                    </div>

                    <div class="form-group">
                        <label for="contact">Contact Number</label>
                        <input type="text" id="contact" placeholder="Enter contact number" required>
                    </div>

                    <div class="form-group full-width">
                        <label for="profile">Profile Picture</label>
                        <input type="file" id="profile" accept="image/*">
                    </div>
                </div>

                <button type="submit" class="add-btn">Create</button>
            </form>
        </div>
    </div>
</body>
</html>
