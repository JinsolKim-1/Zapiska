import Chart from 'chart.js/auto';

// Real-time clock
const datetime = document.getElementById('datetime');
function updateTime() {
    const now = new Date();
    datetime.textContent = now.toLocaleString();
}
setInterval(updateTime, 1000);
updateTime();

// Modal controls
const modal = document.getElementById('superadminModal');
const openModal = document.getElementById('openModal');
const closeModal = document.getElementById('closeModal');
openModal.onclick = () => (modal.style.display = 'flex');
closeModal.onclick = () => (modal.style.display = 'none');
window.onclick = (e) => { if (e.target === modal) modal.style.display = 'none'; };

document.querySelectorAll('.approve-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        if (confirm('Approve this company?')) {
            alert('Company approved successfully!');
        }
    });
});

document.querySelectorAll('.reject-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        if (confirm('Reject this company?')) {
            alert('Company rejected.');
        }
    });
});

// ─── Charts ──────────────────────────────────────────────────────

const colors = {
    blue: '#7aa2f7',
    pink: '#f7768e',
    teal: '#73daca',
    purple: '#bb9af7',
    yellow: '#e0af68'
};

// Company Verification Trend
new Chart(document.getElementById('companyTrendChart'), {
    type: 'line',
    data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct'],
        datasets: [
            {
                label: 'Pending Requests',
                data: [5, 9, 7, 10, 4, 6, 3, 8, 5, 2],
                borderColor: colors.pink,
                backgroundColor: 'rgba(247,118,142,0.2)',
                tension: 0.4,
                fill: true
            },
            {
                label: 'Approved Companies',
                data: [3, 5, 6, 9, 6, 8, 7, 10, 6, 8],
                borderColor: colors.blue,
                backgroundColor: 'rgba(122,162,247,0.2)',
                tension: 0.4,
                fill: true
            }
        ]
    },
    options: {
        plugins: { legend: { labels: { color: '#c0caf5' } } },
        scales: {
            x: { ticks: { color: '#a9b1d6' }, grid: { color: '#2e3348' } },
            y: { ticks: { color: '#a9b1d6' }, grid: { color: '#2e3348' } }
        }
    }
});

// User Growth
new Chart(document.getElementById('userGrowthChart'), {
    type: 'bar',
    data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct'],
        datasets: [{
            label: 'New Users',
            data: [80, 120, 140, 200, 180, 210, 250, 260, 290, 300],
            backgroundColor: colors.teal,
            borderRadius: 6
        }]
    },
    options: {
        plugins: { legend: { labels: { color: '#c0caf5' } } },
        scales: {
            x: { ticks: { color: '#a9b1d6' }, grid: { display: false } },
            y: { ticks: { color: '#a9b1d6' }, grid: { color: '#2e3348' } }
        }
    }
});
