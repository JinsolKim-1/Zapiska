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
                        <button id="specialRequestBtn" class="special-request-btn">+ Special Request</button>
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

<!-- Special Request Modal -->
<div id="specialRequestModal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Submit Special Asset Request</h2>
        <form action="{{ route('manager.specialRequests.store') }}" method="POST">
            @csrf
            <div class="form-group floating">
                <input type="text" name="special_asset" id="special_asset" autocomplete="off" required placeholder=" ">
                <label for="special_asset">Asset Name</label>
            </div>
            <div class="form-group floating">
                <textarea name="justification" id="justification" rows="4" autocomplete="off" required placeholder=" "></textarea>
                <label for="justification">Justification</label>
            </div>
            <input type="hidden" name="sector_id" value="{{ Auth::user()->sector_id }}">
            <input type="hidden" name="company_id" value="{{ Auth::user()->company_id }}">
            <div class="modal-buttons">
                <button type="submit" class="btn-submit">Send to Admin</button>
                <button type="button" class="btn-cancel">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {

    // =========================
    // Table Filter by Search & Category
    // =========================
    const searchInput = document.getElementById('searchInput');
    const categoryFilter = document.getElementById('categoryFilter');
    const table = document.getElementById('assetsTable').getElementsByTagName('tbody')[0];

    function filterTable() {
        const searchValue = searchInput.value.toLowerCase();
        const selectedCategory = categoryFilter.value;

        Array.from(table.rows).forEach(row => {
            const description = row.cells[2].textContent.toLowerCase();
            const category = row.cells[1].textContent;

            const matchesSearch = description.includes(searchValue);
            const matchesCategory = selectedCategory === '' || category === selectedCategory;

            row.style.display = (matchesSearch && matchesCategory) ? '' : 'none';
        });
    }

    searchInput.addEventListener('input', filterTable);
    categoryFilter.addEventListener('change', filterTable);

    // =========================
    // Asset Categories Pie Chart
    // =========================
    const categoryChartData = @json($categoryChartData ?? []);
    const categoryLabels = categoryChartData.map(item => item.name);
    const categoryCounts = categoryChartData.map(item => item.count);

    const palette = ['#10b981','#3b82f6','#f59e0b','#ef4444','#8b5cf6','#ec4899','#f43f5e','#6366f1','#f97316','#8b5c2c'];
    const categoryColors = categoryCounts.map((_, i) => palette[i % palette.length]);

    if(categoryLabels.length > 0) {
        new Chart(document.getElementById('categoriesPieChart'), {
            type: 'pie',
            data: { labels: categoryLabels, datasets: [{ data: categoryCounts, backgroundColor: categoryColors, borderColor: '#fff', borderWidth: 1 }] },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' }, tooltip: { enabled: true } } }
        });
    }

    // =========================
    // Purchase Cost Pie Chart
    // =========================
    const costLabels = @json(array_keys($costRanges ?? []));
    const costData = @json(array_values($costRanges ?? []));
    const costColors = ['#3b82f6','#10b981','#f59e0b','#ef4444'];

    if(costLabels.length > 0) {
        new Chart(document.getElementById('costRangePieChart'), {
            type: 'pie',
            data: { labels: costLabels, datasets: [{ data: costData, backgroundColor: costColors, borderColor: '#fff', borderWidth: 1 }] },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' }, tooltip: { enabled: true } } }
        });
    }

    // =========================
    // Special Request Modal
    // =========================
    const modal = document.getElementById('specialRequestModal');
    const openBtn = document.getElementById('specialRequestBtn');
    const closeBtn = modal.querySelector('.close');
    const cancelBtn = modal.querySelector('.btn-cancel');

    if(openBtn) openBtn.addEventListener('click', () => modal.classList.add('show'));
    closeBtn.addEventListener('click', () => modal.classList.remove('show'));
    cancelBtn.addEventListener('click', () => modal.classList.remove('show'));
    window.addEventListener('click', (event) => { 
        if(event.target === modal) modal.classList.remove('show'); 
    });

});
</script>

</body>
</html>
