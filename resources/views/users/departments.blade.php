<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Departments | Zapiska</title>

  {{-- Pass PHP data to JS safely --}}
  <script>
    window.departmentsData = {
      names: @json($departments->pluck('name')),
      assetCounts: @json($departments->pluck('total_assets')),
      usedBudgets: @json($departments->pluck('used_budget')),
      csrfToken: "{{ csrf_token() }}"
    };
  </script>

  @vite(['resources/css/departments.css', 'resources/js/departments.js'])
</head>
<body>
  <div class="layout">
    @include('users.includes.mainsidebar')

    <main class="main-content">
      <header class="dashboard-header">
        <h1>Departments & Budget Overview</h1>
        <div class="user-info">
          <div class="user-details">
            <div class="user-name">{{ Auth::user()->username }}</div>
            <span class="role">{{ Auth::user()->role->role_name }}</span>
          </div>
          <div class="avatar"><i class="bx bxs-user"></i></div>
        </div>
      </header>

      <section class="dashboard-grid departments-grid">
        <!-- Charts -->
        <div class="card">
          <h3>No. of Assets per Department</h3>
          <canvas id="assetPieChart"></canvas>
        </div>

        <div class="card">
          <h3>Budget Used per Department</h3>
          <canvas id="budgetBarChart"></canvas>
        </div>

        <!-- Controls and Table -->
        <div class="card wide">
          <div class="table-controls">
            <input type="text" id="search-department" placeholder="Search Department...">
            <select id="filter-department">
              <option value="">Sort By</option>
              <option value="assets">Most Assets</option>
              <option value="budget-used">Highest Budget Used</option>
              <option value="budget-remaining">Highest Budget Remaining</option>
              <option value="percent-used">Highest % Budget Used</option>
            </select>
            <button id="add-budget-btn" class="add-budget-btn">Add Budget</button>
          </div>

          <div class="table-wrapper">
            <table id="department-table">
              <thead>
                <tr>
                  <th>Department</th>
                  <th>No. of Assets</th>
                  <th>Requisitions</th>
                  <th>Budget Used</th>
                  <th>% Budget Used</th>
                  <th>Budget Remaining</th>
                  <th>Total Budget</th>
                  <th>Update Time</th>
                </tr>
              </thead>
              <tbody>
                @foreach($departments as $dept)
                  @php
                    $used = $dept['used_budget'] ?? 0;
                    $total = $dept['total_budget'] ?? 0;
                    $percentUsed = ($total > 0) ? round(($used / $total) * 100, 2) : 0;
                    $remaining = $total - $used;
                  @endphp
                  <tr data-sector-id="{{ $dept['sector_id'] }}">
                    <td class="dept-name">{{ $dept['name'] }}</td>
                    <td class="dept-assets">{{ $dept['total_assets'] }}</td>
                    <td>{{ $dept['requisitions'] ?? 0 }}</td>
                    <td class="used-budget">{{ number_format($used, 2) }}</td>
                    <td class="percent-used">{{ $percentUsed }}%</td>
                    <td class="remaining-budget">{{ number_format($remaining, 2) }}</td>
                    <td class="total-budget">{{ number_format($total, 2) }}</td>
                    <td class="update-time">
                      {{ $dept['updated_at'] ? \Carbon\Carbon::parse($dept['updated_at'])->format('M d, Y h:i A') : '—' }}
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

  <!-- Modal -->
  <div id="budgetModal" class="modal">
    <div class="modal-content">
      <h3>Add Budget</h3>
      <select id="modal-department">
        <option value="">Select Department</option>
        @foreach($departments as $dept)
          <option value="{{ $dept['sector_id'] }}">{{ $dept['name'] }}</option>
        @endforeach
      </select>
      <input type="number" id="modal-budget" autocomplete="off" min="0" placeholder="Enter Budget Amount">
      <div>
        <button id="save-budget-btn">Save</button>
        <button class="close-modal">Cancel</button>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</body>
</html>
