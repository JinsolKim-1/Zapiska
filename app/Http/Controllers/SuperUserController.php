<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use App\Models\Superadmin;
use App\Mail\SuperadminConfirm;

class SuperUserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:superadmin')->except(['confirmEdit']);
    }

    public function index()
    {
        $superadmin = Auth::guard('superadmin')->user();
        $superadmins = Superadmin::orderBy('su_created_at', 'desc')->get();

        $months = collect(range(0, 11))
            ->map(fn($i) => now()->subMonths($i)->format('Y-m'))
            ->reverse();

        $registrationsByMonth = $months->mapWithKeys(function ($month) use ($superadmins) {
            $count = $superadmins->filter(fn($a) => \Carbon\Carbon::parse($a->su_created_at)->format('Y-m') === $month)->count();
            return [$month => $count];
        });

        return view('superadmin.users', compact('superadmin', 'superadmins', 'registrationsByMonth'));
    }

    public function fetch()
    {
        return response()->json(Superadmin::orderBy('su_created_at', 'desc')->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'super_username' => 'required|string|max:100|unique:superadmins,super_username',
            'super_email' => 'required|email|max:100|unique:superadmins,super_email',
            'super_password' => 'required|min:8|confirmed',
            'first_name' => 'nullable|string|max:100',
            'last_name' => 'nullable|string|max:100',
            'contact' => 'nullable|string|max:50',
            'profile' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $path = $request->hasFile('profile')
            ? $request->file('profile')->store('superadmin_profiles', 'public')
            : null;

        $superadmin = Superadmin::create([
            'super_username' => $validated['super_username'],
            'super_email' => $validated['super_email'],
            'super_password' => Hash::make($validated['super_password']),
            'first_name' => $validated['first_name'] ?? null,
            'last_name' => $validated['last_name'] ?? null,
            'contact' => $validated['contact'] ?? null,
            'profile' => $path,
            'status' => 'active',
        ]);

        return response()->json([
            'message' => 'Superadmin created successfully.',
            'superadmin' => $superadmin,
        ], 201);
    }

    /**
     * 🔹 Request edit confirmation email (instead of direct update)
     */
    public function requestEdit(Request $request, $id)
    {
        $target = Superadmin::find($id);
        $editor = Auth::guard('superadmin')->user();

        if (!$target) {
            return response()->json(['message' => 'Target superadmin not found.'], 404);
        }

        // Prevent modifying primary superadmin
        if ($target->super_id === 1 && $editor->super_id !== 1) {
            return response()->json(['message' => 'You cannot modify the primary superadmin.'], 403);
        }

        // Encrypt pending data
        $payload = Crypt::encrypt([
            'id' => $id,
            'updates' => $request->all(),
            'expires' => Carbon::now()->addMinutes(30)->timestamp,
        ]);

        // Signed link (valid for 30 minutes)
        $url = URL::temporarySignedRoute(
            'superadmin.confirmEdit',
            now()->addMinutes(10),
            ['id' => $id, 'token' => $payload]
        );

        // Send confirmation email to target user
        Mail::to($target->super_email)->send(new SuperadminConfirm($target, $editor, $url));

        return response()->json(['message' => 'Confirmation link sent to target user email.']);
    }

    /**
     * 🔹 When target user clicks confirmation link in email
     */
    public function confirmEdit(Request $request, $id)
    {
        if (!$request->hasValidSignature()) {
            return view('superadmin.email_invalid')->with('message', 'Invalid or expired confirmation link.');
        }

        try {
            $data = Crypt::decrypt($request->token);

            if (Carbon::now()->timestamp > $data['expires']) {
                return view('superadmin.email_invalid')->with('message', 'This confirmation link has expired.');
            }

            $target = Superadmin::find($data['id']);
            if (!$target) {
                return view('superadmin.email_invalid')->with('message', 'Superadmin not found.');
            }

            // Apply updates
            $target->update($data['updates']);

            return view('superadmin.email_confirmed')->with('message', 'Your account details were successfully updated.');

        } catch (\Exception $e) {
            return view('superadmin.email_invalid')->with('message', 'Invalid or corrupted confirmation link.');
        }
    }

    public function update(Request $request, $id)
    {
        $admin = Auth::guard('superadmin')->user();
        $user = Superadmin::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        // Prevent others from editing directly (handled via requestEdit)
        if ($admin->super_id !== $user->super_id) {
            return response()->json([
                'message' => 'You cannot directly update another superadmin. Use the confirmation email flow.'
            ], 403);
        }

        $validated = $request->validate([
            'super_username' => [
                'required', 'string', 'max:100',
                Rule::unique('superadmins', 'super_username')->ignore($user->super_id, 'super_id'),
            ],
            'super_email' => [
                'required', 'email', 'max:100',
                Rule::unique('superadmins', 'super_email')->ignore($user->super_id, 'super_id'),
            ],
            'first_name' => 'nullable|string|max:100',
            'last_name' => 'nullable|string|max:100',
            'contact' => 'nullable|string|max:50',
            'profile' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'super_password' => 'nullable|min:8|confirmed',
        ]);

        // Handle profile replacement
        if ($request->hasFile('profile')) {
            if ($user->profile && Storage::disk('public')->exists($user->profile)) {
                Storage::disk('public')->delete($user->profile);
            }
            $user->profile = $request->file('profile')->store('superadmin_profiles', 'public');
        }

        $user->fill([
            'super_username' => $validated['super_username'],
            'super_email' => $validated['super_email'],
            'first_name' => $validated['first_name'] ?? null,
            'last_name' => $validated['last_name'] ?? null,
            'contact' => $validated['contact'] ?? null,
        ]);

        if (!empty($validated['super_password'])) {
            $user->super_password = Hash::make($validated['super_password']);
        }

        $user->save();

        return response()->json(['message' => 'Superadmin updated successfully.']);
    }

    public function destroy($id)
    {
        $user = Superadmin::find($id);
        $current = Auth::guard('superadmin')->user();

        if (!$user) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        if ($user->super_id === $current->super_id) {
            return response()->json(['message' => 'You cannot delete your own account.'], 403);
        }

        if ($user->super_id === 1) {
            return response()->json(['message' => 'Cannot delete the primary superadmin.'], 403);
        }

        if ($user->status === 'inactive') {
            if ($user->profile && Storage::disk('public')->exists($user->profile)) {
                Storage::disk('public')->delete($user->profile);
            }
            $user->delete();
            return response()->json(['message' => 'Superadmin permanently deleted.']);
        }

        $user->status = 'inactive';
        $user->save();

        return response()->json(['message' => 'Superadmin deactivated.']);
    }
}
