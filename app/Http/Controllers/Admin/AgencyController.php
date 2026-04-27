<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Agency;
use App\Models\Destination;
use Illuminate\Http\Request;

class AgencyController extends Controller
{
    public function index()
    {
        $agencies = Agency::with('destinations')
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        return view('admin.agencies.index', compact('agencies'));
    }

    public function create()
    {
        $destinations = Destination::orderBy('name')->get();
        return view('admin.agencies.create', compact('destinations'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'company_name' => 'required|string|max:255',
            'primary_contact_name' => 'nullable|string|max:255',
            'whatsapp_number' => 'nullable|string|max:20',
            'destination_ids' => 'nullable|array',
        ]);

        try {
            $agency = Agency::create([
                'company_name' => $request->company_name,
                'primary_contact_name' => $request->primary_contact_name,
                'whatsapp_number' => $request->whatsapp_number,
                'default_markup' => 10.00,
                'currency' => 'USD',
                'is_active' => true,
            ]);

            if ($request->has('destination_ids')) {
                $agency->destinations()->sync($request->destination_ids);
            }

            return redirect()->route('admin.agencies.index')->with('success', 'Agency created successfully.');
        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['error' => 'Failed to create agency: ' . $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        $agency = Agency::with('destinations')->findOrFail($id);
        $destinations = Destination::orderBy('name')->get();
        return view('admin.agencies.edit', compact('agency', 'destinations'));
    }

    public function update(Request $request, $id)
    {
        $agency = Agency::findOrFail($id);

        $request->validate([
            'company_name' => 'required|string|max:255',
            'whatsapp_number' => 'nullable|string|max:20',
        ]);

        $agency->update([
            'company_name' => $request->company_name,
            'primary_contact_name' => $request->primary_contact_name,
            'whatsapp_number' => $request->whatsapp_number,
        ]);

        if ($request->has('destination_ids')) {
            $agency->destinations()->sync($request->destination_ids);
        }

        return redirect()->route('admin.agencies.index')->with('success', 'Agency updated.');
    }

    public function destroy($id)
    {
        $agency = Agency::findOrFail($id);
        $agency->delete();
        return redirect()->route('admin.agencies.index')->with('success', 'Agency deleted.');
    }
}
