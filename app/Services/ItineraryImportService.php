<?php

namespace App\Services;

use App\Models\B2CItinerary;
use App\Models\FixedItinerary;
use App\Models\GroupPackage;
use App\Models\Itinerary;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Converts every legacy itinerary source into the normalized itinerary-days-items
 * structure used by the shared B2C itinerary module.
 *
 * Source records are never modified. Importing always creates a new draft,
 * which protects the source master and gives staff a quote/template snapshot
 * they can edit safely.
 */
class ItineraryImportService
{
    protected $itineraryService;

    public function __construct(ItineraryService $itineraryService)
    {
        $this->itineraryService = $itineraryService;
    }

    public function importToB2C(string $sourceType, int $sourceId, int $destinationId, bool $asTemplate = true): Itinerary
    {
        switch ($sourceType) {
            case 'fixed':
                $data = $this->fromFixedItinerary(FixedItinerary::findOrFail($sourceId));
                break;
            case 'group':
                $data = $this->fromGroupPackage(GroupPackage::findOrFail($sourceId));
                break;
            case 'legacy_b2c':
                $data = $this->fromLegacyB2C(B2CItinerary::findOrFail($sourceId));
                break;
            case 'unified_b2c':
                $data = $this->fromUnifiedB2C(
                    Itinerary::where('itinerary_type', 'b2c')
                        ->with(['days.items', 'b2cDetail'])
                        ->findOrFail($sourceId)
                );
                break;
            default:
                throw (new ModelNotFoundException())->setModel(Itinerary::class, [$sourceId]);
        }

        $data['destination_id'] = $destinationId;
        $data['is_template'] = $asTemplate;
        $data['status'] = 'draft';
        $data['is_published'] = false;
        $data['title'] = $asTemplate
            ? 'Copy of ' . $data['title']
            : $data['title'] . ' Quotation';

        return $this->itineraryService->createB2CItinerary($data);
    }

    protected function fromFixedItinerary(FixedItinerary $source): array
    {
        return $this->baseData([
            'title' => $source->title,
            'duration_days' => $source->duration_days,
            'duration_nights' => $source->duration_nights,
            'description' => $source->itinerary_description,
            'short_description' => $source->short_description ?: $source->itinerary_description,
            'highlights' => $source->highlights,
            'inclusions' => $source->inclusions,
            'exclusions' => $source->exclusions,
            'terms_conditions' => $source->terms_conditions,
            'selling_price' => $source->fixed_price,
            'currency' => $source->currency,
            'days' => $this->normalizeStandardDays($source->days),
        ]);
    }

    protected function fromGroupPackage(GroupPackage $source): array
    {
        return $this->baseData([
            'title' => $source->name,
            'duration_days' => $source->duration_days,
            'duration_nights' => $source->duration_nights,
            'description' => $source->description,
            'short_description' => $source->short_description,
            'inclusions' => $source->included_services,
            'exclusions' => $source->excluded_services,
            'selling_price' => $source->discount_price ?: $source->price,
            'currency' => $source->currency,
            'days' => $this->normalizeGroupPackageDays($source->itinerary),
        ]);
    }

    protected function fromLegacyB2C(B2CItinerary $source): array
    {
        return $this->baseData([
            'title' => $source->title,
            'duration_days' => $source->duration_days,
            'duration_nights' => max(0, (int) $source->duration_days - 1),
            'description' => $source->short_description,
            'short_description' => $source->short_description,
            'highlights' => $source->highlights,
            'inclusions' => $source->inclusions,
            'exclusions' => $source->exclusions,
            'terms_conditions' => $source->terms_conditions,
            'selling_price' => $source->customer_total_price ?: $source->total_price,
            'currency' => $source->customer_currency ?: $source->currency,
            'days' => $this->normalizeLegacyB2CDays($source->itinerary),
        ]);
    }

    protected function fromUnifiedB2C(Itinerary $source): array
    {
        $days = [];
        foreach ($source->days as $day) {
            $items = [];
            foreach ($day->items as $item) {
                $items[] = [
                    'item_type' => $item->item_type,
                    'item_id' => $item->item_id,
                    'supplier_id' => $item->supplier_id,
                    'title' => $item->title,
                    'description' => $item->description,
                    'price' => $item->price,
                    'cost' => $item->cost,
                    'room_type' => $item->room_type,
                    'start_time' => $item->start_time,
                    'end_time' => $item->end_time,
                    'sort_order' => $item->sort_order,
                ];
            }

            $days[] = [
                'day_number' => $day->day_number,
                'title' => $day->title,
                'description' => $day->description,
                'overnight_location' => $day->overnight_location,
                'highlights' => $day->highlights,
                'inclusions' => $day->inclusions,
                'exclusions' => $day->exclusions,
                'items' => $items,
            ];
        }

        return $this->baseData([
            'title' => $source->title,
            'duration_days' => $source->duration_days,
            'duration_nights' => $source->duration_nights,
            'description' => $source->description,
            'short_description' => optional($source->b2cDetail)->short_description,
            'highlights' => $source->highlights,
            'inclusions' => $source->inclusions,
            'exclusions' => $source->exclusions,
            'terms_conditions' => $source->terms_conditions,
            'selling_price' => optional($source->b2cDetail)->selling_price,
            'child_price' => optional($source->b2cDetail)->child_price,
            'currency' => $source->currency ?: optional($source->b2cDetail)->currency,
            'days' => $days,
        ]);
    }

