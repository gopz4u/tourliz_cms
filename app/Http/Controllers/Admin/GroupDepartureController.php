<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FixedItinerary;
use App\Models\GroupDeparture;
use App\Models\GroupDepartureCustomer;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class GroupDepartureController extends Controller
{
    public function __construct()
    {
        GroupDeparture::ensureTableSchema();
    }

    public function index(Request $request)
    {
        GroupDeparture::ensureTableSchema();

        if (!\Illuminate\Support\Facades\Schema::hasTable('group_departures')) {
            $departures = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 15);
            return view('admin.group-departures.index', compact('departures'));
        }

        $query = GroupDeparture::with('fixedItinerary')->withCount('customers')->withSum([
            'customers as booked_pax' => fn ($q) => $q->where('booking_status', '!=', 'cancelled')
        ], 'pax');

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $departures = $query->orderBy('departure_date')->paginate(15)->withQueryString();
        return view('admin.group-departures.index', compact('departures'));
    }

    public function create(Request $request)
    {
        $fixedItineraries = FixedItinerary::where('status', 'active')->orderBy('title')->get();
        $selected = $request->filled('fixed_itinerary_id') ? $fixedItineraries->firstWhere('id', (int) $request->fixed_itinerary_id) : null;
        return view('admin.group-departures.create', compact('fixedItineraries', 'selected'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateDeparture($request);
        $template = FixedItinerary::findOrFail($validated['fixed_itinerary_id']);
        $validated['user_id'] = auth()->id();
        $validated['title'] = $validated['title'] ?: $template->title;
        $departure = GroupDeparture::create($validated);

        return redirect()->route('admin.group-departures.show', $departure)->with('success', 'Group departure announced successfully.');
    }

    public function show(GroupDeparture $groupDeparture)
    {
        $groupDeparture->load(['fixedItinerary.destination', 'customers']);
        return view('admin.group-departures.show', compact('groupDeparture'));
    }

    public function edit(GroupDeparture $groupDeparture)
    {
        $fixedItineraries = FixedItinerary::where('status', 'active')->orderBy('title')->get();
        return view('admin.group-departures.edit', compact('groupDeparture', 'fixedItineraries'));
    }

    public function update(Request $request, GroupDeparture $groupDeparture)
    {
        $groupDeparture->update($this->validateDeparture($request));
        return redirect()->route('admin.group-departures.show', $groupDeparture)->with('success', 'Departure details updated.');
    }

    public function destroy(GroupDeparture $groupDeparture)
    {
        $groupDeparture->delete();
        return redirect()->route('admin.group-departures.index')->with('success', 'Group departure archived.');
    }

    public function addCustomer(Request $request, GroupDeparture $groupDeparture)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255', 'phone' => 'nullable|string|max:40', 'email' => 'nullable|email|max:255',
            'passport_number' => 'nullable|string|max:100', 'date_of_birth' => 'nullable|date',
            'customer_type' => ['required', Rule::in(['adult', 'child'])], 'pax' => 'required|integer|min:1|max:50',
            'rate' => 'required|numeric|min:0', 'amount_paid' => 'nullable|numeric|min:0',
            'payment_status' => ['required', Rule::in(['pending', 'partial', 'paid'])],
            'booking_status' => ['required', Rule::in(['reserved', 'confirmed', 'cancelled'])], 'notes' => 'nullable|string',
        ]);

        $newPax = $groupDeparture->booked_pax + ($validated['booking_status'] === 'cancelled' ? 0 : $validated['pax']);
        if ($newPax > $groupDeparture->capacity) {
            return back()->withErrors(['pax' => "Only {$groupDeparture->available_seats} seats are available."])->withInput();
        }

        $groupDeparture->customers()->create($validated);
        return back()->with('success', 'Customer added to the departure.');
    }

    public function updateCustomer(Request $request, GroupDeparture $groupDeparture, GroupDepartureCustomer $customer)
    {
        abort_unless($customer->group_departure_id === $groupDeparture->id, 404);
        $validated = $request->validate([
            'amount_paid' => 'required|numeric|min:0',
            'payment_status' => ['required', Rule::in(['pending', 'partial', 'paid'])],
            'booking_status' => ['required', Rule::in(['reserved', 'confirmed', 'cancelled'])],
        ]);
        $customer->update($validated);
        return back()->with('success', 'Customer status updated.');
    }

    public function destroyCustomer(GroupDeparture $groupDeparture, GroupDepartureCustomer $customer)
    {
        abort_unless($customer->group_departure_id === $groupDeparture->id, 404);
        $customer->delete();
        return back()->with('success', 'Customer removed from the departure.');
    }

    public function downloadItinerary(GroupDeparture $groupDeparture)
    {
        $itinerary = $groupDeparture->fixedItinerary()->with(['supplier', 'destination'])->firstOrFail();
        $pdf = Pdf::loadView('admin.fixed-itineraries.pdf', compact('itinerary'));
        return $pdf->download('Group_Trip_' . $groupDeparture->departure_date->format('Ymd') . '.pdf');
    }

    private function validateDeparture(Request $request): array
    {
        return $request->validate([
            'fixed_itinerary_id' => 'required|exists:fixed_itineraries,id', 'title' => 'nullable|string|max:255',
            'announcement_date' => 'nullable|date', 'departure_date' => 'required|date',
            'return_date' => 'nullable|date|after_or_equal:departure_date', 'adult_rate' => 'required|numeric|min:0',
            'child_rate' => 'nullable|numeric|min:0', 'currency' => 'required|string|max:10',
            'capacity' => 'required|integer|min:1|max:10000',
            'status' => ['required', Rule::in(['draft', 'announced', 'open', 'sold_out', 'completed', 'cancelled'])],
            'notes' => 'nullable|string',
        ]);
    }
}
