<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Receipt;

class ReceiptController extends Controller
{
    /**
     * Display all receipts for the logged-in user's company.
     */
    public function index()
    {
        $user = Auth::user();
        $companyId = $user->company_id;

        // If the user is a manager, show all receipts in the company
        // If employee, show only their own
        $query = Receipt::with(['user'])
            ->where('company_id', $companyId);

        if ($user->role->role_name === 'Employee') {
            $query->where('user_id', $user->user_id);
        }

        $receipts = $query->orderBy('receipt_date', 'desc')->get();

        return view('users.receipts', compact('receipts'));
    }
}
