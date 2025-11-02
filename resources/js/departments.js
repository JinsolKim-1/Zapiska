const { names, assetCounts, usedBudgets, csrfToken } = window.departmentsData;

// Charts
new Chart(document.getElementById('assetPieChart'), {
  type: 'pie',
  data: {
    labels: names,
    datasets: [{
      data: assetCounts,
      backgroundColor: ['#3b82f6','#10b981','#f59e0b','#8b5cf6','#ef4444','#f43f5e','#6366f1']
    }]
  },
  options: {
    plugins: { legend: { position: 'bottom' } },
    maintainAspectRatio: false
  }
});

new Chart(document.getElementById('budgetBarChart'), {
  type: 'bar',
  data: {
    labels: names,
    datasets: [{
      label: 'Budget Used ($)',
      data: usedBudgets,
      backgroundColor: ['#10b981','#3b82f6','#f59e0b','#8b5cf6','#ef4444','#f43f5e','#6366f1'],
      borderRadius: 6
    }]
  },
  options: {
    plugins: { legend: { display: false } },
    maintainAspectRatio: false,
    scales: { y: { beginAtZero: true, ticks: { callback: v => '$' + v.toLocaleString() } } }
  }
});

// Search Filter
document.getElementById('search-department')?.addEventListener('input', function() {
  const query = this.value.toLowerCase();
  document.querySelectorAll('#department-table tbody tr').forEach(row => {
    const name = row.querySelector('.dept-name')?.textContent.toLowerCase() || '';
    row.style.display = name.includes(query) ? '' : 'none';
  });
});

// Sort Filter
document.getElementById('filter-department')?.addEventListener('change', function() {
  const type = this.value;
  const tbody = document.querySelector('#department-table tbody');
  if (!tbody) return;

  const rows = Array.from(tbody.querySelectorAll('tr'));

  rows.sort((a, b) => {
    const num = (row, sel) => parseFloat(row.querySelector(sel)?.textContent.replace(/[^0-9.-]+/g, '')) || 0;

    if (type === 'assets') return num(b, '.dept-assets') - num(a, '.dept-assets');
    if (type === 'budget-used') return num(b, '.used-budget') - num(a, '.used-budget');
    if (type === 'budget-remaining') return num(b, '.remaining-budget') - num(a, '.remaining-budget');
    if (type === 'percent-used') return num(b, '.percent-used') - num(a, '.percent-used');
    return 0;
  });

  tbody.innerHTML = '';
  rows.forEach(r => tbody.appendChild(r));
});

// Modal Logic
const modal = document.getElementById('budgetModal');
const addBtn = document.getElementById('add-budget-btn');
const closeBtn = document.querySelector('.close-modal');
const saveBtn = document.getElementById('save-budget-btn');

addBtn?.addEventListener('click', () => modal.style.display = 'flex');
closeBtn?.addEventListener('click', () => modal.style.display = 'none');
window.addEventListener('click', e => { if (e.target === modal) modal.style.display = 'none'; });

saveBtn?.addEventListener('click', () => {
  const sectorId = document.getElementById('modal-department')?.value;
  const budget = document.getElementById('modal-budget')?.value;

  if (!sectorId || !budget) {
    alert("Please select a department and enter a budget amount.");
    return;
  }

  fetch(`/users/departments/add-budget`, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': csrfToken
    },
    body: JSON.stringify({ sector_id: sectorId, total_budget: parseFloat(budget) })
  })
  .then(res => res.json())
  .then(data => {
    if (data.success) {
      alert("Budget added successfully! Refresh to see changes.");
      modal.style.display = 'none';
    } else alert("Failed to add budget.");
  })
  .catch(err => console.error(err));
});
