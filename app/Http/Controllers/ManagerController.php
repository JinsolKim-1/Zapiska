<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use App\Models\Receipt;
use Illuminate\Http\Request;
use App\Models\AssetCategory;
use App\Models\Asset;

class ManagerController extends Controller
{
    public function dashboard() {
        return view('users.dashboards.manager-dashboard');
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
        return view('users.manager-requests');
    }

    public function analytics() {
        return view('users.Maindashboard',['dashboard' => 'analytics']); 
    }

    public function receipts()
    {
        $manager = Auth::user();

        // Assuming each user has a `sector_id` or `department_id`
        $receipts = Receipt::where('sector_id', $manager->sector_id)
            ->orWhere('requested_by', $manager->id) // if manager requested from admin
            ->get();

        return view('users.receipts', compact('receipts'));
    }

}
