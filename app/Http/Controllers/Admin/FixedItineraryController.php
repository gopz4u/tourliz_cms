<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FixedItinerary;
use App\Models\Destination;
use App\Models\Country;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FixedItineraryController extends Controller
{
    public function __construct()
    {
        FixedItinerary::ensureTableSchema();
    }

    public function index(Request $request)
    {
        FixedItinerary::ensureTableSchema();

        $hasDestCol = \Illuminate\Support\Facades\Schema::hasColumn('fixed_itineraries', 'destination_id');
        $hasSupplierIds = \Illuminate\Support\Facades\Schema::hasColumn('fixed_itineraries', 'supplier_ids');

        $withRelations = ['supplier'];
        if ($hasDestCol) {
            $withRelations[] = 'destination';
        }

        $query = FixedItinerary::with($withRelations);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search, $hasDestCol) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('itinerary_description', 'like', "%{$search}%");
                if ($hasDestCol) {
                    $q->orWhereHas('destination', function ($dq) use ($search) {
                        $dq->where('name', 'like', "%{$search}%");
                    });
                }
            });
        }

        if ($request->filled('supplier_id')) {
            $sId = (int) $request->supplier_id;
            $query->where(function ($sq) use ($sId, $hasSupplierIds) {
                $sq->where('supplier_id', $sId);
                if ($hasSupplierIds) {
                    $sq->orWhereJsonContains('supplier_ids', $sId);
                }
            });
        }

        if ($hasDestCol && $request->filled('destination_id')) {
            $query->where('destination_id', $request->destination_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $itineraries = $query->orderBy('updated_at', 'desc')->paginate(15);
        $suppliers = Supplier::where('is_active', true)->orderBy('name')->get();
        $destinations = Destination::orderBy('name')->get();

        return view('admin.fixed-itineraries.index', compact('itineraries', 'suppliers', 'destinations'));
    }

    public function create()
    {
        $destinations = Destination::orderBy('name')->get();
        $countries = Country::where('status', true)->orderBy('name')->get();
        $suppliers = Supplier::where('is_active', true)->orderBy('name')->get();

        return view('admin.fixed-itineraries.create', compact('destinations', 'countries', 'suppliers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'destination_id' => 'nullable|exists:destinations,id',
            'supplier_id' => 'nullable',
            'supplier_ids' => 'nullable|array',
            'supplier_ids.*' => 'nullable|string|max:255',
            'country_ids' => 'nullable|array',
            'country_ids.*' => 'exists:countries,id',
            'duration_days' => 'required|integer|min:1',
            'duration_nights' => 'required|integer|min:0',
            'short_description' => 'nullable|string',
            'itinerary_description' => 'nullable|string',
            'highlights' => 'nullable',
            'inclusions' => 'nullable',
            'exclusions' => 'nullable',
            'terms_conditions' => 'nullable|string',
            'fixed_price' => 'required|numeric|min:0',
            'currency' => 'nullable|string|max:10',
            'status' => 'nullable|string',
            'image' => 'nullable|image|max:4096',
            'days' => 'nullable|array',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('itineraries/fixed', 'public');
        }

        $days = $this->cleanDaysData($request->input('days', []));

        $rawSupplierIds = $request->input('supplier_ids', []);
        if (empty($rawSupplierIds) && $request->filled('supplier_id')) {
            $rawSupplierIds = [$request->input('supplier_id')];
        }
        $supplierIds = $this->resolveSupplierIds($rawSupplierIds, $validated['destination_id'] ?? null);
        $primarySupplierId = !empty($supplierIds) ? $supplierIds[0] : null;

        $itinerary = FixedItinerary::create($this->filterColumnsIfMissing([
            'user_id' => auth()->id(),
            'supplier_id' => $primarySupplierId,
            'supplier_ids' => $supplierIds,
            'destination_id' => $validated['destination_id'] ?? null,
            'country_ids' => $validated['country_ids'] ?? [],
            'title' => $validated['title'],
            'duration_days' => $validated['duration_days'] ?? 1,
            'duration_nights' => $validated['duration_nights'] ?? 0,
            'short_description' => $validated['short_description'] ?? null,
            'itinerary_description' => $validated['itinerary_description'] ?? null,
            'highlights' => $this->parseList($request->input('highlights')),
            'inclusions' => $this->parseList($request->input('inclusions')),
            'exclusions' => $this->parseList($request->input('exclusions')),
            'terms_conditions' => $validated['terms_conditions'] ?? null,
            'days' => $days,
            'fixed_price' => $validated['fixed_price'],
            'currency' => $validated['currency'] ?? 'MYR',
            'image' => $imagePath,
            'status' => $validated['status'] ?? 'active',
        ]));

        return redirect()->route('admin.fixed-itineraries.index')
            ->with('success', "Fixed itinerary '{$itinerary->title}' created successfully.");
    }

    public function show($id)
    {
        FixedItinerary::ensureTableSchema();
        $withRelations = ['supplier'];
        if (\Illuminate\Support\Facades\Schema::hasColumn('fixed_itineraries', 'destination_id')) {
            $withRelations[] = 'destination';
        }
        $itinerary = FixedItinerary::with($withRelations)->findOrFail($id);

        return view('admin.fixed-itineraries.show', compact('itinerary'));
    }

    public function edit($id)
    {
        FixedItinerary::ensureTableSchema();
        $withRelations = ['supplier'];
        if (\Illuminate\Support\Facades\Schema::hasColumn('fixed_itineraries', 'destination_id')) {
            $withRelations[] = 'destination';
        }
        $itinerary = FixedItinerary::with($withRelations)->findOrFail($id);
        $destinations = Destination::orderBy('name')->get();
        $countries = Country::where('status', true)->orderBy('name')->get();
        $suppliers = Supplier::where('is_active', true)->orderBy('name')->get();

        return view('admin.fixed-itineraries.edit', compact('itinerary', 'destinations', 'countries', 'suppliers'));
    }

    public function update(Request $request, $id)
    {
        $itinerary = FixedItinerary::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'destination_id' => 'nullable|exists:destinations,id',
            'supplier_id' => 'nullable',
            'supplier_ids' => 'nullable|array',
            'supplier_ids.*' => 'nullable|string|max:255',
            'country_ids' => 'nullable|array',
            'country_ids.*' => 'exists:countries,id',
            'duration_days' => 'required|integer|min:1',
            'duration_nights' => 'required|integer|min:0',
            'short_description' => 'nullable|string',
            'itinerary_description' => 'nullable|string',
            'highlights' => 'nullable',
            'inclusions' => 'nullable',
            'exclusions' => 'nullable',
            'terms_conditions' => 'nullable|string',
            'fixed_price' => 'required|numeric|min:0',
            'currency' => 'nullable|string|max:10',
            'status' => 'nullable|string',
            'image' => 'nullable|image|max:4096',
            'days' => 'nullable|array',
        ]);

        $imagePath = $itinerary->image;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('itineraries/fixed', 'public');
        }

        $days = $this->cleanDaysData($request->input('days', []));

        $rawSupplierIds = $request->input('supplier_ids', []);
        if (empty($rawSupplierIds) && $request->filled('supplier_id')) {
            $rawSupplierIds = [$request->input('supplier_id')];
        }
        $destId = $validated['destination_id'] ?? $itinerary->destination_id;
        $supplierIds = $this->resolveSupplierIds($rawSupplierIds, $destId);
        $primarySupplierId = !empty($supplierIds) ? $supplierIds[0] : null;

        $itinerary->update($this->filterColumnsIfMissing([
            'supplier_id' => $primarySupplierId,
            'supplier_ids' => $supplierIds,
            'destination_id' => $validated['destination_id'] ?? $itinerary->destination_id,
            'country_ids' => $validated['country_ids'] ?? $itinerary->country_ids,
            'title' => $validated['title'],
            'duration_days' => $validated['duration_days'] ?? $itinerary->duration_days,
            'duration_nights' => $validated['duration_nights'] ?? $itinerary->duration_nights,
            'short_description' => $validated['short_description'] ?? $itinerary->short_description,
            'itinerary_description' => $validated['itinerary_description'] ?? $itinerary->itinerary_description,
            'highlights' => $this->parseList($request->input('highlights')),
            'inclusions' => $this->parseList($request->input('inclusions')),
            'exclusions' => $this->parseList($request->input('exclusions')),
            'terms_conditions' => $validated['terms_conditions'] ?? $itinerary->terms_conditions,
            'days' => $days,
            'fixed_price' => $validated['fixed_price'],
            'currency' => $validated['currency'] ?? $itinerary->currency,
            'image' => $imagePath,
            'status' => $validated['status'] ?? $itinerary->status,
        ]));

        return redirect()->route('admin.fixed-itineraries.index')
            ->with('success', "Fixed itinerary '{$itinerary->title}' updated successfully.");
    }

    public function destroy($id)
    {
        $itinerary = FixedItinerary::findOrFail($id);
        $title = $itinerary->title;
        $itinerary->delete();

        return redirect()->route('admin.fixed-itineraries.index')
            ->with('success', "Fixed itinerary '{$title}' deleted successfully.");
    }

    /** Create an independent copy and open it for customization. */
    public function duplicate($id)
    {
        $itinerary = FixedItinerary::findOrFail($id);
        $copy = $itinerary->replicate();
        $copy->title = $this->duplicateTitle($itinerary->title);
        $copy->user_id = auth()->id() ?: $itinerary->user_id;
        $copy->save();

        return redirect()->route('admin.fixed-itineraries.edit', $copy->id)
            ->with('success', "Fixed itinerary duplicated as '{$copy->title}'. You can now customize it.");
    }

    public function pdf($id)
    {
        FixedItinerary::ensureTableSchema();
        $withRelations = ['supplier'];
        if (\Illuminate\Support\Facades\Schema::hasColumn('fixed_itineraries', 'destination_id')) {
            $withRelations[] = 'destination';
        }
        $itinerary = FixedItinerary::with($withRelations)->findOrFail($id);

        return view('admin.fixed-itineraries.pdf', compact('itinerary'));
    }

    public function whatsapp($id)
    {
        FixedItinerary::ensureTableSchema();
        $withRelations = ['supplier'];
        if (\Illuminate\Support\Facades\Schema::hasColumn('fixed_itineraries', 'destination_id')) {
            $withRelations[] = 'destination';
        }
        $itinerary = FixedItinerary::with($withRelations)->findOrFail($id);
        $lines = [
            "✈️ *TOURLIZ HOLIDAYS*",
            "_Curated Travel Proposal_",
            '',
            "🌟 *{$itinerary->title}*",
            "🗓️ *Duration:* {$itinerary->duration}",
            '📍 *Destination:* ' . ($itinerary->destination->name ?? 'Multiple Destinations'),
        ];

        if ($itinerary->countries->count() > 0) {
            $lines[] = '🌍 *Countries:* ' . $itinerary->countries->pluck('name')->join(', ');
        }

        $overview = $itinerary->short_description ?: $itinerary->itinerary_description;
        if ($overview) {
            $lines[] = '';
            $lines[] = '📝 *JOURNEY OVERVIEW*';
            $lines[] = Str::limit(trim(strip_tags($overview)), 700);
        }

        if (is_array($itinerary->days) && count($itinerary->days) > 0) {
            $lines[] = '';
            $lines[] = '🗺️ *DAY-BY-DAY ITINERARY*';

            foreach ($itinerary->days as $index => $rawDay) {
                $day = (array) $rawDay;
                $dayNumber = $day['day_number'] ?? ($index + 1);
                $dayTitle = $day['title'] ?? ('Day ' . $dayNumber);
                $lines[] = '';
                $lines[] = "*DAY {$dayNumber} — {$dayTitle}*";

                if (!empty($day['description'])) {
                    $lines[] = Str::limit(trim(strip_tags($day['description'])), 500);
                }

                $items = is_array($day['items'] ?? null) ? $day['items'] : [];
                foreach ($items as $rawItem) {
                    $item = (array) $rawItem;
                    if (!empty($item['title'])) {
                        $type = ($item['item_type'] ?? '') === 'spot'
                            ? '📍'
                            : ucfirst($item['item_type'] ?? 'Activity') . ':';
                        $room = !empty($item['room_type']) ? ' (' . $item['room_type'] . ')' : '';
                        $lines[] = "  • {$type} {$item['title']}{$room}";
                    }
                }

                if (!empty($day['overnight_location'])) {
                    $lines[] = '🌙 _Overnight: ' . $day['overnight_location'] . '_';
                }
            }
        }

        if (!empty($itinerary->inclusions)) {
            $lines[] = '';
            $lines[] = '✅ *PACKAGE INCLUSIONS*';
            foreach ($itinerary->inclusions as $item) {
                $lines[] = '  ✓ ' . $item;
            }
        }

        if (!empty($itinerary->exclusions)) {
            $lines[] = '';
            $lines[] = '❌ *PACKAGE EXCLUSIONS*';
            foreach ($itinerary->exclusions as $item) {
                $lines[] = '  × ' . $item;
            }
        }

        if (!empty($itinerary->terms_conditions)) {
            $lines[] = '';
            $lines[] = 'ℹ️ *IMPORTANT INFORMATION*';
            $lines[] = Str::limit(trim(strip_tags($itinerary->terms_conditions)), 700);
        }

        $lines[] = '';
        $lines[] = '💰 *TOTAL FIXED PACKAGE PRICE*';
        $lines[] = '*' . ($itinerary->currency ?: 'MYR') . ' ' . number_format($itinerary->fixed_price, 2) . '*';
        $lines[] = '';
        $lines[] = '_Rates and availability are subject to confirmation._';
        $lines[] = 'Thank you for choosing *Tourliz Holidays!* 🌴';

        $nativeUrl = 'whatsapp://send?text=' . rawurlencode(implode("\n", $lines));

        return view('admin.whatsapp-launcher', compact('nativeUrl'));
    }

    /**
     * Clean and normalize days array with operations inventory items
     */
    protected function cleanDaysData($days): array
    {
        if (!is_array($days)) {
            return [];
        }

        $cleanedDays = [];
        foreach ($days as $dayIdx => $day) {
            $dayNum = (int) ($day['day_number'] ?? ($dayIdx + 1));
            $cleanedItems = [];

            if (!empty($day['items']) && is_array($day['items'])) {
                foreach ($day['items'] as $itemIdx => $item) {
                    if (empty($item['title']) && empty($item['item_type'])) {
                        continue;
                    }
                    $cleanedItems[] = [
                        'item_type' => $item['item_type'] ?? 'activity',
                        'item_id' => !empty($item['item_id']) ? (int) $item['item_id'] : null,
                        'supplier_id' => !empty($item['supplier_id']) ? (int) $item['supplier_id'] : null,
                        'title' => trim($item['title'] ?? ''),
                        'description' => trim($item['description'] ?? ''),
                        'room_type' => trim($item['room_type'] ?? ''),
                        'price' => (float) ($item['price'] ?? 0),
                        'sort_order' => (int) ($item['sort_order'] ?? $itemIdx),
                    ];
                }
            }

            $cleanedDays[] = [
                'day_number' => $dayNum,
                'title' => trim($day['title'] ?? ('Day ' . $dayNum)),
                'overnight_location' => trim($day['overnight_location'] ?? ''),
                'description' => trim($day['description'] ?? ''),
                'highlights' => $this->parseList($day['highlights'] ?? []),
                'inclusions' => $this->parseList($day['inclusions'] ?? []),
                'exclusions' => $this->parseList($day['exclusions'] ?? []),
                'items' => $cleanedItems,
            ];
        }

        return $cleanedDays;
    }

    /**
     * Parse text lines or arrays into clean string lists
     */
    protected function parseList($input): array
    {
        if (empty($input)) {
            return [];
        }

        if (is_array($input)) {
            $cleaned = [];
            foreach ($input as $item) {
                if (is_string($item) || is_numeric($item)) {
                    $str = trim((string) $item, " \t\n\r\0\x0B-•*\xC2\xA0");
                    if ($str !== '') {
                        $cleaned[] = $str;
                    }
                }
            }
            return array_values($cleaned);
        }

        if (is_string($input)) {
            $text = preg_replace('/<\/(p|li|h[1-6]|div)>/i', "\n", $input);
            $text = preg_replace('/<br\s*\/?>/i', "\n", $text);
            $text = strip_tags($text);
            $lines = explode("\n", $text);
            $clean = [];
            foreach ($lines as $line) {
                $lineStr = html_entity_decode($line, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                $lineStr = trim($lineStr, " \t\n\r\0\x0B-•*\xC2\xA0");
                if ($lineStr !== '') {
                    $clean[] = $lineStr;
                }
            }
            return array_values($clean);
        }

        return [];
    }

    /**
     * Resolve supplier IDs array, automatically creating new Supplier models for custom strings.
     */
    protected function resolveSupplierIds(array $rawSupplierIds, ?int $destinationId = null): array
    {
        $resolved = [];
        foreach ($rawSupplierIds as $val) {
            if (is_numeric($val) && Supplier::where('id', (int) $val)->exists()) {
                $resolved[] = (int) $val;
            } elseif (is_string($val) && trim($val) !== '') {
                $name = trim($val);
                // Strip out any trailing " (Custom / New)" text if appended by Select2 createTag
                $name = preg_replace('/\s*\(Custom\s*\/?\s*New\)$/i', '', $name);
                $supplier = Supplier::firstOrCreate(
                    ['name' => $name],
                    [
                        'type' => 'Other',
                        'destination_id' => $destinationId,
                        'is_active' => true,
                    ]
                );
                $resolved[] = $supplier->id;
            }
        }
        return array_values(array_unique($resolved));
    }

    /**
     * Ensure fixed_itineraries table has all columns, and strip any non-existent columns from payload.
     */
    protected function filterColumnsIfMissing(array $data): array
    {
        FixedItinerary::ensureTableSchema();

        try {
            $tableColumns = \Illuminate\Support\Facades\Schema::getColumnListing('fixed_itineraries');
            if (!empty($tableColumns)) {
                return array_intersect_key($data, array_flip($tableColumns));
            }
        } catch (\Throwable $e) {
            // fallback
        }

        return $data;
    }

    protected function duplicateTitle(string $title): string
    {
        $base = Str::limit($title, 248, '');
        $candidate = $base . ' (Copy)';
        $number = 2;

        while (FixedItinerary::where('title', $candidate)->exists()) {
            $suffix = " (Copy {$number})";
            $candidate = Str::limit($title, 255 - strlen($suffix), '') . $suffix;
            $number++;
        }

        return $candidate;
    }
}
