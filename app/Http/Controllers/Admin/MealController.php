<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Meal;
use App\Models\Destination;
use App\Models\Supplier;
use Illuminate\Http\Request;

class MealController extends Controller
{
    public function index()
    {
        $meals = Meal::with('destination')->paginate(15);
        return view('admin.meals.index', compact('meals'));
    }

    public function create()
    {
        $countries = \App\Models\Country::orderBy('name')->get();
        $destinations = Destination::all();
        $suppliers = Supplier::where('is_active', 1)->orderBy('name')->get();
        return view('admin.meals.create', compact('countries', 'destinations', 'suppliers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:Breakfast,Lunch,Dinner,Snack,All Inclusive',
            'price' => 'required|numeric|min:0',
            'destination_id' => 'nullable|exists:destinations,id',
        ]);

        Meal::create($request->all());

        return redirect()->route('admin.meals.index')->with('success', 'Meal option created.');
    }

    public function edit(Meal $meal)
    {
        $countries = \App\Models\Country::orderBy('name')->get();
        $destinations = Destination::all();
        $suppliers = Supplier::where('is_active', 1)->orderBy('name')->get();
        return view('admin.meals.edit', compact('meal', 'countries', 'destinations', 'suppliers'));
    }

    public function update(Request $request, Meal $meal)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:Breakfast,Lunch,Dinner,Snack,All Inclusive',
            'price' => 'required|numeric|min:0',
            'destination_id' => 'nullable|exists:destinations,id',
        ]);

        $meal->update($request->all());

        return redirect()->route('admin.meals.index')->with('success', 'Meal option updated.');
    }

    public function destroy(Meal $meal)
    {
        $meal->delete();
        return redirect()->route('admin.meals.index')->with('success', 'Meal option deleted.');
    }
}
