
const searchInput = document.getElementById('searchInput');
const categoryFilter = document.getElementById('categoryFilter');
const table = document.getElementById('assetsTable');
const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');

// =========================
// Chart.js Initialization
// =========================
const requesterChartCtx = document.getElementById('requesterPieChart').getContext('2d');
const categoryChartCtx = document.getElementById('categoriesPieChart').getContext('2d');
const costChartCtx = document.getElementById('costRangePieChart').getContext('2d');

let requesterChart, categoryChart, costChart;

// =========================
// 1. Filter Table & Update Charts
// =========================
function filterTableAndCharts() {
    const searchTerm = searchInput.value.toLowerCase();
    const categoryTerm = categoryFilter.value.toLowerCase();

    // Filter table rows
    const visibleRows = [];
    for (let row of rows) {
        const cells = row.getElementsByTagName('td');
        const matchesSearch = Array.from(cells).some(cell => cell.textContent.toLowerCase().includes(searchTerm));
        const matchesCategory = categoryTerm === '' || cells[2].textContent.toLowerCase() === categoryTerm;
        row.style.display = (matchesSearch && matchesCategory) ? '' : 'none';
        if(row.style.display !== 'none') visibleRows.push(row);
    }

    // =========================
    // Requester Pie Chart
    // =========================
    const requesterCounts = {};
    visibleRows.forEach(row => {
        const user = row.cells[1].textContent || 'Unassigned';
        requesterCounts[user] = (requesterCounts[user] || 0) + 1;
    });

    const requesterLabels = Object.keys(requesterCounts);
    const requesterData = Object.values(requesterCounts);

    if(requesterChart) requesterChart.destroy();
    requesterChart = new Chart(requesterChartCtx, {
        type: 'pie',
        data: {
            labels: requesterLabels,
            datasets: [{
                label: 'Assigned Users',
                data: requesterData,
                backgroundColor: ['#4CAF50','#FF9800','#2196F3','#F44336','#9C27B0','#00BCD4','#FFEB3B']
            }]
        },
        options: { responsive: true,maintainAspectRatio: false, plugins: { legend: { position: 'left' } } }
    });

    // =========================
    // Category Pie Chart
    // =========================
    const categoryCounts = {};
    visibleRows.forEach(row => {
        const cat = row.cells[2].textContent || 'Uncategorized';
        categoryCounts[cat] = (categoryCounts[cat] || 0) + 1;
    });

    const categoryLabels = Object.keys(categoryCounts);
    const categoryData = Object.values(categoryCounts);

    if(categoryChart) categoryChart.destroy();
    categoryChart = new Chart(categoryChartCtx, {
        type: 'pie',
        data: {
            labels: categoryLabels,
            datasets: [{
                label: 'Asset Categories',
                data: categoryData,
                backgroundColor: ['#FF6384','#36A2EB','#FFCE56','#8BC34A','#FF9800','#9C27B0','#00BCD4' ]
            }]
        },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'left' } } }
    });

    // =========================
    // Purchase Cost Pie Chart
    // =========================
    const costRanges = [
        { label: '0-10000', min: 0, max: 1000 },
        { label: '10001-100000', min: 10001, max: 100000 },
        { label: '100001-500000', min: 100001, max: 500000 },
        { label: '500001-1000000', min: 500001, max: 1000000 },
        { label: '1000001+', min: 1000001, max: Infinity }
    ];

    const costCounts = costRanges.map(r => 0);
    visibleRows.forEach(row => {
        const cost = parseFloat(row.cells[5].textContent.replace('$','').replace(',','')) || 0;
        costRanges.forEach((r, i) => {
            if(cost >= r.min && cost <= r.max) costCounts[i]++;
        });
    });

    if(costChart) costChart.destroy();
    costChart = new Chart(costChartCtx, {
        type: 'pie',
        data: {
            labels: costRanges.map(r => r.label),
            datasets: [{
                label: 'Purchase Cost Range',
                data: costCounts,
                backgroundColor: ['#FF6384','#36A2EB','#FFCE56','#4CAF50','#951b1bff']
            }]
        },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'left' } } }
    });
}

// =========================
// Event Listeners
// =========================
searchInput.addEventListener('keyup', filterTableAndCharts);
categoryFilter.addEventListener('change', filterTableAndCharts);

// =========================
// 2. Category Modal
// =========================
const addBtn = document.getElementById('addCategoryBtn');
const modal = document.getElementById('addCategoryModal');
const cancelBtn = document.getElementById('cancelCategoryBtn');

if (addBtn) {
    addBtn.addEventListener('click', () => modal.classList.add('show'));
    cancelBtn.addEventListener('click', () => modal.classList.remove('show'));
}

// =========================
// 3. Sector & Status Assignment (Admin Only)
// =========================
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

// Update sector and auto-set status to 'in_use'
document.querySelectorAll('.sector-dropdown').forEach(select => {
    select.addEventListener('change', async function() {
        const assetId = this.dataset.assetId;
        const sectorId = this.value;

        try {
            const res = await fetch(`/users/assets/${assetId}/update-sector`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({ sector_id: sectorId })
            });

            if (res.ok) {
                // Auto-set status to "in_use"
                const statusRes = await fetch(`/users/assets/${assetId}/update-status`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                    body: JSON.stringify({ asset_status: 'in_use' })
                });

                if (statusRes.ok) {
                    const row = this.closest('tr');
                    const statusDropdown = row.querySelector('.status-dropdown');
                    if(statusDropdown) statusDropdown.value = 'in_use';
                    alert('✅ Sector assigned and status set to "In Use"!');
                    filterTableAndCharts(); // Refresh charts
                } else alert('⚠️ Failed to update status.');
            } else alert('⚠️ Failed to update sector.');
        } catch (err) { console.error(err); alert('❌ An error occurred.'); }
    });
});

// Manually update status
document.querySelectorAll('.status-dropdown').forEach(select => {
    select.addEventListener('change', async function() {
        const assetId = this.dataset.assetId;
        const assetStatus = this.value;
        try {
            const res = await fetch(`/users/assets/${assetId}/update-status`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({ asset_status: assetStatus })
            });

            if (res.ok) {
                alert('✅ Status updated successfully!');
                filterTableAndCharts(); 
            } else alert('⚠️ Failed to update status.');
        } catch (err) { console.error(err); alert('❌ An error occurred.'); }
    });
});

// =========================
// Initial Render
// =========================
filterTableAndCharts();
