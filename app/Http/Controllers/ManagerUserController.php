<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Sector;
use App\Models\User;

class ManagerUserController extends Controller
{
    /**
     * Show the manager's department and its employees.
     * Route: GET /manager/users
     */
    public function index()
    {
        $manager = Auth::user();

        // Find the sector where this user is set as manager
        $sector = Sector::with(['users.role'])
            ->where('manager_id', $manager->user_id)
            ->first();

        // If no sector assigned, return view with null sector
        if (! $sector) {
            return view('users.manager-users', [
                'sector' => null,
                'employees' => collect(),
            ]);
        }

        $employees = User::with('role')
            ->where('sector_id', $sector->sector_id)
            ->where('user_id', '!=', $manager->user_id)
            ->get();

        return view('users.manager-users', compact('sector', 'employees'));
    }
}
