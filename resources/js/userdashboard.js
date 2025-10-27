import Chart from 'chart.js/auto';

// Overall Approved (Bar Chart)
new Chart(document.getElementById('overallApprovedChart'), {
    type: 'bar',
    data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
        datasets: [{
            label: 'Approved Requests',
            data: [12, 19, 3, 5, 2, 3],
            backgroundColor: '#4e73df',
            borderRadius: 8,
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true } }
    }
});

// Overall Requests (Line Chart)
new Chart(document.getElementById('overallRequestsChart'), {
    type: 'line',
    data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
        datasets: [{
            label: 'Requests',
            data: [5, 9, 8, 12, 7, 10],
            borderColor: '#1cc88a',
            backgroundColor: 'rgba(28,200,138,0.1)',
            tension: 0.3,
            fill: true
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true } }
    }
});

// Asset Categories (Pie Chart)
new Chart(document.getElementById('assetCategoriesChart'), {
    type: 'pie',
    data: {
        labels: ['Electronics', 'Furniture', 'Vehicles', 'Office Supplies'],
        datasets: [{
            data: [40, 25, 20, 15],
            backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e']
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { position: 'bottom' } }
    }
});

// Asset Status (Doughnut Chart)
new Chart(document.getElementById('assetStatusChart'), {
    type: 'doughnut',
    data: {
        labels: ['Active', 'Under Maintenance', 'Disposed'],
        datasets: [{
            data: [70, 20, 10],
            backgroundColor: ['#1cc88a', '#f6c23e', '#e74a3b']
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { position: 'bottom' } }
    }
});
