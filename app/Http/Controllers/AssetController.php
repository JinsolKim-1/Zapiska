<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\DepartmentAsset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Inventory;
use App\Models\Vendor;
use App\Models\Order;

class AssetController extends Controller
{
    public function assets()
    {
        $user = Auth::user();
        $companyId = $user->company_id;

        // Fetch all asset categories
        $categories = AssetCategory::where('company_id', $companyId)->get();

        // Fetch all assets (admins see all)
        $assets = Asset::with(['category', 'sector', 'user'])
            ->where('company_id', $companyId)
            ->get();

        // Fetch sectors for assigning (if needed in admin-assets view)
        $sectors = \App\Models\Sector::where('company_id', $companyId)->get();

        return view('users.assets.admin-assets', compact('assets', 'categories', 'sectors'));
    }

    /**
     * Optional: Show order form
     */
    public function orderForm(Request $request)
    {
        $user = Auth::user();

        // Optional preselected inventory item
        $inventoryItem = null;
        if ($request->has('inventory_id')) {
            $inventoryItem = Inventory::find($request->inventory_id);
        }

        // Get all vendors, assets, and inventory items
        $vendors = Vendor::where('company_id', $user->company_id)->get();
        $assets = Asset::where('company_id', $user->company_id)->get();
        $inventory = Inventory::where('company_id', $user->company_id)->get();
        $requests = Order::with(['vendor', 'category'])
                    ->where('company_id', $user->company_id)
                    ->orderBy('order_date', 'desc')
                    ->get();

        $categories = AssetCategory::where('company_id', $user->company_id)->get();

        return view('users.assets.orderForm', compact(
            'inventoryItem', 'vendors', 'assets', 'inventory', 'requests', 'categories'
        ));
    }


    // Add new category
    public function addCategory(Request $request)
    {
        $request->validate([
            'category_name' => 'required|string|max:100',
        ]);

        AssetCategory::create([
            'company_id' => Auth::user()->company_id,
            'category_name' => $request->category_name,
            'created_by' => Auth::id(),
        ]);

        return redirect()->back()->with('success', 'Category added successfully!');
    }

    // Admin-specific assets (optional)
    public function adminAssets()
    {
        $companyId = Auth::user()->company_id;

        $assets = Asset::with('category')
            ->where('company_id', $companyId)
            ->get();

        $categories = AssetCategory::where('company_id', $companyId)->get();

        return view('users.assets.admin-assets', compact('assets', 'categories'));
    }

    // Manager-specific assets
    public function managerAssets()
    {
        $user = Auth::user();
        $companyId = $user->company_id;

        $assets = DepartmentAsset::with('asset')
            ->where('company_id', $companyId)
            ->where('sector_id', $user->sector_id)
            ->get();

        $categories = AssetCategory::where('company_id', $companyId)->get();

        return view('users.assets.manager-assets', compact('assets', 'categories'));
    }

    // Employee-specific assets
    public function employeeAssets()
    {
        $user = Auth::user();
        $companyId = $user->company_id;

        $assets = DepartmentAsset::with('asset')
            ->where('company_id', $companyId)
            ->where('assigned_by', $user->user_id)
            ->get();

        $categories = AssetCategory::where('company_id', $companyId)->get();

        return view('users.assets.employee-assets', compact('assets', 'categories'));
    }
    
    public function showOrderForm(Request $request)
    {
        $user = Auth::user();

        // Get all assets and inventories from the same company
        $assets = Asset::where('company_id', $user->company_id)->get();
        $inventories = Inventory::where('company_id', $user->company_id)->get();
        $vendors = Vendor::where('company_id', $user->company_id)->get();

        $categories = AssetCategory::where('company_id', $user->company_id)->get();
        // Orders can still be filtered by the user if needed
        $orders = Order::with('vendor','category')
                    ->where('company_id', $user->company_id) // optional
                    ->orderBy('order_date', 'desc')
                    ->get();

        $selectedInventoryId = $request->query('inventory_id');

        return view('users.assets.orderForm', compact('assets', 'inventories', 'vendors', 'orders','categories', 'selectedInventoryId'));
    }

    public function updateSector(Request $request, Asset $asset)
    {
        $request->validate([
            'sector_id' => 'required|exists:sectors,sector_id',
        ]);

        $asset->update([
            'sector_id' => $request->sector_id
        ]);

        return redirect()->back()->with('success', 'Asset sector updated!');
    }

    public function updateStatus(Request $request, Asset $asset)
    {
        // Validate using asset_status
        $request->validate([
            'asset_status' => 'required|in:available,in_use,maintenance,disposed',
            'sector_id'    => 'sometimes|nullable|exists:sectors,sector_id'
        ]);

        // If a sector is being assigned, update it
        if ($request->has('sector_id') && $request->sector_id) {
            $asset->sector_id = $request->sector_id;

            // If current status is 'available', auto-set to 'in_use'
            if ($asset->asset_status === 'available') {
                $asset->asset_status = 'in_use';
            }
        }

        // If JS sends asset_status, update it
        if ($request->has('asset_status')) {
            $asset->asset_status = $request->asset_status;
        }

        $asset->save();

        return response()->json([
            'success' => true,
            'message' => 'Asset updated successfully.',
            'asset_status' => $asset->asset_status
        ]);
    }
}
