document.addEventListener('DOMContentLoaded', () => {
    /* ================================
       1. AUTO TOTAL COST CALCULATION
    ================================ */
    const unitCostInput = document.getElementById('unitCost');
    const quantityInput = document.getElementById('quantity');
    const totalCostInput = document.getElementById('totalCost');

    function updateTotal() {
        const qty = parseFloat(quantityInput?.value) || 0;
        const cost = parseFloat(unitCostInput?.value) || 0;
        if (totalCostInput) totalCostInput.value = (qty * cost).toFixed(2);
    }

    if (unitCostInput && quantityInput) {
        unitCostInput.addEventListener('input', updateTotal);
        quantityInput.addEventListener('input', updateTotal);
    }

    /* ================================
       1b. AUTOFILL ORDER FORM FROM URL
    ================================ */
    const urlParams = new URLSearchParams(window.location.search);
    const itemName = urlParams.get('item_name');
    const unitCost = urlParams.get('unit_cost');
    const categoryId = urlParams.get('asset_category_id');
    const vendorId = urlParams.get('vendor_id');

    const itemNameInput = document.getElementById('itemName');
    if (itemNameInput && itemName) itemNameInput.value = itemName;

    if (unitCostInput && unitCost) unitCostInput.value = parseFloat(unitCost).toFixed(2);
    if (quantityInput) quantityInput.value = 1;

    if (totalCostInput && unitCostInput && quantityInput) updateTotal();

    const categorySelect = document.getElementById('categorySelect');
    if (categorySelect && categoryId) categorySelect.value = categoryId;

    const vendorSelectEl = document.getElementById('vendorId');
    if (vendorSelectEl && vendorId) vendorSelectEl.value = vendorId;

    /* ================================
       2. ADD VENDOR VIA MODAL (AJAX)
    ================================ */
    const modal = document.getElementById('addVendorModal');
    const addVendorBtn = document.getElementById('addVendorBtn');
    const closeBtn = modal?.querySelector('.close');
    const form = document.getElementById('addVendorForm');
    const vendorSelect = document.getElementById('vendorId');

    const showModal = () => { if(modal) modal.style.display = 'block'; };
    const closeModal = () => { if(modal) modal.style.display = 'none'; };

    addVendorBtn?.addEventListener('click', showModal);
    closeBtn?.addEventListener('click', closeModal);
    window.addEventListener('click', e => { if (e.target === modal) closeModal(); });

    form?.addEventListener('submit', async e => {
        e.preventDefault();

        if (!form) return;

        const payload = {
            vendor_name: form.vendor_name.value.trim(),
            contact_person: form.contact_person.value.trim(),
            email: form.email.value.trim(),
            phone: form.phone.value.trim(),
            address: form.address.value.trim()
        };

        try {
            const res = await fetch(form.dataset.storeUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(payload)
            });

            const data = await res.json();

            if (res.ok && data.success) {
                const newOption = new Option(data.vendor_name, data.vendor_id);
                if (vendorSelect) {
                    vendorSelect.appendChild(newOption);
                    vendorSelect.value = data.vendor_id;
                }

                alert('✅ Vendor added successfully!');
                form.reset();
                closeModal();
            } else {
                const errors = data.errors ? Object.values(data.errors).flat().join('\n') : (data.message || 'Failed to add vendor.');
                alert('⚠️ ' + errors);
            }
        } catch (error) {
            console.error('Vendor add error:', error);
            alert('❌ An error occurred while adding the vendor.');
        }
    });

    /* ================================
       3. ORDER STATUS UPDATE (AJAX)
    ================================ */
    function setDropdownColor(select) {
        const status = select.value;
        select.classList.remove('pending', 'shipped', 'delivered', 'cancelled');
        select.classList.add(status);
    }

    function setRowColor(tr, status) {
        tr.classList.remove('pending', 'shipped', 'delivered', 'cancelled');
        tr.classList.add(status);
    }

    document.querySelectorAll('.order-status-dropdown').forEach(select => {
        const tr = select.closest('tr');
        if (!tr) return;

        setDropdownColor(select);
        setRowColor(tr, select.value);

        select.addEventListener('change', async () => {
            const orderId = tr.dataset.orderId;
            const url = `/users/orders/${orderId}/update-status`;
            const newStatus = select.value;
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            const deliveredCell = tr.querySelector('.delivered-date-cell');

            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({ order_status: newStatus })
                });

                let data;
                try { data = await response.json(); } catch { data = {}; }

                if (response.ok) {
                    setDropdownColor(select);
                    setRowColor(tr, newStatus);

                    if (deliveredCell) {
                        switch(newStatus) {
                            case 'delivered': deliveredCell.textContent = new Date().toLocaleString(); break;
                            case 'shipped': deliveredCell.textContent = 'Package on the way'; break;
                            case 'cancelled': deliveredCell.textContent = 'Cancelled'; break;
                            default: deliveredCell.textContent = 'Not Delivered'; break;
                        }
                    }

                    alert(`✅ Order status updated to "${newStatus}"`);
                } else {
                    alert('⚠️ ' + (data.message || 'Failed to update order status.'));
                }
            } catch (err) {
                console.error('Error updating order:', err);
                alert('❌ Something went wrong.');
            }
        });
    });
});
