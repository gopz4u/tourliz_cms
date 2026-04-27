<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ItineraryExpense;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    /**
     * Store a newly created expense in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'itinerary_id' => 'required|integer',
            'itinerary_type' => 'required|string|in:b2b,b2c',
            'category' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
            'description' => 'nullable|string',
            'supplier_name' => 'nullable|string',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'vehicle_type' => 'nullable|string',
        ]);

        $expense = ItineraryExpense::create([
            'itinerary_id' => $request->itinerary_id,
            'itinerary_type' => $request->itinerary_type,
            'category' => $request->category,
            'amount' => $request->amount,
            'expense_date' => $request->expense_date,
            'description' => $request->description,
            'supplier_name' => $request->supplier_name,
            'supplier_id' => $request->supplier_id,
            'vehicle_type' => $request->vehicle_type,
            'created_by' => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Expense added successfully',
            'expense' => $expense->load('supplier')
        ]);
    }

    /**
     * Remove the specified expense from storage.
     */
    public function destroy($id)
    {
        $expense = ItineraryExpense::findOrFail($id);
        $expense->delete();

        return response()->json([
            'success' => true,
            'message' => 'Expense deleted successfully'
        ]);
    }

    /**
     * List expenses for a specific itinerary.
     */
    public function index(Request $request)
    {
        $expenses = ItineraryExpense::where('itinerary_id', $request->itinerary_id)
            ->where('itinerary_type', $request->itinerary_type)
            ->with(['creator', 'supplier'])
            ->orderBy('expense_date', 'desc')
            ->get();

        return response()->json($expenses);
    }

    /**
     * Update the specified expense, including status or changing supplier.
     */
    public function update(Request $request, $id)
    {
        $expense = ItineraryExpense::findOrFail($id);

        $request->validate([
            'status' => 'nullable|in:pending,requested,confirmed,rejected',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'amount' => 'nullable|numeric|min:0',
            'category' => 'nullable|string',
            'description' => 'nullable|string',
            'paid_amount' => 'nullable|numeric|min:0',
            'paid_by' => 'nullable|string',
        ]);

        if ($request->filled('status')) {
            $expense->status = $request->status;
        }

        if ($request->filled('amount')) {
            $expense->amount = $request->amount;
        }

        if ($request->filled('paid_amount')) {
            $expense->paid_amount = $request->paid_amount;
        }

        if ($request->filled('paid_by')) {
            $expense->paid_by = $request->paid_by;
        }

        if ($request->filled('category')) {
            $expense->category = $request->category;
        }

        if ($request->filled('description')) {
            $expense->description = $request->description;
        }

        if ($request->filled('supplier_id')) {
            $expense->supplier_id = $request->supplier_id;
            if ($request->status !== 'confirmed') {
                $expense->status = 'pending';
            }
        }

        $expense->save();

        return response()->json([
            'success' => true,
            'message' => 'Expense updated successfully',
            'expense' => $expense->load('supplier')
        ]);
    }
}
