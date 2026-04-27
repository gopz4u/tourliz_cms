<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use Illuminate\Http\Request;

class CountryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $countries = Country::orderBy('name')->paginate(15);
        return view('admin.countries.index', compact('countries'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin.countries.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:countries,name',
            'code' => 'nullable|string|max:10',
            'status' => 'boolean',
        ]);

        $validated['status'] = $request->has('status') ? 1 : 0;

        Country::create($validated);

        return redirect()->route('admin.countries.index')->with('success', 'Country created successfully!');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $country = Country::findOrFail($id);
        return view('admin.countries.edit', compact('country'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $country = Country::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:countries,name,' . $country->id,
            'code' => 'nullable|string|max:10',
            'status' => 'boolean',
        ]);

        $validated['status'] = $request->has('status') ? 1 : 0;

        $country->update($validated);

        return redirect()->route('admin.countries.index')->with('success', 'Country updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $country = Country::findOrFail($id);
        $country->delete();

        return redirect()->route('admin.countries.index')->with('success', 'Country deleted successfully!');
    }
}
