<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Order Form | Zapiska</title>
@vite(['resources/css/inventory.css','resources/js/orderForm.js'])
<meta name="csrf-token" content="{{ csrf_token() }}">
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

            <!-- Category (Dropdown) -->
            <label for="categoryId">Category</label>
            <select id="categoryId" name="asset_category_id" required>
                <option value="">Select category</option>
                @foreach($categories as $category)
                    <option value="{{ $category->asset_category_id }}">{{ $category->category_name }}</option>
                @endforeach
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
                <select name="vendor_id" id="vendor_id" class="form-control">
                    <option value="">Select Supplier</option>
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
                <form id="addVendorForm" data-store-url="{{ route('users.vendors.store') }}">
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
            <th>Category</th>
            <th>Type</th>
            <th>Vendor</th>
            <th>Quantity</th>
            <th>Unit Cost</th>
            <th>Total Cost</th>
            <th>Status</th>
            <th>Order Date</th>
            <th>Delivered Date</th>
        </tr>
        </thead>
        <tbody>
        @foreach($orders as $order)
        <tr data-order-id="{{ $order->orders_id }}" data-type="{{ $order->item_type ?? 'asset' }}">
            <td>{{ $order->item_name ?? 'N/A' }}</td>
            <td>{{ $order->category->category_name ?? 'N/A' }}</td>
            <td>{{ $order->item_type ?? 'N/A' }}</td>
            <td>{{ $order->vendor->vendor_name ?? 'N/A' }}</td>
            <td>{{ $order->quantity }}</td>
            <td>$ {{ number_format($order->unit_cost, 2) }}</td>
            <td>$ {{ number_format($order->total_cost, 2) }}</td>
            <td>
                <select class="order-status-dropdown" data-order-id="{{ $order->orders_id }}">
                    <option value="pending" {{ $order->order_status == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="shipped" {{ $order->order_status == 'shipped' ? 'selected' : '' }}>Shipped</option>
                    <option value="delivered" {{ $order->order_status == 'delivered' ? 'selected' : '' }}>Delivered</option>
                    <option value="cancelled" {{ $order->order_status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </td>
            <td>{{ $order->order_date ? \Carbon\Carbon::parse($order->order_date)->format('Y-m-d') : 'N/A' }}</td>
            <td class="delivered-date-cell">
                {{ $order->delivered_at ? \Carbon\Carbon::parse($order->delivered_at)->format('M d, Y h:i A') : 'Not Delivered' }}
            </td>
        </tr>
        @endforeach
        </tbody>
        </table>
        </section>
    </main>
</body>
</html>
