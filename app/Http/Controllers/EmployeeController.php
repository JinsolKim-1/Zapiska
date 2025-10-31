<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use App\Models\Receipt;
use Illuminate\Http\Request;
use App\Models\AssetCategory;
use App\Models\Asset;

class EmployeeController extends Controller
{
    
    public function dashboard()
    {
        $employee = Auth::user();
        $companyId = $employee->company_id;

        // Example: get all asset categories for company
        $categories = AssetCategory::where('company_id', $companyId)->get();

        // Example: get all assets for the company
        $inventory = Asset::with('category')->where('company_id', $companyId)->get();

        // You can also fetch other data for your dashboard/analytics
        return view('users.dashboards.employee-dashboard', compact('employee', 'categories', 'inventory'));
    }

    public function inventory()
    {
        $companyId = Auth::user()->company_id;

        // Get all asset categories for the company
        $categories = AssetCategory::where('company_id', $companyId)->get();

        // Get all assets under the same company
        $inventory = Asset::with('category')
            ->where('company_id', $companyId)
            ->get();

        return view('users.inventory', compact('categories', 'inventory'));
    }

    public function requests() {
        return view('users.employee-requests');
    }

    public function receipts()
    {
        $employee = Auth::user();

        // Employee sees only their own receipts
        $receipts = Receipt::where('requested_by', $employee->id)->get();

        return view('users.receipts', compact('receipts'));
    }

}
