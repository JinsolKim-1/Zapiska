<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vendor;
use Illuminate\Support\Facades\Auth;

class VendorController extends Controller
{
    public function index()
    {
        $companyId = Auth::user()->company_id;
        $vendors = Vendor::where('company_id', $companyId)->get();
        return view('users.vendors.index', compact('vendors'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'vendor_name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:100',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
        ]);

        $vendor = Vendor::create([
            'company_id' => Auth::user()->company_id,
            'vendor_name' => $validated['vendor_name'],
            'contact_person' => $validated['contact_person'] ?? null,
            'email' => $validated['email'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'vendor_id' => $vendor->vendor_id,
            'vendor_name' => $vendor->vendor_name,
        ]);
    }



    public function update(Request $request, $id)
    {
        $vendor = Vendor::findOrFail($id);

        $validated = $request->validate([
            'vendor_name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:100',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'api_source' => 'nullable|in:manual,amazon,other',
        ]);

        $vendor->update($request->only([
            'vendor_name', 'contact_person', 'email', 'phone', 'address', 'api_source', 'api_vendor_id'
        ]));

        return redirect()->back()->with('success', 'Vendor updated successfully!');
    }

    public function destroy($id)
    {
        $vendor = Vendor::findOrFail($id);
        $vendor->delete();

        return redirect()->back()->with('success', 'Vendor deleted successfully!');
    }
}
