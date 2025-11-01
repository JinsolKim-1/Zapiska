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
                const card = document.querySelector(`.inventory-card[data-id='${currentInventoryId}']`);
                const params = new URLSearchParams({
                    item_name: card.querySelector('strong').textContent,
                    asset_category_id: card.dataset.categoryId,
                    unit_cost: card.dataset.unitCost,
                    supplier_id: card.dataset.supplierid || '',
                    item_type: 'inventory',
                });
                orderBtn.href = `/users/assets/order-form?${params.toString()}`;
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