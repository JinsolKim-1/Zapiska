<?php

namespace App\Http\Controllers;

use App\Mail\InvitationMail;
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


class AdminController extends Controller
{
    public function departments()
    {
        return view('users.departments');
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

        // Get all sectors for this company, with manager and users
        $sectors = Sector::with('manager', 'users.role')
            ->where('company_id', $companyId)
            ->get();

        // Get role ID for "Manager"
        $managerRole = Role::whereRaw('LOWER(role_name) = ?', ['manager'])->first();
        $managerRoleId = $managerRole ? $managerRole->role_id : null;

        // Get all users with manager role for this company
        $managers = $managerRoleId
            ? User::where('company_id', $companyId)
                ->where('role_id', $managerRoleId)
                ->get()
            : collect(); // empty collection if no manager role found

        return view('users.users', compact('sectors', 'managers'));
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



    public function settings()
    {
        return view('users.settings');
    }
}
