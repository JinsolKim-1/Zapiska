<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Vendor;
use App\Models\Inventory;
use App\Models\Asset;
use App\Models\AssetRequest;
use App\Models\Receipt;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;

class OrderController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Fetch orders created by this user
        $orders = Order::with(['vendor', 'request'])
            ->where('created_by', $user->user_id)
            ->get();

        // Fetch assets and inventories for the order form dropdown
        $assets = Asset::all(); // optionally filter by company
        $inventories = Inventory::all(); // optionally filter by company

        // Fetch vendors for this user's company
        $vendors = Vendor::where('company_id', $user->company_id)->get();

        return view('users.assets.orderForm', compact('orders', 'assets', 'inventories', 'vendors'));
    }

    public function create($itemType, $itemId)
    {
        $vendors = Vendor::where('company_id', Auth::user()->company_id)->get();

        // Determine the item (Inventory or Asset)
        $item = $itemType === 'inventory' 
            ? Inventory::findOrFail($itemId) 
            : Asset::findOrFail($itemId);

        return view('users.orders.create', compact('vendors', 'item', 'itemType'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'company_id' => 'required|exists:companies,company_id',
            'vendor_id' => 'required|exists:vendors,vendor_id',
            'item_name' => 'required|string|max:255', 
            'item_type' => 'required|string|in:asset,inventory',
            'quantity' => 'required|integer|min:1',
            'unit_cost' => 'required|numeric|min:0',
            'purpose' => 'nullable|string'
        ]);

        $user = Auth::user();
        $sectorId = $user->sector_id ?? null; 
        $itemName = $request->item_name;

        $assetRequest = AssetRequest::create([
            'company_id' => $user->company_id,
            'sector_id' => $sectorId,
            'user_id' => $user->user_id,
            'asset_name' => $itemName,
            'quantity' => $request->quantity,
            'purpose' => $request->purpose ?: null,
            'request_type' => 'common',
            'manager_approval' => $user->role->category === 'employee' ? 'pending' : 'approved',
            'admin_approval' => in_array($user->role->category, ['employee', 'manager']) ? 'pending' : 'approved',
            'final_status' => 'pending'
        ]);

        $order = Order::create([
            'company_id' => $user->company_id,
            'vendor_id' => $request->vendor_id,
            'requests_id' => $assetRequest->requests_id,
            'created_by' => $user->user_id,
            'item_name' => $itemName,
            'item_type' => $request->item_type,
            'quantity' => $request->quantity,
            'unit_cost' => $request->unit_cost,
            'order_status' => 'pending',
            'order_date' => now()
        ]);

        if ($user->role->category === 'admin') {
            Receipt::create([
                'company_id' => $user->company_id,
                'requests_id' => $assetRequest->requests_id,
                'sector_id' => $sectorId, 
                'user_id' => $user->user_id,
                'asset_name' => $itemName,
                'quantity' => $order->quantity,
                'total_cost' => $order->quantity * $order->unit_cost,
                'approved_by' => $user->username,
                'receipt_number' => 'REC-' . strtoupper(Str::random(8)),
                'request_status' => 'approved',
                'receipt_date' => now(),
                'verification_code' => Str::random(12)
            ]);
        }

        return redirect()->route('users.orders.index')->with('success', 'Order placed successfully!');
    }

    public function updateStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        // Validate input
        $request->validate([
            'order_status' => 'required|string|in:pending,shipped,delivered,cancelled',
        ]);

        $oldStatus = $order->order_status;
        $newStatus = $request->order_status;

        // Update order status
        $order->order_status = $newStatus;

        // Handle delivered state
        if ($newStatus === 'delivered') {
            $order->delivered_at = now();
            $order->save();

            // Check if there's an associated asset request
            if ($order->asset_request) {
                if ($order->asset_request->request_type === 'asset') {
                    Asset::create([
                        'company_id' => $order->asset_request->company_id,
                        'user_id' => $order->asset_request->user_id,
                        'asset_description' => $order->item_name,
                        'purchase_date' => now(),
                        'purchase_cost' => $order->unit_cost,
                        'location' => 'Warehouse',
                        'asset_status' => 'available'
                    ]);
                } else {
                    Inventory::create([
                        'company_id' => $order->asset_request->company_id,
                        'item_name' => $order->item_name,
                        'quantity' => $order->quantity,
                        'unit_price' => $order->unit_cost,
                        'added_on' => now()
                    ]);
                }
            }
        } else {
            // Reset delivered_at if reverting back
            if ($oldStatus === 'delivered' && $newStatus !== 'delivered') {
                $order->delivered_at = null;
            }
            $order->save();
        }

        return response()->json([
            'success' => true,
            'message' => "Order #{$order->orders_id} status updated to '{$newStatus}'.",
            'delivered_at' => $order->delivered_at ? $order->delivered_at->format('Y-m-d H:i:s') : null
        ]);
    }
}
