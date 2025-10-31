<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipts | Zapiska</title>
    @vite(['resources/css/receipts.css'])
</head>
<body>
    @include('users.includes.mainsidebar')

    <main class="main-content">
        <header class="dashboard-header">
            <h1>Receipts Overview</h1>

            <div class="user-info">
                <div class="user-details">
                    <div class="user-name">{{ Auth::user()->username }}</div>
                    <span class="role">{{ Auth::user()->role->role_name }}</span>
                </div>
                <div class="avatar"><i class="bx bxs-user"></i></div>
            </div>
        </header>

        <div class="receipts-controls">
            <input type="text" id="searchInput" placeholder="Search receipts...">
            <select id="filterSelect">
                <option value="all">All Statuses</option>
                <option value="approved">Approved</option>
                <option value="pending">Pending</option>
                <option value="rejected">Rejected</option>
            </select>
        </div>

        <!-- Dynamic Receipts -->
        <section class="receipts-grid" id="receiptsGrid">
            @forelse ($receipts as $receipt)
                <div class="receipt-card">
                    <strong>{{ $receipt->receipt_number }}</strong> <!-- Receipt number in bold -->
                    <p>Item: {{ $receipt->asset_name }}</p> <!-- Item ordered -->
                    <p>Quantity: {{ $receipt->quantity }}</p>
                    <p>Total Cost: ${{ number_format($receipt->total_cost, 2) }}</p>
                    <p>Approved by: {{ $receipt->approved_by ?? 'N/A' }}</p> 
                    <p>Date: {{ $receipt->receipt_date ? $receipt->receipt_date->format('Y-m-d') : 'N/A' }}</p>
                </div>
            @empty
                <p class="no-data">No receipts found.</p>
            @endforelse
        </section>
    </main>

    <script>
        const searchInput = document.getElementById('searchInput');
        const filterSelect = document.getElementById('filterSelect');
        const receiptsGrid = document.getElementById('receiptsGrid');

        searchInput.addEventListener('input', filterReceipts);
        filterSelect.addEventListener('change', filterReceipts);

        function filterReceipts() {
            const searchValue = searchInput.value.toLowerCase();
            const filterValue = filterSelect.value;
            const cards = receiptsGrid.querySelectorAll('.receipt-card');

            cards.forEach(card => {
                const text = card.textContent.toLowerCase();
                const matchesFilter = filterValue === 'all' || text.includes(filterValue);
                const matchesSearch = text.includes(searchValue);
                card.style.display = (matchesFilter && matchesSearch) ? 'flex' : 'none';
            });
        }
    </script>
</body>
</html>
