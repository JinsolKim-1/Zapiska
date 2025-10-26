<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Company;

class CompanyController extends Controller
{
    // Show company registration form
    public function create()
    {
        // Check if the user already has an approved company
        $existingApproved = Company::where('creator_id', Auth::id())
            ->where('verification_status', 'verified')
            ->first();

        if ($existingApproved) {
            return redirect()->route('welcmain')
                ->with('error', 'You already have an approved company and cannot create another.');
        }

        return view('company_register');
    }

    // Store company (submission only, no email)
    public function store(Request $request)
    {
        if (Company::where('creator_id', Auth::id())
            ->where('verification_status', 'verified')
            ->exists()) {
            return redirect()->route('welcmain')
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

            // Create company with "pending" status
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
}
