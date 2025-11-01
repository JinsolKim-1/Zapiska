<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assets | Zapiska</title>
    @vite(['resources/css/manager-assets.css'])
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
                <!-- Charts -->
                <div class="card">
                    <h3>Asset Categories</h3>
                    <canvas id="categoriesPieChart"></canvas>
                </div>
                <div class="card">
                    <h3>Purchase Cost Range</h3>
                    <canvas id="costRangePieChart"></canvas>
                </div>

                <!-- Table -->
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

                        @if(Auth::user()->role->category === 'manager')
                            <a href="#" class="order-form-btn">+ Order Form</a>
                        @elseif(Auth::user()->role->category === 'employee')
                            <a href="#" class="order-form-btn">+ My Request</a>
                        @endif
                    </div>

                    <div class="table-wrapper">
                        <table id="assetsTable">
                            <thead>
                                <tr>
                                    <th>Asset ID</th>
                                    <th>Category</th>
                                    <th>Description</th>
                                    <th>Purchase Date</th>
                                    <th>Purchase Cost</th>
                                    <th>Location</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($assets as $asset)
                                    <tr>
                                        <td>{{ $asset->asset_id }}</td>
                                        <td>{{ $asset->category->category_name ?? 'N/A' }}</td>
                                        <td>{{ $asset->asset_description }}</td>
                                        <td>{{ $asset->purchase_date ? \Carbon\Carbon::parse($asset->purchase_date)->format('Y-m-d') : 'N/A' }}</td>
                                        <td>₱{{ number_format($asset->purchase_cost ?? 0, 2) }}</td>
                                        <td>{{ $asset->sector->department_name ?? 'N/A' }}</td>
                                        <td>{{ ucfirst($asset->asset_status) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const searchInput = document.getElementById('searchInput');
        const categoryFilter = document.getElementById('categoryFilter');
        const table = document.getElementById('assetsTable');
        const rows = table.querySelectorAll('tbody tr');

        function filterTable() {
            const searchTerm = searchInput.value.toLowerCase();
            const categoryTerm = categoryFilter.value.toLowerCase();

            rows.forEach(row => {
                const cells = row.querySelectorAll('td');
                const matchesSearch = Array.from(cells).some(cell => cell.textContent.toLowerCase().includes(searchTerm));
                const matchesCategory = categoryTerm === '' || cells[1].textContent.toLowerCase() === categoryTerm;
                row.style.display = (matchesSearch && matchesCategory) ? '' : 'none';
            });
        }

        searchInput.addEventListener('keyup', filterTable);
        categoryFilter.addEventListener('change', filterTable);
    </script>
</body>
</html>
