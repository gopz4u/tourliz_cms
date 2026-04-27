<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EntryTicket;
use App\Models\Destination;
use App\Models\Supplier;
use Illuminate\Http\Request;

class EntryTicketController extends Controller
{
    public function index()
    {
        $tickets = EntryTicket::with('destination')->paginate(15);
        return view('admin.entry-tickets.index', compact('tickets'));
    }

    public function create()
    {
        $countries = \App\Models\Country::orderBy('name')->get();
        $destinations = Destination::all();
        $suppliers = Supplier::where('type', 'Activity')->where('is_active', 1)->orderBy('name')->get();
        return view('admin.entry-tickets.create', compact('countries', 'destinations', 'suppliers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'attraction_name' => 'required|string|max:255',
            'destination_id' => 'required|exists:destinations,id',
            'adult_price' => 'required|numeric',
        ]);

        EntryTicket::create($request->all());

        return redirect()->route('admin.entry-tickets.index')->with('success', 'Entry Ticket created successfully.');
    }

    public function edit(EntryTicket $entryTicket)
    {
        $countries = \App\Models\Country::orderBy('name')->get();
        $destinations = Destination::all();
        $suppliers = Supplier::where('type', 'Activity')->where('is_active', 1)->orderBy('name')->get();
        return view('admin.entry-tickets.edit', compact('entryTicket', 'countries', 'destinations', 'suppliers'));
    }

    public function update(Request $request, EntryTicket $entryTicket)
    {
        $request->validate([
            'attraction_name' => 'required|string|max:255',
        ]);

        $entryTicket->update($request->all());

        return redirect()->route('admin.entry-tickets.index')->with('success', 'Entry Ticket updated successfully.');
    }

    public function destroy(EntryTicket $entryTicket)
    {
        $entryTicket->delete();
        return redirect()->route('admin.entry-tickets.index')->with('success', 'Entry Ticket deleted successfully.');
    }
}
