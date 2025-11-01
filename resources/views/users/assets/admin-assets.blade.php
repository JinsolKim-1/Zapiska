<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assets | Zapiska</title>
    @vite(['resources/css/assets.css','resources/js/admin-assets.js'])
</head>
<body>
    <div class="layout">
        @include('users.includes.mainsidebar')

        <main class="main-content">
            <header class="dashboard-header">
                <h1>Assets Overview</h1>
                <div class="user-info">
                    <div class="user-details">
                        <div class="user-name">{{ Auth::user()->username }}</div>
                        <span class="role">{{ Auth::user()->role->role_name }}</span>
                    </div>
                    <div class="avatar"><i class="bx bxs-user"></i></div>
                </div>
            </header>

            <section class="dashboard-grid assets-grid">
                <!-- Charts Row -->
                <div class="card">
                    <h3>Requester Distribution</h3>
                    <canvas id="requesterPieChart"></canvas>
                </div>
                <div class="card">
                    <h3>Asset Categories</h3>
                    <canvas id="categoriesPieChart"></canvas>
                </div>
                <div class="card">
                    <h3>Purchase Cost Range</h3>
                    <canvas id="costRangePieChart"></canvas>
                </div>

                <!-- Table Row -->
                <div class="card wide">
                    <h3>Asset Summary</h3>
                    <div class="table-controls">
                        <input type="text" id="searchInput" placeholder="Search assets...">
                        <select id="categoryFilter">
                            <option value="">All Categories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->category_name }}">{{ $category->category_name }}</option>
                            @endforeach
                        </select>

                        @if(Auth::user()->role && Auth::user()->role->category === 'admin')
                            <button id="addCategoryBtn">+ Add Category</button>
                        @endif

                        <a href="{{ route('users.orders.form') }}" class="order-form-btn">+ Order Form</a>
                    </div>

                    <div class="table-wrapper">
                        <table id="assetsTable">
                            <thead>
                                <tr>
                                    <th>Asset ID</th>
                                    <th>Assigned User</th>
                                    <th>Category</th>
                                    <th>Description</th>
                                    <th>Purchase Date</th>
                                    <th>Purchase Cost</th>
                                    <th>Location</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $statuses = ['available', 'in_use', 'maintenance', 'disposed'];
                                @endphp
                                @foreach($assets as $asset)
                                    <tr>
                                        <td>{{ $asset->asset_id }}</td>
                                        <td>{{ $asset->user->username ?? 'N/A' }}</td>
                                        <td>{{ $asset->category->category_name ?? 'N/A' }}</td>
                                        <td>{{ $asset->asset_description }}</td>
                                        <td>{{ $asset->purchase_date ? \Carbon\Carbon::parse($asset->purchase_date)->format('Y-m-d') : 'N/A' }}</td>
                                        <td>$ {{ $asset->purchase_cost ? number_format($asset->purchase_cost, 2) : '0.00' }}</td>
                                        <td>
                                            @if(Auth::user()->role && Auth::user()->role->category === 'admin')
                                                <select class="styled-dropdown sector-dropdown" data-asset-id="{{ $asset->asset_id }}">
                                                    <option value="">Select Location</option>
                                                    @foreach($sectors as $sector)
                                                        <option value="{{ $sector->sector_id }}"
                                                            {{ $asset->sector_id == $sector->sector_id ? 'selected' : '' }}>
                                                            {{ $sector->department_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            @else
                                                {{ $asset->sector->department_name ?? 'N/A' }}
                                            @endif
                                        </td>
                                        <td>
                                            @if(Auth::user()->role && Auth::user()->role->category === 'admin')
                                                <select class="styled-dropdown status-dropdown" data-asset-id="{{ $asset->asset_id }}">
                                                    @foreach($statuses as $status)
                                                        <option value="{{ $status }}" {{ $asset->asset_status === $status ? 'selected' : '' }}>
                                                            {{ ucfirst(str_replace('_',' ',$status)) }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            @else
                                                <span class="status-badge {{ $asset->asset_status }}">
                                                    {{ ucfirst(str_replace('_',' ',$asset->asset_status)) }}
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </main>
    </div>

    <!-- Add Category Modal -->
    <div id="addCategoryModal">
        <form id="addCategoryForm" method="POST" action="{{ route('users.assets.addCategory') }}">
            @csrf
            <label for="category_name">Category Name:</label>
            <input type="text" name="category_name" id="category_name" required>
            <button type="submit">Add</button>
            <button type="button" id="cancelCategoryBtn">Cancel</button>
        </form>
    </div>
</body>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</html>
