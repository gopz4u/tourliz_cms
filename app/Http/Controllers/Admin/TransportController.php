<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transport;
use Illuminate\Http\Request;

class TransportController extends Controller
{
    public function index()
    {
        $transports = Transport::paginate(15);
        return view('admin.transports.index', compact('transports'));
    }

    public function create()
    {
        $countries = \App\Models\Country::orderBy('name')->get();
        $destinations = \App\Models\Destination::all();
        $suppliers = \App\Models\Supplier::where('type', 'transport')->orWhere('type', 'Data')->get();
        return view('admin.transports.create', compact('countries', 'destinations', 'suppliers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'destination' => 'nullable|string',
            'duration' => 'nullable|string',
            'vehicle_type' => 'required|string',
            'base_price' => 'required|numeric',
        ]);

        Transport::create($request->all());

        return redirect()->route('admin.transports.index')->with('success', 'Transport created successfully.');
    }

    public function edit(Transport $transport)
    {
        $countries = \App\Models\Country::orderBy('name')->get();
        $destinations = \App\Models\Destination::all();
        $suppliers = \App\Models\Supplier::where('type', 'transport')->orWhere('type', 'Data')->get();
        return view('admin.transports.edit', compact('transport', 'countries', 'destinations', 'suppliers'));
    }

    public function update(Request $request, Transport $transport)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'vehicle_type' => 'required|string',
            'base_price' => 'required|numeric',
        ]);

        $transport->update($request->all());

        return redirect()->route('admin.transports.index')->with('success', 'Transport updated successfully.');
    }

    public function destroy(Transport $transport)
    {
        $transport->delete();
        return redirect()->route('admin.transports.index')->with('success', 'Transport deleted successfully.');
    }
}
