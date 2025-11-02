<?php

namespace App\Http\Controllers;

use App\Mail\InvitationMail;
use App\Models\Asset;
use App\Models\Sector;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\AssetCategory;
use App\Models\Inventory;
use App\Models\Receipt;
use App\Models\AssetRequest;
use App\Models\Vendor;
use App\Models\SectorBudget;


class AdminController extends Controller
{
    public function departments()
    {
        $sectors = Sector::with('company')->get();

        // Count assets per sector
        $assetsPerSector = Asset::selectRaw('sector_id, COUNT(*) as total')
            ->groupBy('sector_id')
            ->pluck('total', 'sector_id');

        // Fetch budgets per sector
        $budgets = SectorBudget::all()->keyBy('sector_id');

        // Prepare data for table and charts
        $departments = $sectors->map(function ($sector) use ($assetsPerSector, $budgets) {
            $budget = $budgets->get($sector->sector_id);
            $used = $budget->used_budget ?? 0;
            $total = $budget->total_budget ?? 0;

            return [
                'name' => $sector->department_name,
                'sector_id' => $sector->sector_id,
                'total_assets' => $assetsPerSector[$sector->sector_id] ?? 0,
                'used_budget' => $used,
                'total_budget' => $total,
                'percent_used' => ($total > 0) ? round(($used / $total) * 100, 2) : 0,
                'remaining_budget' => $total - $used,
                'updated_at' => optional($budget)->updated_at,
            ];
        });

        return view('users.departments', [
            'departments' => $departments
        ]);
    }

    public function addBudget(Request $request)
    {
        $request->validate([
            'sector_id' => 'required|integer|exists:sectors,sector_id',
            'total_budget' => 'required|numeric|min:0',
        ]);

        $companyId = Auth::user()->company_id;

        // Check if a record already exists for this company + sector
        $existingBudget = SectorBudget::where('company_id', $companyId)
            ->where('sector_id', $request->sector_id)
            ->first();

        if ($existingBudget) {
            // Just update the total budget (add or replace, your choice)
            $existingBudget->total_budget += $request->total_budget;
            $existingBudget->save();
        } else {
            // Create new budget record
            SectorBudget::create([
                'company_id' => $companyId,
                'sector_id' => $request->sector_id,
                'total_budget' => $request->total_budget,
                'used_budget' => 0,
                'start_date' => now()->toDateString(),
                'end_date' => now()->addYear()->toDateString(),
            ]);
        }

    return response()->json(['success' => true]);
}


    public function assets()
    {
        $companyId = Auth::user()->company_id;

        $categories = AssetCategory::where('company_id', $companyId)->get();

        return view('users.assets', compact('categories'));
    }

    public function requests()
    {
        return view('users.requests');
    }

    public function receipts()
    {
        $receipts = Receipt::all(); // Admin sees all
        return view('users.receipts', compact('receipts'));
    }

    public function users()
    {
        $companyId = Auth::user()->company_id;

        // Eager load manager and role relationships for all sectors of this company
        $sectors = Sector::with(['manager', 'users.role'])
            ->where('company_id', $companyId)
            ->get();

        // Get all users with 'manager' category roles within this company
        $managers = User::where('company_id', $companyId)
            ->whereHas('role', function ($query) {
                $query->where('category', 'manager');
            })
            ->with('role') // Eager load role for display in dropdown
            ->get();

        // Return to view with both sectors and managers data
        return view('users.users', [
            'sectors' => $sectors,
            'managers' => $managers,
        ]);
    }



    public function addSector(Request $request)
    {
        $request->validate([
            'department_name' => 'required|string|max:150',
            'manager_id' => 'nullable|exists:users,user_id',
        ]);

        Sector::create([
            'company_id' => Auth::user()->company_id,
            'department_name' => $request->department_name,
            'manager_id' => $request->manager_id,
        ]);

        return redirect()->back()->with('success', 'Sector added successfully!');
    }

    public function addUser(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'sector_id' => 'required|exists:sectors,sector_id',
            'role_id' => 'required|exists:roles,role_id'
        ]);

        User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'company_id' => Auth::user()->company_id,
            'sector_id' => $request->sector_id,
            'role_id' => $request->role_id,
        ]);

        return redirect()->back()->with('success', 'User added successfully!');
    }

    public function sectorUsers($sectorId)
    {
        $companyId = Auth::user()->company_id;

        $sector = Sector::with('manager', 'users.role')
            ->where('company_id', $companyId)
            ->findOrFail($sectorId);

        return view('users.sectorUsers', compact('sector'));
    }

    public function addUserForm($sectorId)
    {
        $companyId = Auth::user()->company_id;

        // Ensure the sector belongs to the logged-in user's company
        $sector = Sector::where('company_id', $companyId)->findOrFail($sectorId);

        // Get all users in the company who are not yet assigned to a sector
        $availableUsers = User::where('company_id', $companyId)
            ->whereNull('sector_id') // not assigned to a sector yet
            ->get();

        // All roles for selection
        $roles = Role::all();

        return view('users.addUser', compact('sector', 'availableUsers', 'roles'));
    }


    public function kickUser($userId)
    {
        $companyId = Auth::user()->company_id;

        $user = User::where('company_id', $companyId)
            ->findOrFail($userId);

        $user->delete();

        return redirect()->back()->with('success', 'User removed successfully');
    }

    public function editManager($sectorId)
    {
        $sector = Sector::findOrFail($sectorId);

        // Get all managers in the same company
        $managers = User::where('company_id', Auth::user()->company_id)
                        ->whereHas('role', function($query) {
                            $query->where('category', 'manager');
                        })
                        ->get();

        return view('users.editManager', compact('sector', 'managers'));
    }

    public function assignUserToSector($sectorId, $userId, Request $request)
    {
        $companyId = Auth::user()->company_id;

        $user = User::where('company_id', $companyId)
            ->where('user_id', $userId)
            ->firstOrFail();

        $user->update([
            'sector_id' => $sectorId,
            'role_id' => $request->role_id,
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
        ]);

        return redirect()->back()->with('success', "{$user->username} assigned to sector successfully!");
    }

    public function invite()
    {
        return view('users.invite');
    }

    public function orderForm(Request $request)
    {
        // Optional: preselected inventory item
        $inventoryItem = null;
        if ($request->has('inventory_id')) {
            $inventoryItem = Inventory::find($request->inventory_id);
        }

        // Get all vendors
        $vendors = Vendor::all();

        // Get all asset requests
        $requests = AssetRequest::all();

        // Get all inventory items (assets) for the dropdown
        $assets = Inventory::all(); // or filter by company_id if needed
        $inventory = Inventory::all();
        return view('users.assets.orderForm', compact('inventoryItem', 'vendors', 'requests', 'assets','inventory'));
    }

    public function updateManager(Request $request, $sectorId)
    {
        $sector = Sector::findOrFail($sectorId);

        $validated = $request->validate([
            'manager_id' => 'nullable|exists:users,user_id',
        ]);

        // Optional: Unassign the selected manager from their old sector (if any)
        if (!empty($validated['manager_id'])) {
            Sector::where('manager_id', $validated['manager_id'])
                ->where('sector_id', '!=', $sectorId)
                ->update(['manager_id' => null]);
        }

        // Update this sector’s manager
        $sector->update([
            'manager_id' => $validated['manager_id'],
        ]);

        return redirect()->route('users.users')
            ->with('success', 'Manager updated successfully.');
    }

    
    public function settings()
    {
        return view('users.settings');
    }
}
