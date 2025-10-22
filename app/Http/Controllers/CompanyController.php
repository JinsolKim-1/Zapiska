<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Company;

class CompanyController extends Controller
{
    public function create()
    {
        $existingCompany = Company::where('creator_id', Auth::id())->first();
        if ($existingCompany) {
            return redirect()->route('welcmain')
                ->with('error', 'You already have a company registered.');
        }

        return view('company_register');
    }

    public function store(Request $request)
    {
        if (Company::where('creator_id', Auth::id())->exists()) {
            return redirect()->route('welcmain')
                ->with('error', 'You can only register one company.');
        }

        $validated = $request->validate([
            'company_name' => 'required|string|max:150',
            'company_email' => 'required|email|unique:companies,company_email',
            'company_number' => ['required', 'regex:/^\+[1-9]\d{1,14}$/'],
            'company_address' => 'nullable|string|max:255',
            'company_desc' => 'nullable|string|max:2000',
            'company_website' => 'nullable|url|max:255',
        ]);

        $validated['company_desc'] = strip_tags($validated['company_desc']);

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

        return redirect()->route('welcmain')
            ->with('success', 'Your company has been submitted for verification.');
    }
}
