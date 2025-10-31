<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Sector;
use App\Models\User;

class ManagerUserController extends Controller
{
    public function index()
    {
        $manager = Auth::user();

        // Get the manager's sector
        $sector = Sector::with(['users.role'])
            ->where('manager_id', $manager->user_id)
            ->first();

        if (!$sector) {
            return view('users.manager-users', [
                'sector' => null,
                'employees' => collect(),
            ]);
        }

        // Get employees in that same sector
        $employees = User::with('role')
            ->where('sector_id', $sector->sector_id)
            ->whereHas('role', fn($q) => $q->where('role_name', 'Employee'))
            ->get();

        return view('users.manager-users', compact('sector', 'employees'));
    }
}
