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
    @include('superadmin.includes.sidebar')
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
</body>
</html>
