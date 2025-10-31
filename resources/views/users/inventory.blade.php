<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory | Zapiska</title>
    @vite(['resources/css/inventory.css'])
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    @include('users.includes.mainsidebar')

    <main class="main-content">
        <!-- Dashboard Header -->
        <header class="dashboard-header">
            <h1>Inventory Overview</h1>
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

        <!-- Search & Filter -->
        <div class="inventory-controls">
            <input type="text" id="searchInput" placeholder="Search inventory...">
            <select id="categoryFilter">
                <option value="">All Categories</option>
                @foreach($categories as $category)
                    <option value="{{ $category->category_name }}">{{ $category->category_name }}</option>
                @endforeach
            </select>

            @if(Auth::user()->role && Auth::user()->role->category === 'admin')
                <button id="addInventoryBtn">+ Add Inventory</button>
            @endif
        </div>

        <!-- Inventory Grid -->
        <section class="inventory-grid" id="inventoryGrid">
            @foreach($inventory as $item)
                <div class="inventory-card" 
                    data-id="{{ $item->inventory_id }}"
                    data-description="{{ $item->description }}"
                    data-supplier="{{ $item->supplier }}"
                    data-last-restock="{{ $item->last_restock }}"
                    data-category-id="{{ $item->asset_category_id }}"
                    data-category="{{ $item->category->category_name ?? 'N/A' }}"
                    data-quantity="{{ $item->quantity }}"
                    data-unit-cost="{{ number_format($item->unit_cost,2) }}">
                    <strong>{{ $item->asset_name }}</strong>
                    <p>Category: {{ $item->category->category_name ?? 'N/A' }}</p>
                    <p>Qty: {{ $item->quantity }}</p>
                    <p>Unit: ${{ number_format($item->unit_cost,2) }}</p>
                </div>
            @endforeach
        </section>

        <!-- Add Inventory Modal -->
        @if(Auth::user()->role && Auth::user()->role->category === 'admin')
        <div id="addInventoryModal" class="modal">
            <div class="modal-content">
                <span class="close">&times;</span>
                <h3>Add New Inventory Item</h3>
                <form action="{{ route('users.inventory.store') }}" method="POST">
                    @csrf
                    <label for="assetName">Asset Name</label>
                    <input type="text" id="assetName" name="asset_name" required>

                    <label for="assetCategory">Category</label>
                    <select id="assetCategory" name="asset_category_id" required>
                        @foreach($categories as $category)
                            <option value="{{ $category->asset_category_id }}">{{ $category->category_name }}</option>
                        @endforeach
                    </select>

                    <label for="quantity">Quantity</label>
                    <input type="number" id="quantity" name="quantity" min="0" required>

                    <label for="unitCost">Unit Cost</label>
                    <input type="number" id="unitCost" step="0.01" name="unit_cost" min="0" required>

                    <label for="reorderLevel">Reorder Level</label>
                    <input type="number" id="reorderLevel" name="reorder_level" min="0">

                    <label for="supplier">Supplier</label>
                    <input type="text" id="supplier" name="supplier">

                    <label for="description">Description</label>
                    <input type="text" id="description" name="description" placeholder="Enter description">

                    <button type="submit">Add Inventory</button>
                </form>
            </div>
        </div>
        @endif

        <!-- Inventory Details Modal -->
        <div id="inventoryModal" class="modal">
            <div class="modal-content">
                <span class="close">&times;</span>
                <h2 id="modalAssetName"></h2>
                <p><strong>Category:</strong> <span id="modalCategory"></span></p>
                <p><strong>Description:</strong> <span id="modalDescription"></span></p>
                <p><strong>Quantity:</strong> <span id="modalQuantity"></span></p>
                <p><strong>Unit Cost:</strong> $<span id="modalUnitCost"></span></p>
                <p><strong>Supplier:</strong> <span id="modalSupplier"></span></p>
                <p><strong>Last Restock:</strong> <span id="modalLastRestock"></span></p>

                <div class="modal-actions">
                    @if(Auth::user()->role && Auth::user()->role->category === 'admin')
                        <button class="edit-btn">Edit</button>
                        <button class="restock-btn">Restock</button>
                        <button class="assign-btn">Assign</button>
                        <button class="delete-btn">Delete</button>
                        <a id="orderBtn" href="#" class="order-btn">Order</a>

                    @else
                        <button class="withdraw-btn" style="background:#3b82f6;">Withdraw</button>
                    @endif
                </div>
            </div>
        </div>

        <!-- Edit Inventory Modal -->
        <div id="editInventoryModal" class="modal">
            <div class="modal-content">
                <span class="close">&times;</span>
                <h3>Edit Inventory</h3>
                <form id="editInventoryForm">
                    <label for="editAssetName">Asset Name</label>
                    <input type="text" id="editAssetName" name="asset_name" required>

                    <label for="editCategory">Category</label>
                    <select id="editCategory" name="asset_category_id" required>
                        @foreach($categories as $category)
                            <option value="{{ $category->asset_category_id }}">{{ $category->category_name }}</option>
                        @endforeach
                    </select>

                    <label for="editQuantity">Quantity</label>
                    <input type="number" id="editQuantity" name="quantity" min="0" required>

                    <label for="editUnitCost">Unit Cost</label>
                    <input type="number" id="editUnitCost" step="0.01" name="unit_cost" min="0" required>

                    <label for="editReorderLevel">Reorder Level</label>
                    <input type="number" id="editReorderLevel" name="reorder_level" min="0">

                    <label for="editSupplier">Supplier</label>
                    <input type="text" id="editSupplier" name="supplier">

                    <label for="editDescription">Description</label>
                    <input type="text" id="editDescription" name="description" autocomplete="off">

                    <div class="modal-actions">
                        <button type="submit" style="background:#3b82f6">Save</button>
                        <button type="button" class="close" style="background:#6b7280">Cancel</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Withdraw Inventory Modal -->
        <div id="withdrawInventoryModal" class="modal">
            <div class="modal-content">
                <span class="close">&times;</span>
                <h3>Withdraw Inventory</h3>
                <p>How many items would you like to withdraw?</p>
                <input type="number" id="withdrawQuantity" min="1" placeholder="Enter quantity">
                <div class="modal-actions">
                    <button id="confirmWithdrawBtn" style="background:#3b82f6">Withdraw</button>
                    <button class="close" style="background:#6b7280">Cancel</button>
                </div>
            </div>
        </div>

        <!-- Restock Modal -->
        <div id="restockInventoryModal" class="modal">
            <div class="modal-content">
                <span class="close">&times;</span>
                <h3>Restock Inventory</h3>
                <input type="number" id="restockQuantity" placeholder="Enter restock quantity" min="1">
                <div class="modal-actions">
                    <button id="confirmRestockBtn" style="background:#10b981">Restock</button>
                    <button class="close" style="background:#6b7280">Cancel</button>
                </div>
            </div>
        </div>

        <!-- Assign Inventory Modal -->
        <div id="assignInventoryModal" class="modal">
            <div class="modal-content">
                <span class="close">&times;</span>
                <h3>Assign Inventory</h3>
                <input type="text" id="assignUserId" placeholder="Enter User ID">
                <div class="modal-actions">
                    <button id="confirmAssignBtn" style="background:#f59e0b">Assign</button>
                    <button class="close" style="background:#6b7280">Cancel</button>
                </div>
            </div>
        </div>

        <!-- Delete Inventory Modal -->
        <div id="deleteInventoryModal" class="modal">
            <div class="modal-content">
                <span class="close">&times;</span>
                <h3>Delete Inventory</h3>
                <p>Are you sure you want to delete <strong id="deleteAssetName"></strong>?</p>
                <div class="modal-actions">
                    <button id="confirmDeleteBtn" style="background:#ef4444">Delete</button>
                    <button class="close" style="background:#6b7280">Cancel</button>
                </div>
            </div>
        </div>
    </main>

    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const baseInventoryUrl = '/users/inventory';
        let currentInventoryId = null;

        // ==================== MODALS ====================
        const modals = {
            inventory: document.getElementById('inventoryModal'),
            add: document.getElementById('addInventoryModal'),
            edit: document.getElementById('editInventoryModal'),
            restock: document.getElementById('restockInventoryModal'),
            assign: document.getElementById('assignInventoryModal'),
            delete: document.getElementById('deleteInventoryModal'),
            withdraw: document.getElementById('withdrawInventoryModal'),
        };

        // Close modals
        document.querySelectorAll('.modal .close').forEach(btn =>
            btn.addEventListener('click', e => e.target.closest('.modal').classList.remove('show'))
        );

        Object.values(modals).forEach(modal => {
            if(!modal) return;
            modal.addEventListener('click', e => { if(e.target === modal) modal.classList.remove('show'); });
            modal.querySelector('.modal-content')?.addEventListener('click', e => e.stopPropagation());
        });

        // ==================== INVENTORY CARDS ====================
        const cards = document.querySelectorAll('.inventory-card');
        const orderBtn = document.getElementById('orderBtn');

        cards.forEach(card => {
            card.addEventListener('click', () => {
                currentInventoryId = card.dataset.id;

                document.getElementById('modalAssetName').textContent = card.querySelector('strong').textContent;
                document.getElementById('modalCategory').textContent = card.dataset.category;
                document.getElementById('modalQuantity').textContent = card.dataset.quantity;
                document.getElementById('modalUnitCost').textContent = card.dataset.unitCost;
                document.getElementById('modalDescription').textContent = card.dataset.description || 'N/A';
                document.getElementById('modalSupplier').textContent = card.dataset.supplier || 'N/A';
                document.getElementById('modalLastRestock').textContent = card.dataset.lastRestock || 'N/A';

                // Update Order button dynamically
                if(orderBtn) {
                    orderBtn.href = `/users/assets/order-form?inventory_id=${currentInventoryId}`;
                }

                modals.inventory.classList.add('show');
            });
        });

        // ==================== ADD INVENTORY ====================
        document.getElementById('addInventoryBtn')?.addEventListener('click', () => modals.add.classList.add('show'));

        // ==================== INVENTORY MODAL BUTTONS ====================
        const inventoryActions = modals.inventory.querySelector('.modal-actions');

        const editBtn = inventoryActions.querySelector('.edit-btn');
        const restockBtn = inventoryActions.querySelector('.restock-btn');
        const assignBtn = inventoryActions.querySelector('.assign-btn');
        const deleteBtn = inventoryActions.querySelector('.delete-btn');
        const requestsBtn = inventoryActions.querySelector('.view-requests-btn');
        const withdrawBtn = inventoryActions.querySelector('.withdraw-btn');

        // --- Edit ---
        editBtn?.addEventListener('click', () => {
            const card = document.querySelector(`.inventory-card[data-id='${currentInventoryId}']`);

            document.getElementById('editAssetName').value = card.querySelector('strong').textContent;
            document.getElementById('editCategory').value = card.dataset.categoryId || card.dataset.category;
            document.getElementById('editQuantity').value = card.dataset.quantity;
            document.getElementById('editUnitCost').value = card.dataset.unitCost;
            document.getElementById('editReorderLevel').value = card.dataset.reorderLevel || '';
            document.getElementById('editSupplier').value = card.dataset.supplier || '';
            document.getElementById('editDescription').value = card.dataset.description || '';

            modals.edit.classList.add('show');
        });

        document.getElementById('editInventoryForm')?.addEventListener('submit', e => {
            e.preventDefault();
            const formData = {
                asset_name: document.getElementById('editAssetName').value,
                asset_category_id: document.getElementById('editCategory').value,
                quantity: document.getElementById('editQuantity').value,
                unit_cost: document.getElementById('editUnitCost').value,
                reorder_level: document.getElementById('editReorderLevel').value,
                supplier: document.getElementById('editSupplier').value,
                description: document.getElementById('editDescription').value
            };

            fetch(`${baseInventoryUrl}/${currentInventoryId}`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify(formData)
            }).then(res => res.json()).then(() => location.reload());
        });

        // --- Restock ---
        restockBtn?.addEventListener('click', () => modals.restock.classList.add('show'));
        document.getElementById('confirmRestockBtn')?.addEventListener('click', () => {
            const qty = document.getElementById('restockQuantity').value;
            if(qty && qty > 0){
                fetch(`${baseInventoryUrl}/${currentInventoryId}/restock`, {
                    method:'PUT',
                    headers:{ 'Content-Type':'application/json', 'X-CSRF-TOKEN': csrfToken },
                    body: JSON.stringify({ quantity: qty })
                }).then(res => res.json()).then(() => location.reload());
            }
        });

        // --- Assign ---
        assignBtn?.addEventListener('click', () => modals.assign.classList.add('show'));
        document.getElementById('confirmAssignBtn')?.addEventListener('click', () => {
            const userId = document.getElementById('assignUserId').value.trim();
            if(userId){
                fetch(`${baseInventoryUrl}/${currentInventoryId}/assign`, {
                    method:'POST',
                    headers:{ 'Content-Type':'application/json', 'X-CSRF-TOKEN': csrfToken },
                    body: JSON.stringify({ user_id: userId })
                }).then(res => res.json()).then(() => location.reload());
            }
        });

        // --- Delete ---
        deleteBtn?.addEventListener('click', () => {
            modals.delete.classList.add('show');
            document.getElementById('deleteAssetName').textContent = document.getElementById('modalAssetName').textContent;
        });
        document.getElementById('confirmDeleteBtn')?.addEventListener('click', () => {
            fetch(`${baseInventoryUrl}/${currentInventoryId}`, {
                method:'DELETE',
                headers:{ 'X-CSRF-TOKEN': csrfToken }
            }).then(res => res.json()).then(() => location.reload());
        });

        // --- Withdraw ---
        withdrawBtn?.addEventListener('click', () => modals.withdraw.classList.add('show'));
        document.getElementById('confirmWithdrawBtn')?.addEventListener('click', () => {
            const qty = parseInt(document.getElementById('withdrawQuantity').value);
            if(qty && qty > 0){
                fetch(`${baseInventoryUrl}/${currentInventoryId}/withdraw`, {
                    method:'POST',
                    headers:{ 'Content-Type':'application/json', 'X-CSRF-TOKEN': csrfToken },
                    body: JSON.stringify({ quantity: qty })
                }).then(res => res.json()).then(data => {
                    alert(data.message);
                    location.reload();
                });
            }
        });

        // --- Requests ---
        requestsBtn?.addEventListener('click', () => {
            window.location.href = `/users/assets/order-form?inventory_id=${currentInventoryId}`;
        });


        // ==================== FILTER INVENTORY ====================
        const searchInput = document.getElementById('searchInput');
        const categoryFilter = document.getElementById('categoryFilter');
        const inventoryCards = document.querySelectorAll('.inventory-card');

        function filterInventory() {
            const searchTerm = searchInput.value.toLowerCase();
            const selectedCategory = categoryFilter.value;

            inventoryCards.forEach(card => {
                const name = card.querySelector('strong').textContent.toLowerCase();
                const category = card.dataset.category.toLowerCase();

                const matchesSearch = name.includes(searchTerm);
                const matchesCategory = selectedCategory === '' || category === selectedCategory.toLowerCase();

                card.style.display = (matchesSearch && matchesCategory) ? '' : 'none';
            });
        }

        searchInput.addEventListener('input', filterInventory);
        categoryFilter.addEventListener('change', filterInventory);
    });
    </script>


</body>
</html>
