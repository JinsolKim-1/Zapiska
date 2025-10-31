<?php

namespace App\Http\Controllers;

use App\Models\Invitation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Company;

class CompanyController extends Controller
{
    // 🔹 Show company registration form
    public function create()
    {
        $existingApproved = Company::where('creator_id', Auth::id())
            ->where('verification_status', 'verified')
            ->first();

        if ($existingApproved) {
            return redirect()->route('company.dashboard')
                ->with('error', 'You already have an approved company and cannot create another.');
        }

        return view('company_register');
    }

    // 🔹 Store company (submission)
    public function store(Request $request)
    {
        if (Company::where('creator_id', Auth::id())
            ->where('verification_status', 'verified')
            ->exists()) {
            return redirect()->route('company.dashboard')
                ->with('error', 'You already have an approved company.');
        }

        $validated = $request->validate([
            'company_name' => 'required|string|max:150',
            'company_email' => 'required|email|unique:companies,company_email',
            'company_number' => ['required', 'regex:/^\+?[0-9\s().-]{7,20}$/'],
            'company_address' => 'nullable|string|max:255',
            'company_desc' => 'nullable|string|max:2000',
            'company_website' => 'nullable|url|max:255',
        ]);

        $validated['company_desc'] = strip_tags($validated['company_desc']);

        try {
            DB::beginTransaction();

            Company::create([
                'creator_id' => Auth::id(),
                'company_name' => $validated['company_name'],
                'company_email' => $validated['company_email'],
                'company_number' => $validated['company_number'],
                'company_address' => $validated['company_address'],
                'company_desc' => $validated['company_desc'],
                'company_website' => $validated['company_website'],
                'verification_status' => 'pending',
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to register company. Please try again.');
        }

        return redirect()->route('welcmain')
            ->with('success', 'Your company has been submitted. Await admin approval.');
    }

    // 🔹 Company Dashboard
    public function dashboard(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login')
                ->with('error', 'You must be logged in to access the dashboard.');
        }

        if (!$user->company_id || !$user->company) {
            return redirect()->route('welcmain')
                ->with('error', 'You have no registered company.');
        }

        $company = $user->company;

        if ($company->verification_status !== 'verified') {
            return redirect()->route('welcmain')
                ->with('error', 'Your company is still pending approval.');
        }

        return view('users.Maindashboard', compact('user', 'company'));
    }

    // 🔹 Join a company via invite code
    public function joinCompany(Request $request)
    {
        $request->validate([
            'invite_code' => 'required|string|exists:invitations,invite_token',
        ]);

        $invite = Invitation::where('invite_token', $request->invite_code)
            ->where('status', 'pending')
            ->first();

        if (!$invite) {
            return response()->json([
                'message' => 'Invalid or already used invite code.'
            ], 422);
        }

        $user = Auth::user();
        $user->company_id = $invite->company_id;
        $user->role_id = $invite->role_id;
        $user->save();

        $invite->update(['status' => 'approved']);

        return response()->json([
            'message' => 'Successfully joined the company!',
            'redirect' => route('users.dashboard'),
        ]);
    }
}
