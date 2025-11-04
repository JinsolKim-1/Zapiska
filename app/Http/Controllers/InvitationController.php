<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use App\Models\Invitation;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\InviteEmail;

class InvitationController extends Controller
{
    public function index()
    {
        $companyId = Auth::user()->company_id;
        $roles = Role::where('company_id', $companyId)->get();

        return view('users.invite', compact('roles'));
    }

    public function sendInvite(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'category' => 'required|in:admin,manager,employee',
            'role_name' => 'required|string|max:100',
        ]);

        $email = $request->email;

        // Check if email already exists in users table
        if (User::where('email', $email)->exists()) {
            return redirect()->back()->with('error', "The email {$email} is already in use.");
        }

        $company = Auth::user()->company;
        $companyId = $company->company_id;
        $inviterId = Auth::id();

        // Create or reuse existing role
        $role = Role::firstOrCreate([
            'company_id' => $companyId,
            'role_name' => $request->role_name,
            'category' => $request->category,
        ]);

        // Create invitation with auto-generated token
        $invitation = Invitation::create([
            'company_id' => $companyId,
            'inviter_id' => $inviterId,
            'invitee_email' => $email,
            'role_id' => $role->role_id,
            'status' => 'pending',
        ]);

        // Send invitation email
        Mail::to($email)->send(new InviteEmail($invitation));

        Log::info("Invitation sent to {$email} by user ID {$inviterId} from company {$company->company_name}");

        return redirect()->back()->with('success', 'Invitation sent successfully to ' . $email);
    }

    public function acceptInvite($token)
    {
        $invite = Invitation::where('invite_token', $token)->firstOrFail();

        if ($invite->expires_at < now()) {
            return redirect()->route('register')->with('error', 'Invitation expired, please register manually.');
        }

        // If user already has an account
        $user = User::where('email', $invite->invitee_email)->first();
        if ($user) {
            // Attach role and company
            $user->update([
                'company_id' => $invite->company_id,
                'role_id' => $invite->role_id,
            ]);

            $invite->update(['status' => 'approved']);

            return redirect()->route('users.dashboard')
                ->with('success', 'Welcome back! Invitation accepted.');
        }

        // If no account → redirect to register, store token in session
        session(['invite_token' => $token]);

        return redirect()->route('register')
            ->with('info', 'Please register to accept the invitation.');
    }
}
