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
use App\Models\AssetCategory;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // Fetch orders created by this user
        $orders = Order::with(['vendor', 'request', 'category'])
            ->where('created_by', $user->user_id)
            ->orderBy('order_date', 'desc')
            ->get();

        // Fetch dropdown data
        $assets = Asset::all(); 
        $inventories = Inventory::with('category')->get(); 
        $vendors = Vendor::where('company_id', $user->company_id)->get();
        $categories = AssetCategory::all();

        // Prefill form from query parameters if coming from inventory card or asset link
        $prefill = [
            'item_name' => $request->query('item_name', ''),
            'asset_category_id' => $request->query('asset_category_id', ''),
            'unit_cost' => $request->query('unit_cost', ''),
            'vendor_id' => $request->query('vendor_id', ''),
            'item_type' => $request->query('item_type', ''),
        ];

        return view('users.assets.orderForm', compact(
            'orders', 'assets', 'inventories', 'vendors', 'categories', 'prefill'
        ));
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

        // Get vendor instance for supplier name
        $vendor = Vendor::find($request->vendor_id);

        // Create an AssetRequest for record tracking
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

        // Create Order with vendor + supplier info
        $order = Order::create([
            'company_id' => $user->company_id,
            'vendor_id' => $vendor->vendor_id,
            'requests_id' => $assetRequest->requests_id,
            'created_by' => $user->user_id,
            'item_name' => $itemName,
            'item_type' => $request->item_type,
            'asset_category_id' => $request->asset_category_id,
            'quantity' => $request->quantity,
            'unit_cost' => $request->unit_cost,
            'order_status' => 'pending',
            'order_date' => now(),
        ]);

        // Optional: if the user is admin, generate a receipt immediately
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
        $order = Order::with(['vendor', 'request'])->findOrFail($id);

        // Prevent editing already delivered orders
        if ($order->order_status === 'delivered') {
            return response()->json([
                'success' => false,
                'message' => 'Delivered orders cannot be modified.'
            ], 403);
        }

        $request->validate([
            'order_status' => 'required|string|in:pending,shipped,delivered,cancelled',
        ]);

        $oldStatus = $order->order_status;
        $newStatus = $request->order_status;

        $order->order_status = $newStatus;

        if ($newStatus === 'delivered') {
            $order->delivered_at = now();
            $order->save();

            // ✅ INVENTORY TYPE — Add or update inventory
            if ($order->item_type === 'inventory') {
                $query = Inventory::where('company_id', $order->company_id)
                    ->where('asset_name', $order->item_name);

                if ($order->vendor && $order->vendor->vendor_name) {
                    $query->where('supplier', $order->vendor->vendor_name);
                }

                $inventory = $query->first();

                if ($inventory) {
                    $inventory->quantity += $order->quantity;
                    $inventory->last_restock = now();
                    $inventory->save();
                } else {
                    Inventory::create([
                        'company_id' => $order->company_id,
                        'asset_category_id' => $order->asset_category_id,
                        'asset_name' => $order->item_name,
                        'description' => null,
                        'quantity' => $order->quantity,
                        'unit_cost' => $order->unit_cost,
                        'reorder_level' => 0,
                        'last_restock' => now(),
                        'supplier' => $order->vendor ? $order->vendor->vendor_name : 'N/A',
                    ]);
                }
            }

            // ✅ ASSET TYPE — Create new asset record linked to order
            if ($order->item_type === 'asset') {
                $existing = Asset::where('orders_id', $order->orders_id)->first();

                if (!$existing) {
                    Asset::create([
                        'company_id' => $order->company_id,
                        'orders_id' => $order->getKey(),
                        'user_id' => $order->created_by,
                        'asset_category_id' => $order->asset_category_id,
                        'asset_description' => $order->item_name,
                        'purchase_date' => now(),
                        'purchase_cost' => $order->unit_cost * $order->quantity,
                        'asset_status' => 'available',
                        'sector_id' => null, // admin assigns later
                    ]);
                }
            }
        } else {
            // If reverting from delivered, clear delivery date
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
