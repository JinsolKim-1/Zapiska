<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users | Zapiska</title>
    @vite(['resources/css/users.css'])
</head>
<body>
    @include('users.includes.mainsidebar')

    <main class="main-content">
        <!-- Dashboard Header -->
        <header class="dashboard-header">
            <h1>Users & Assigned Departments</h1>
            <div class="user-info">
                <div class="user-details">
                    <div class="user-name">{{ Auth::user()->username }}</div>
                    <span class="role">{{ Auth::user()->role->role_name }}</span>
                </div>
                <div class="avatar">
                    <i class="bx bxs-user"></i>
                </div>
            </div>
        </header>

        <!-- Search & Filter + Add Sector -->
        <div class="users-controls" style="display:flex; align-items:center; gap:1rem; margin-bottom:1rem;">
            <input type="text" id="searchInput" placeholder="Search sectors...">
            <select id="filterSelect">
                <option value="all">All Sectors</option>
                @foreach($sectors as $sector)
                    <option value="{{ $sector->sector_id }}">{{ $sector->department_name }}</option>
                @endforeach
            </select>

            <button class="add-sector-btn" onclick="showAddSectorModal()">+ Add Sector</button>
        </div>

        <!-- Sectors Grid -->
        <section class="departments-grid" id="departmentsGrid">
            @foreach($sectors as $sector)
            <div class="department-card" data-sector="{{ $sector->sector_id }}">
                <div class="sector-details">
                    <p><strong>Sector Name:</strong><span class="sector-name">{{ $sector->department_name }}</span></p>
                    <p><strong>Manager:</strong> {{ $sector->manager->username ?? 'N/A' }}</p>
                    <p><strong>Created At:</strong> {{ $sector->created_at ? $sector->created_at->format('F d, Y') : 'N/A' }}</p>
                    <p class="user-count">Users: {{ $sector->users->count() }} Members</p>
                </div>

                @if($sector->users->count() > 0)
                <div class="users-list">
                    <strong>Users:</strong>
                    @foreach($sector->users as $user)
                        <div class="user-item">
                            {{ $user->username }} ({{ $user->role->role_name ?? 'No role' }})
                        </div>
                    @endforeach
                </div>
                @endif

                <button class="add-user-btn" data-sector="{{ $sector->sector_id }}">+ Add User</button>
            </div>
            @endforeach
        </section>

        <!-- Add Sector Modal -->
        <div id="addSectorModal" class="modal"">
            <div class="modal-content">
                <span class="close-btn" onclick="closeAddSectorModal()">&times;</span>
                <h2>Add New Sector</h2>
                <form action="{{ route('users.addSector') }}" method="POST">
                    @csrf
                    <div>
                        <label for="department_name">Department Name</label>
                        <input type="text" id="department_name" name="department_name" required>
                    </div>

                    <div>
                        <label for="manager_id">Assign Manager (optional)</label>
                        <select id="manager_id" name="manager_id">
                            <option value="">-- Select Manager --</option>
                                @foreach($managers as $manager)
                                    <option value="{{ $manager->user_id }}">{{ $manager->username }}</option>
                                @endforeach
                        </select>
                    </div>

                    <button type="submit">Add Sector</button>
                </form>
            </div>
        </div>

    </main>

    <script>
        // Toggle department card expansion
        const sectorCards = document.querySelectorAll('.department-card');
        sectorCards.forEach(card => {
            card.addEventListener('click', (e) => {
                if (!e.target.classList.contains('add-user-btn')) {
                    card.classList.toggle('expanded');
                }
            });
        });

        // Filter sectors by search or dropdown
        const searchInput = document.getElementById('searchInput');
        const filterSelect = document.getElementById('filterSelect');
        const departmentsGrid = document.getElementById('departmentsGrid');

        searchInput.addEventListener('input', filterSectors);
        filterSelect.addEventListener('change', filterSectors);

        function filterSectors() {
            const searchValue = searchInput.value.toLowerCase();
            const filterValue = filterSelect.value;

            const cards = departmentsGrid.querySelectorAll('.department-card');
            cards.forEach(card => {
                const name = card.querySelector('.sector-name').textContent.toLowerCase();
                const matchesFilter = filterValue === 'all' || card.dataset.sector === filterValue;
                const matchesSearch = name.includes(searchValue);

                card.style.display = (matchesFilter && matchesSearch) ? 'flex' : 'none';
            });
        }

        // Placeholder for Add User button
        function addUser(sectorId) {
            alert('Add user to sector ID: ' + sectorId);
        }

        // Modal functions
        const modal = document.getElementById('addSectorModal');

        function showAddSectorModal() {
            modal.classList.add('active');
        }

        function closeAddSectorModal() {
            modal.classList.remove('active');
        }

        window.onclick = function(event) {
            if (event.target == modal) {
                modal.classList.remove('active');
            }
        }
        document.querySelectorAll('.add-user-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const sectorId = e.target.dataset.sector;
                window.location.href = `/users/sector/${sectorId}/users`; 
            });
        });
    </script>
</body>
</html>