    protected function baseData(array $data): array
    {
        $days = !empty($data['days']) ? array_values($data['days']) : [[
            'day_number' => 1,
            'title' => 'Day 1',
            'description' => '',
            'overnight_location' => '',
            'highlights' => [],
            'inclusions' => [],
            'exclusions' => [],
            'items' => [],
        ]];

        $durationDays = max(1, (int) ($data['duration_days'] ?? count($days)));

        $result = array_merge([
            'title' => 'Untitled Itinerary',
            'duration_days' => $durationDays,
            'duration_nights' => max(0, (int) ($data['duration_nights'] ?? ($durationDays - 1))),
            'description' => null,
            'short_description' => null,
            'highlights' => [],
            'inclusions' => [],
            'exclusions' => [],
            'terms_conditions' => null,
            'selling_price' => 0,
            'child_price' => null,
            'currency' => 'MYR',
            'days' => $days,
        ], $data);

        $result['duration_days'] = $durationDays;
        $result['duration_nights'] = max(0, (int) ($data['duration_nights'] ?? ($durationDays - 1)));
        $result['currency'] = !empty($data['currency']) ? $data['currency'] : 'MYR';
        $result['days'] = $days;

        return $result;
    }

    protected function normalizeStandardDays($days): array
    {
        $normalized = [];
        foreach ((array) $days as $index => $day) {
            $day = is_array($day) ? $day : [];
            $items = [];
            foreach ((array) ($day['items'] ?? []) as $itemIndex => $item) {
                $item = is_array($item) ? $item : [];
                if (empty($item['title']) && empty($item['item_type'])) {
                    continue;
                }
                $items[] = $this->normalizeItem($item, $itemIndex);
            }

            $number = (int) ($day['day_number'] ?? $day['day'] ?? ($index + 1));
            $normalized[] = [
                'day_number' => $number,
                'title' => $day['title'] ?? ('Day ' . $number),
                'description' => $day['description'] ?? ($day['notes'] ?? ''),
                'overnight_location' => $day['overnight_location'] ?? '',
                'highlights' => $day['highlights'] ?? [],
                'inclusions' => $day['inclusions'] ?? [],
                'exclusions' => $day['exclusions'] ?? [],
                'items' => $items,
            ];
        }

        return $normalized;
    }

    protected function normalizeGroupPackageDays($days): array
    {
        $normalized = [];
        foreach ((array) $days as $index => $day) {
            $day = is_array($day) ? $day : [];
            $number = (int) ($day['day'] ?? $day['day_number'] ?? ($index + 1));
            $items = [];

            $hotel = $day['hotel'] ?? null;
            if (is_string($hotel)) {
                $hotel = ['name' => $hotel];
            }
            if (is_array($hotel) && !empty($hotel['name'])) {
                $items[] = $this->normalizeItem([
                    'item_type' => 'hotel',
                    'item_id' => $hotel['hotel_id'] ?? null,
                    'supplier_id' => $hotel['supplier_id'] ?? null,
                    'title' => $hotel['name'],
                    'description' => $hotel['description'] ?? '',
                    'price' => $hotel['price_per_night'] ?? 0,
                    'room_type' => $hotel['type'] ?? null,
                ], count($items));
            }

            foreach ((array) ($day['transport'] ?? []) as $transport) {
                if (is_string($transport)) {
                    $transport = ['mode' => $transport];
                }
                if (!is_array($transport)) {
                    continue;
                }
                $items[] = $this->normalizeItem([
                    'item_type' => 'transport',
                    'item_id' => $transport['transport_id'] ?? null,
                    'supplier_id' => $transport['supplier_id'] ?? null,
                    'title' => $transport['name'] ?? $transport['mode'] ?? $transport['type'] ?? 'Transport',
                    'description' => $transport['description'] ?? '',
                    'price' => $transport['price'] ?? 0,
                    'room_type' => $transport['vehicle_type'] ?? null,
                ], count($items));
            }

            foreach (['places', 'spots'] as $spotKey) {
                foreach ((array) ($day[$spotKey] ?? []) as $spot) {
                    if (is_string($spot)) {
                        $spot = ['name' => $spot];
                    }
                    if (!is_array($spot)) {
                        continue;
                    }
                    $items[] = $this->normalizeItem([
                        'item_type' => 'spot',
                        'item_id' => $spot['spot_id'] ?? $spot['tourist_spot_id'] ?? $spot['id'] ?? null,
                        'supplier_id' => $spot['supplier_id'] ?? null,
                        'title' => $spot['name'] ?? $spot['attraction_name'] ?? 'Tourist Spot',
                        'description' => $spot['description'] ?? '',
                        'price' => $spot['price'] ?? 0,
                    ], count($items));
                }
            }

            foreach ((array) ($day['activities'] ?? []) as $activity) {
                if (is_string($activity)) {
                    $activity = ['name' => $activity];
                }
                if (!is_array($activity)) {
                    continue;
                }
                $ticket = is_array($activity['entry_ticket'] ?? null) ? $activity['entry_ticket'] : [];
                $items[] = $this->normalizeItem([
                    'item_type' => 'activity',
                    'item_id' => $activity['activity_id'] ?? $activity['id'] ?? null,
                    'supplier_id' => $activity['supplier_id'] ?? null,
                    'title' => $activity['name'] ?? 'Activity',
                    'description' => $activity['description'] ?? '',
                    'price' => $activity['price'] ?? $ticket['adult_price'] ?? 0,
                ], count($items));
            }

            foreach ((array) ($day['meals'] ?? []) as $mealType => $meal) {
                if (is_array($meal)) {
                    $title = $meal['name'] ?? ucfirst((string) $mealType);
                    $description = $meal['description'] ?? '';
                    $price = $meal['price'] ?? 0;
                } else {
                    if (empty($meal) || stripos((string) $meal, 'included') === false) {
                        continue;
                    }
                    $title = ucfirst((string) $mealType);
                    $description = (string) $meal;
                    $price = 0;
                }
                $items[] = $this->normalizeItem([
                    'item_type' => 'meal',
                    'title' => $title,
                    'description' => $description,
                    'price' => $price,
                ], count($items));
            }

            $normalized[] = [
                'day_number' => $number,
                'title' => $day['title'] ?? ('Day ' . $number),
                'description' => $day['description'] ?? ($day['notes'] ?? ''),
                'overnight_location' => $day['overnight_location'] ?? '',
                'highlights' => $day['highlights'] ?? [],
                'inclusions' => $day['inclusions'] ?? [],
                'exclusions' => $day['exclusions'] ?? [],
                'items' => $items,
            ];
        }

        return $normalized;
    }

