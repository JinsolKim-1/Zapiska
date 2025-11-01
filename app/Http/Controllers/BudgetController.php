<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sector;
use App\Models\SectorBudget;
use App\Models\Asset;
use Illuminate\Support\Facades\Auth;

class BudgetController extends Controller
{
    // Display dashboard
    public function index()
    {
        $sectors = Sector::with('budget')->get();
        $assetsPerSector = Asset::selectRaw('sector_id, COUNT(*) as total_assets')
                                ->groupBy('sector_id')
                                ->pluck('total_assets', 'sector_id');

        $budgets = $sectors->map(function($sector) use ($assetsPerSector) {
            $used = $sector->budget->used_budget ?? 0;
            $total = $sector->budget->total_budget ?? 0;
            return [
                'sector_name' => $sector->sector_name,
                'total_assets' => $assetsPerSector[$sector->sector_id] ?? 0,
                'used_budget' => $used,
                'total_budget' => $total,
                'percent_used' => $total ? round($used / $total * 100, 2) : 0,
                'remaining_budget' => $total - $used
            ];
        });

        return view('users.departments.dashboard', compact('budgets', 'sectors'));
    }

    // Assign/Update Budget
    public function updateBudget(Request $request, $sector_id)
    {
        $request->validate([
            'total_budget' => 'required|numeric|min:0',
        ]);

        $budget = SectorBudget::updateOrCreate(
            ['sector_id' => $sector_id, 'company_id' => Auth::user()->company_id],
            ['total_budget' => $request->total_budget]
        );

        return response()->json(['success' => true, 'message' => 'Budget updated successfully', 'budget' => $budget]);
    }
}
