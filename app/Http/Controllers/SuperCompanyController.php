<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\CompanyVerification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Mail\CompanyVerifyEmail;
use App\Mail\CompanyRejected;

class SuperCompanyController extends Controller
{
    // Show all pending companies
    public function index()
    {
        $companies = Company::where('verification_status', 'pending')->get();
        return view('superadmin.companies', compact('companies'));
    }

    // Approve a company
    public function approve($id)
    {
        $company = Company::findOrFail($id);

        DB::beginTransaction();
        try {
            // Update company status to approved (admin approved)
            $company->update(['verification_status' => 'approved']);

            // Generate verification token
            $token = Str::random(60);

            CompanyVerification::create([
                'company_id' => $company->company_id,
                'company_token' => $token,
                'expires_at' => now()->addHours(24),
                'status' => 'pending',
            ]);

            // Send verification email
            Mail::to($company->company_email)->send(new CompanyVerifyEmail($company, $token));

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('superadmin.companies')
                ->with('error', 'Failed to approve company. Please try again.');
        }

        return redirect()->route('superadmin.companies')
            ->with('success', 'Company approved. Verification email sent.');
    }

    // Reject a company
    public function reject($id)
    {
        $company = Company::findOrFail($id);

        DB::beginTransaction();
        try {
            // Delete any related verification record first
            $verification = CompanyVerification::where('company_id', $company->company_id)->first();

            if ($verification) {
                $verification->delete();
            }

            // Send rejection email before deleting company record
            Mail::to($company->company_email)->send(new CompanyRejected($company));

            // Finally delete the company record
            $company->delete();

            DB::commit();

            return redirect()->route('superadmin.companies')
                ->with('success', 'Company rejected and deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('superadmin.companies')
                ->with('error', 'Failed to reject company. Please try again.');
        }
    }
}
