<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Inventory;
use App\Models\AssetCategory;

class InventoryController extends Controller
{
    /**
     * Display all inventory items for the company.
     */
    public function index()
    {
        $user = Auth::user();

        // Fetch all inventory items belonging to the company, with categories
        $inventory = Inventory::where('company_id', $user->company_id)
            ->with('category') // Make sure Inventory model has category() relation
            ->get();

        // Fetch all categories for the filter dropdown
        $categories = AssetCategory::all();

        return view('users.inventory', compact('inventory', 'categories'));
    }

    /**
     * Show create inventory form (optional modal or separate page).
     */
    public function create()
    {
        $categories = AssetCategory::all();
        return view('users.inventoryCreate', compact('categories'));
    }

    /**
     * Store a new inventory item.
     */
    public function store(Request $request)
    {
        $request->validate([
            'asset_name' => 'required|string|max:255',
            'asset_category_id' => 'required|integer|exists:asset_categories,asset_category_id',
            'description' => 'nullable|string|max:1000',
            'quantity' => 'required|integer|min:0',
            'unit_cost' => 'required|numeric|min:0',
            'reorder_level' => 'nullable|integer|min:0',
            'supplier' => 'nullable|string|max:100',
        ]);

        Inventory::create([
            'company_id' => Auth::user()->company_id,
            'asset_category_id' => $request->asset_category_id,
            'asset_name' => $request->asset_name,
            'description' => $request->description,
            'quantity' => $request->quantity,
            'unit_cost' => $request->unit_cost,
            'reorder_level' => $request->reorder_level ?? 0,
            'supplier' => $request->supplier,
            'last_restock' => now(),
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('users.inventory.index')->with('success', 'Inventory added successfully!');
    }

    /**
     * Update an inventory item.
     */
    public function update(Request $request, $id)
    {
        $inventory = Inventory::findOrFail($id);

        $request->validate([
            'asset_name' => 'required|string|max:255',
            'asset_category_id' => 'required|integer|exists:asset_categories,asset_category_id',
            'description' => 'nullable|string|max:1000',
            'quantity' => 'nullable|integer|min:0',
            'unit_cost' => 'nullable|numeric|min:0',
            'reorder_level' => 'nullable|integer|min:0',
            'supplier' => 'nullable|string|max:100',
        ]);

        $inventory->update([
            'asset_name' => $request->asset_name,
            'asset_category_id' => $request->asset_category_id,
            'description' => $request->description,
            'quantity' => $request->quantity,
            'unit_cost' => $request->unit_cost,
            'reorder_level' => $request->reorder_level,
            'supplier' => $request->supplier,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Inventory updated successfully!',
            'data' => $inventory
        ]);
    }


    /**
     * Restock an inventory item.
     */
    public function restock(Request $request, $id)
    {
        $inventory = Inventory::findOrFail($id);

        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $inventory->quantity += (int)$request->quantity;
        $inventory->last_restock = now();
        $inventory->save();

        return response()->json(['message' => 'Inventory restocked successfully']);
    }

    public function assign(Request $request, $id)
    {
        return response()->json(['message' => 'Inventory assigned']);
    }

    public function withdraw(Request $request, $id)
    {
        $inventory = Inventory::findOrFail($id);
        $quantity = (int) $request->input('quantity');

        if ($quantity <= 0) {
            return response()->json(['message' => 'Invalid quantity.'], 400);
        }

        if ($inventory->quantity < $quantity) {
            return response()->json(['message' => 'Not enough stock.'], 400);
        }

        $inventory->quantity -= $quantity;
        $inventory->save();

        return response()->json(['message' => 'Withdrawal successful.']);
    }

    /**
     * Delete an inventory item.
     */
    public function destroy($id)
    {
        $inventory = Inventory::findOrFail($id);
        $inventory->delete();

        return response()->json(['message' => 'Inventory deleted successfully']);
    }
}
