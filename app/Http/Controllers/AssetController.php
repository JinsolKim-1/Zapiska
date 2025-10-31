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
    // All assets view (role-based)
    public function assets()
    {
        $user = Auth::user();
        $companyId = $user->company_id;

        if ($user->role->category === 'admin') {
            // Admin sees all assets
            $assets = Asset::with(['category'])->where('company_id', $companyId)->get();
            $view = 'users.assets.admin-assets';
        } else {
            // Manager & Employee see assets via DepartmentAsset
            $query = DepartmentAsset::with('asset')->where('company_id', $companyId);

            if ($user->role->category === 'manager') {
                // Manager sees assets in their sector
                $query->where('sector_id', $user->sector_id);
            } else {
                // Employee sees assets assigned to them
                $query->where('assigned_by', $user->user_id);
            }

            $assets = $query->get();
            $view = 'users.assets.manager-employee-assets';
        }

        $categories = AssetCategory::where('company_id', $companyId)->get();

        return view($view, compact('assets', 'categories'));
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

        // Orders can still be filtered by the user if needed
        $orders = Order::with('vendor')
                    ->where('company_id', $user->company_id) // optional
                    ->orderBy('order_date', 'desc')
                    ->get();

        $selectedInventoryId = $request->query('inventory_id');

        return view('users.assets.orderForm', compact('assets', 'inventories', 'vendors', 'orders', 'selectedInventoryId'));
    }

}
