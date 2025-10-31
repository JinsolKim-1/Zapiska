<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Order Form | Zapiska</title>
@vite(['resources/css/inventory.css'])
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
/* ================== FORM STYLING ================== */
form label { display:block; margin-top:15px; font-weight:600; }
form select, form input, form textarea { width:100%; padding:8px; margin-top:5px; border-radius:5px; border:1px solid #ccc; }
form button { padding:10px 15px; border:none; border-radius:5px; cursor:pointer; }

/* Vendor container */
.vendor-container { display:flex; align-items:center; gap:10px; }
.vendor-container button { background-color:#3b82f6; color:white; margin-top:0; }

/* Table styling */
table { width:100%; border-collapse:collapse; margin-top:30px; }
table th, table td { padding:12px 15px; border:1px solid #ddd; text-align:left; }
table th { background-color:#10b981; color:white; }
table tr:nth-child(even) { background-color:#f3f4f6; }
table tr:hover { background-color:#d1fae5; }

/* Modal styling */
.modal { display:none; position:fixed; z-index:999; left:0; top:0; width:100%; height:100%; overflow:auto; background-color: rgba(0,0,0,0.4); }
.modal-content { background-color:#fff; margin:10% auto; padding:20px; border-radius:10px; width:400px; position:relative; }
.close { color:#aaa; position:absolute; top:10px; right:15px; font-size:28px; font-weight:bold; cursor:pointer; }
.close:hover { color:black; }
</style>
</head>
<body>
@include('users.includes.mainsidebar')

<main class="main-content">
<header class="dashboard-header">
    <h1>Create New Order</h1>
    <div class="user-info">
        <div class="user-details">
            <div class="user-name">{{ Auth::user()->username }}</div>
            <span class="role">{{ Auth::user()->role->role_name }}</span>
        </div>
        <div class="avatar"><i class="bx bxs-user"></i></div>
    </div>
</header>

<section class="order-form-section">
<form id="orderForm" method="POST" action="{{ route('users.orders.store') }}">
    @csrf

    <!-- Item Type -->
    <label for="itemType">Item Type</label>
    <select id="itemType" name="item_type" required>
        <option value="">Select type</option>
        <option value="asset">Asset</option>
        <option value="inventory">Inventory</option>
    </select>

    <!-- Item Name (Manual Input) -->
    <label for="itemName">Item Name</label>
    <input type="text" id="itemName" name="item_name" placeholder="Enter item"  autocomplete="off" required>


        <!-- Assets -->
        @foreach($assets as $asset)
            <option data-type="asset" value="{{ $asset->asset_id }}" data-unit-cost="{{ $asset->purchase_cost }}">
                {{ $asset->asset_name }}
            </option>
        @endforeach
    </select>

    <!-- Vendor + Add Vendor -->
    <label for="vendorId">Vendor</label>
    <div class="vendor-container">
        <select id="vendorId" name="vendor_id" required>
            <option value="">Select vendor</option>
            @foreach($vendors as $vendor)
                <option value="{{ $vendor->vendor_id }}">{{ $vendor->vendor_name }}</option>
            @endforeach
        </select>
        <button type="button" id="addVendorBtn">+ Add Vendor</button>
    </div>

    <!-- Quantity -->
    <label for="quantity">Quantity</label>
    <input type="number" name="quantity" id="quantity" min="1" value="1" required>

    <!-- Unit Cost -->
    <label for="unitCost">Unit Cost</label>
    <input type="number" step="0.01" name="unit_cost" id="unitCost" min="0" required>

    <!-- Total Cost -->
    <label for="totalCost">Total Cost</label>
    <input type="number" step="0.01" id="totalCost" readonly>
    <input type="hidden" name="company_id" value="{{ Auth::user()->company_id }}">
    <button type="submit" style="background:#10b981; color:white; margin-top:10px;">Place Order</button>
</form>

<!-- Add Vendor Modal -->
<div id="addVendorModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h3>Add New Vendor</h3>
        <form id="addVendorForm">
            @csrf
            <label for="vendor_name">Vendor Name</label>
            <input type="text" name="vendor_name" id="vendor_name" required>

            <label for="contact_person">Contact Person</label>
            <input type="text" name="contact_person" id="contact_person">

            <label for="email">Email</label>
            <input type="email" name="email" id="email">

            <label for="phone">Phone</label>
            <input type="text" name="phone" id="phone">

            <label for="address">Address</label>
            <textarea name="address" id="address"></textarea>

            <button type="submit" style="background:#3b82f6; color:white; margin-top:10px;">Save Vendor</button>
        </form>
    </div>
</div>

<!-- Order History Table -->
<h2>Order History / Items</h2>
<table>
<thead>
<tr>
    <th>Item</th>
    <th>Type</th>
    <th>Vendor</th>
    <th>Quantity</th>
    <th>Unit Cost</th>
    <th>Total Cost</th>
    <th>Status</th>
    <th>Order Date</th>
</tr>
</thead>
<tbody>
@foreach($orders as $order)
<tr>
    <td>{{ $order->item_name ?? 'N/A' }}</td>
    <td>{{ $order->item_type ?? 'N/A' }}</td>
    <td>{{ $order->vendor->vendor_name ?? 'N/A' }}</td>
    <td>{{ $order->quantity }}</td>
    <td>{{ number_format($order->unit_cost, 2) }}</td>
    <td>{{ number_format($order->total_cost, 2) }}</td>
    <td>
        <select class="order-status-dropdown" data-order-id="{{ $order->orders_id }}">
            <option value="pending" {{ $order->order_status == 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="shipped" {{ $order->order_status == 'shipped' ? 'selected' : '' }}>Shipped</option>
            <option value="delivered" {{ $order->order_status == 'delivered' ? 'selected' : '' }}>Delivered</option>
            <option value="cancelled" {{ $order->order_status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
        </select>
    </td>
    <td>{{ $order->order_date ? \Carbon\Carbon::parse($order->order_date)->format('Y-m-d') : 'N/A' }}</td>
</tr>
@endforeach
</tbody>
</table>
</section>
</main>

<script>
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
       2. ADD VENDOR VIA MODAL (AJAX)
    ================================ */
    const modal = document.getElementById('addVendorModal');
    const addVendorBtn = document.getElementById('addVendorBtn');
    const closeBtn = modal?.querySelector('.close');
    const form = document.getElementById('addVendorForm');
    const vendorSelect = document.getElementById('vendorId');

    const showModal = () => modal.style.display = 'block';
    const closeModal = () => modal.style.display = 'none';

    addVendorBtn?.addEventListener('click', showModal);
    closeBtn?.addEventListener('click', closeModal);
    window.addEventListener('click', e => { if (e.target === modal) closeModal(); });

    form?.addEventListener('submit', async e => {
        e.preventDefault();

        const payload = {
            vendor_name: form.vendor_name.value.trim(),
            contact_person: form.contact_person.value.trim(),
            email: form.email.value.trim(),
            phone: form.phone.value.trim(),
            address: form.address.value.trim()
        };

        try {
            const res = await fetch("{{ route('users.vendors.store') }}", {
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
                // Add new vendor to dropdown
                const newOption = new Option(data.vendor_name, data.vendor_id);
                vendorSelect.appendChild(newOption);
                vendorSelect.value = data.vendor_id;

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
    const rows = document.querySelectorAll('table tbody tr');

    rows.forEach(row => {
        const statusCell = row.querySelector('td:nth-child(7)');
        const orderId = row.dataset.orderId; // Ensure Blade uses data-order-id="{{ $order->order_id }}"
        if (!statusCell || !orderId) return;

        const currentStatus = statusCell.textContent.trim().toLowerCase();
        const select = document.createElement('select');
        const statuses = ['pending', 'approved', 'delivered'];

        statuses.forEach(status => {
            const option = new Option(status.charAt(0).toUpperCase() + status.slice(1), status);
            if (status === currentStatus) option.selected = true;
            select.appendChild(option);
        });

        statusCell.textContent = ''; // Clear cell content
        statusCell.appendChild(select);

        select.addEventListener('change', async () => {
            const newStatus = select.value;
            select.disabled = true;

            try {
                const res = await fetch(`/users/orders/${orderId}/update-status`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ order_status: newStatus })
                });

                const data = await res.json();

                if (res.ok && data.success) {
                    alert(`✅ Order #${orderId} updated to "${newStatus}"`);
                } else {
                    alert('⚠️ ' + (data.message || 'Failed to update order status.'));
                    select.value = currentStatus; // revert if failed
                }
            } catch (err) {
                console.error('Update status error:', err);
                alert('❌ Error updating status. Check console for details.');
                select.value = currentStatus;
            } finally {
                select.disabled = false;
            }
        });
    });
});
</script>
</body>
</html>
