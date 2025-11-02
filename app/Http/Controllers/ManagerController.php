<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use App\Models\Receipt;
use Illuminate\Http\Request;
use App\Models\AssetCategory;
use App\Models\Asset;
use App\Models\SpecialRequest; 

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

        // Find the sector where this user is manager
        $sector = \App\Models\Sector::where('manager_id', $manager->user_id)->first();

        if (!$sector) {
            $receipts = collect();
            $message = 'You are not assigned to any department.';
        } else {
            $receipts = \App\Models\Receipt::where('sector_id', $sector->sector_id)
                ->orderBy('receipt_date', 'desc')
                ->get();

            $message = $receipts->isEmpty() ? 'No receipts found in your department.' : null;
        }

        return view('users.receipts', compact('receipts', 'sector', 'message'));
    }
    
    public function assets()
    {
        $user = Auth::user();
        $companyId = $user->company_id;

        // Get all categories for this company
        $categories = \App\Models\AssetCategory::where('company_id', $companyId)->get();

        // Get the sector you manage
        $managedSector = \App\Models\Sector::where('manager_id', $user->id)->first();

        // Get assets assigned to user or in the sector you manage
        $assets = \App\Models\Asset::with('category', 'sector')
            ->where('company_id', $companyId)
            ->where(function($query) use ($user, $managedSector) {
                $query->where('user_id', $user->id);
                if ($managedSector) {
                    $query->orWhere('sector_id', $managedSector->sector_id);
                }
            })
            ->get();

        // Group assets by category_id
        $categoryCounts = $assets->groupBy('asset_category_id')->map(fn($group) => $group->count());

        // Map categories to chart data
        $categoryChartData = $categories->map(function($cat) use ($categoryCounts) {
            return [
                'name' => $cat->category_name,
                'count' => $categoryCounts->get($cat->asset_category_id, 0)
            ];
        })->values()->toArray();

        // Prepare purchase cost ranges
        $costRanges = [
            '0-10k' => 0,
            '10k-50k' => 0,
            '50k-100k' => 0,
            '100k+' => 0,
        ];

        foreach ($assets as $asset) {
            $cost = $asset->purchase_cost ?? 0;
            if ($cost < 10000) $costRanges['0-10k']++;
            elseif ($cost < 50000) $costRanges['10k-50k']++;
            elseif ($cost < 100000) $costRanges['50k-100k']++;
            else $costRanges['100k+']++;
        }

        return view('users.assets.manager-employee-assets', compact(
            'categories', 'assets', 'categoryChartData', 'costRanges'
        ));
    }

    public function storeSpecialRequest(Request $request)
    {
        $request->validate([
            'special_asset' => 'required|string|max:255',
            'justification' => 'required|string',
            'sector_id'    => 'required|exists:sectors,sector_id',
            'company_id'   => 'required|exists:companies,company_id',
        ]);

        SpecialRequest::create([
            'special_asset' => $request->special_asset,
            'justification' => $request->justification,
            'sector_id'     => $request->sector_id,
            'company_id'    => $request->company_id,
            'admin_approve' => 'pending',
        ]);

        return redirect()->back()->with('success', 'Special request sent to admin for approval.');
    }

}
