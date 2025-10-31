<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Log;
use App\Models\Invitation;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
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

        // Check if email already exists
        if (User::where('email', $email)->exists()) {
            return redirect()->back()->with('error', "The email {$email} is already in use.");
        }

        $companyId = Auth::user()->company_id;
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
            'inviter_email' => $email,
            'role_id' => $role->role_id,
            'status' => 'pending',
        ]);

        Log::info('Sending invite to ' . $email);

        // Send invitation email
        Mail::to($email)->send(new InviteEmail($invitation));

        return redirect()->back()->with('success', 'Invitation sent successfully to ' . $email);
    }

    public function acceptInvite($token)
    {
        $invite = Invitation::where('invite_token', $token)->firstOrFail();

        if ($invite->expires_at < now()) {
            return redirect()->route('register')->with('error', 'Invitation expired, please register manually.');
        }

        // If already has account
        $user = User::where('email', $invite->inviter_email)->first();
        if ($user) {
            // attach role and company
            $user->update([
                'company_id' => $invite->company_id,
                'role_id' => $invite->role_id,
            ]);
            $invite->update(['status' => 'approved']);
            return redirect()->route('users.dashboard')->with('success', 'Welcome back! Invitation accepted.');
        }

        // If no account → redirect to register, store token in session
        session(['invite_token' => $token]);
        return redirect()->route('register')->with('info', 'Please register to accept the invitation.');
    }
}