    protected function normalizeLegacyB2CDays($days): array
    {
        $normalized = [];
        foreach ((array) $days as $index => $day) {
            $day = is_array($day) ? $day : [];
            if (!empty($day['items'])) {
                $normalized = array_merge($normalized, $this->normalizeStandardDays([$day]));
                continue;
            }

            $number = (int) ($day['day'] ?? $day['day_number'] ?? ($index + 1));
            $items = [];
            $definitions = [
                'hotels' => ['hotel', 'hotel_id', 'price_per_night'],
                'transport' => ['transport', 'transport_id', 'price'],
                'spots' => ['spot', 'tourist_spot_id', 'price_per_hour'],
                'places' => ['spot', 'tourist_spot_id', 'price_per_hour'],
                'activities' => ['activity', 'activity_id', 'price'],
                'meals' => ['meal', 'meal_id', 'price'],
            ];

            foreach ($definitions as $key => $definition) {
                foreach ((array) ($day[$key] ?? []) as $entry) {
                    if (is_string($entry)) {
                        $entry = ['name' => $entry];
                    }
                    if (!is_array($entry)) {
                        continue;
                    }
                    $ticket = is_array($entry['entry_ticket'] ?? null) ? $entry['entry_ticket'] : [];
                    $items[] = $this->normalizeItem([
                        'item_type' => $definition[0],
                        'item_id' => $entry[$definition[1]] ?? $entry['item_id'] ?? null,
                        'supplier_id' => $entry['supplier_id'] ?? null,
                        'title' => $entry['name'] ?? $entry['title'] ?? ucfirst($definition[0]),
                        'description' => $entry['description'] ?? '',
                        'price' => $entry[$definition[2]] ?? $entry['price'] ?? $ticket['adult_price'] ?? 0,
                        'room_type' => $entry['type'] ?? $entry['vehicle_type'] ?? null,
                    ], count($items));
                }
            }

            $normalized[] = [
                'day_number' => $number,
                'title' => $day['title'] ?? ('Day ' . $number),
                'description' => $day['description'] ?? ($day['notes'] ?? ''),
                'overnight_location' => $day['overnight_location'] ?? '',
                'highlights' => $day['highlights'] ?? [],
                'inclusions' => $day['inclusions'] ?? [],
                'exclusions' => $day['exclusions'] ?? [],
                'items' => $items,
            ];
        }

        return $normalized;
    }

    protected function normalizeItem(array $item, int $index): array
    {
        $type = strtolower((string) ($item['item_type'] ?? 'activity'));
        if ($type === 'tourist_spot' || $type === 'place') {
            $type = 'spot';
        }

        return [
            'item_type' => $type,
            'item_id' => !empty($item['item_id']) ? (int) $item['item_id'] : null,
            'supplier_id' => !empty($item['supplier_id']) ? (int) $item['supplier_id'] : null,
            'title' => trim((string) ($item['title'] ?? ucfirst($type))),
            'description' => $item['description'] ?? null,
            'price' => (float) ($item['price'] ?? 0),
            'cost' => (float) ($item['cost'] ?? 0),
            'room_type' => $item['room_type'] ?? null,
            'start_time' => $item['start_time'] ?? null,
            'end_time' => $item['end_time'] ?? null,
            'sort_order' => (int) ($item['sort_order'] ?? $index),
        ];
    }
}
