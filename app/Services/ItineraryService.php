<?php

namespace App\Services;

use App\Models\Itinerary;
use App\Models\ItineraryB2bDetail;
use App\Models\ItineraryB2cDetail;
use App\Models\ItineraryDay;
use App\Models\ItineraryDayItem;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ItineraryService
{
    /**
     * Get paginated itineraries with filtering & eager loading
     */
    public function getPaginatedItineraries(string $type, array $filters = [], int $perPage = 15)
    {
        $query = Itinerary::where('itinerary_type', $type)
            ->with(['destination', 'creator']);

        if ($type === 'b2c') {
            $query->with('b2cDetail');
        } else {
            $query->with(['b2bDetail.agency', 'b2bDetail.supplier']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%")
                  ->orWhereHas('destination', function ($dq) use ($search) {
                      $dq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if (!empty($filters['destination_id'])) {
            $query->where('destination_id', $filters['destination_id']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['is_published']) && $filters['is_published'] !== '') {
            $query->where('is_published', (bool) $filters['is_published']);
        }

        return $query->latest('created_at')->paginate($perPage)->withQueryString();
    }

    /**
     * Create B2C Itinerary
     */
    public function createB2CItinerary(array $data, ?string $imagePath = null): Itinerary
    {
        return DB::transaction(function () use ($data, $imagePath) {
            $slug = !empty($data['slug']) ? Str::slug($data['slug']) : Str::slug($data['title']);
            
            // Ensure unique slug
            $originalSlug = $slug;
            $count = 1;
            while (Itinerary::where('slug', $slug)->exists()) {
                $slug = "{$originalSlug}-{$count}";
                $count++;
            }

            $itinerary = Itinerary::create([
                'title' => $data['title'],
                'slug' => $slug,
                'itinerary_type' => 'b2c',
                'destination_id' => $data['destination_id'],
                'duration_days' => $data['duration_days'],
                'duration_nights' => $data['duration_nights'],
                'description' => $data['description'] ?? null,
                'highlights' => $this->parseListInput($data['highlights'] ?? []),
                'inclusions' => $this->parseListInput($data['inclusions'] ?? []),
                'exclusions' => $this->parseListInput($data['exclusions'] ?? []),
                'terms_conditions' => $data['terms_conditions'] ?? null,
                'status' => $data['status'] ?? 'draft',
                'is_published' => ($data['status'] ?? '') === 'published' || !empty($data['is_published']),
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ]);

            ItineraryB2cDetail::create([
                'itinerary_id' => $itinerary->id,
                'short_description' => $data['short_description'] ?? null,
                'selling_price' => $data['selling_price'] ?? 0,
                'child_price' => $data['child_price'] ?? null,
                'image' => $imagePath,
                'seo_title' => $data['seo_title'] ?? null,
                'seo_description' => $data['seo_description'] ?? null,
                'seo_keywords' => $data['seo_keywords'] ?? null,
            ]);

            $this->saveDaysAndItems($itinerary, $data['days'] ?? []);

            return $itinerary;
        });
    }

    /**
     * Update B2C Itinerary
     */
    public function updateB2CItinerary(Itinerary $itinerary, array $data, ?string $imagePath = null): Itinerary
    {
        return DB::transaction(function () use ($itinerary, $data, $imagePath) {
            $itinerary->update([
                'title' => $data['title'],
                'destination_id' => $data['destination_id'],
                'duration_days' => $data['duration_days'],
                'duration_nights' => $data['duration_nights'],
                'description' => $data['description'] ?? null,
                'highlights' => $this->parseListInput($data['highlights'] ?? []),
                'inclusions' => $this->parseListInput($data['inclusions'] ?? []),
                'exclusions' => $this->parseListInput($data['exclusions'] ?? []),
                'terms_conditions' => $data['terms_conditions'] ?? null,
                'status' => $data['status'] ?? 'draft',
                'is_published' => ($data['status'] ?? '') === 'published' || !empty($data['is_published']),
                'updated_by' => auth()->id(),
            ]);

            $b2cData = [
                'short_description' => $data['short_description'] ?? null,
                'selling_price' => $data['selling_price'] ?? 0,
                'child_price' => $data['child_price'] ?? null,
                'seo_title' => $data['seo_title'] ?? null,
                'seo_description' => $data['seo_description'] ?? null,
                'seo_keywords' => $data['seo_keywords'] ?? null,
            ];

            if ($imagePath) {
                $b2cData['image'] = $imagePath;
            }

            ItineraryB2cDetail::updateOrCreate(['itinerary_id' => $itinerary->id], $b2cData);

            $this->saveDaysAndItems($itinerary, $data['days'] ?? []);

            return $itinerary;
        });
    }

    /**
     * Create B2B Itinerary
     */
    public function createB2BItinerary(array $data): Itinerary
    {
        return DB::transaction(function () use ($data) {
            $slug = !empty($data['slug']) ? Str::slug($data['slug']) : Str::slug($data['title']);

            $originalSlug = $slug;
            $count = 1;
            while (Itinerary::where('slug', $slug)->exists()) {
                $slug = "{$originalSlug}-{$count}";
                $count++;
            }

            $itinerary = Itinerary::create([
                'title' => $data['title'],
                'slug' => $slug,
                'itinerary_type' => 'b2b',
                'destination_id' => $data['destination_id'],
                'duration_days' => $data['duration_days'],
                'duration_nights' => $data['duration_nights'],
                'description' => $data['description'] ?? null,
                'highlights' => $this->parseListInput($data['highlights'] ?? []),
                'inclusions' => $this->parseListInput($data['inclusions'] ?? []),
                'exclusions' => $this->parseListInput($data['exclusions'] ?? []),
                'terms_conditions' => $data['terms_conditions'] ?? null,
                'status' => $data['status'] ?? 'draft',
                'is_published' => ($data['status'] ?? '') === 'published' || !empty($data['is_published']),
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ]);

            ItineraryB2bDetail::create([
                'itinerary_id' => $itinerary->id,
                'agency_id' => $data['agency_id'] ?? null,
                'supplier_id' => $data['supplier_id'] ?? null,
                'net_rate' => $data['net_rate'] ?? 0,
                'agent_rate' => $data['agent_rate'] ?? 0,
                'markup_percentage' => $data['markup_percentage'] ?? 0,
                'markup_amount' => $data['markup_amount'] ?? 0,
                'commission' => $data['commission'] ?? 0,
                'min_pax' => $data['min_pax'] ?? 1,
                'max_pax' => $data['max_pax'] ?? 50,
                'hotel_category' => $data['hotel_category'] ?? null,
                'room_category' => $data['room_category'] ?? null,
                'vehicle_category' => $data['vehicle_category'] ?? null,
                'guide_required' => !empty($data['guide_required']),
                'validity_start' => $data['validity_start'] ?? null,
                'validity_end' => $data['validity_end'] ?? null,
                'operational_notes' => $data['operational_notes'] ?? null,
                'agent_notes' => $data['agent_notes'] ?? null,
            ]);

            $this->saveDaysAndItems($itinerary, $data['days'] ?? []);

            return $itinerary;
        });
    }

    /**
     * Update B2B Itinerary
     */
    public function updateB2BItinerary(Itinerary $itinerary, array $data): Itinerary
    {
        return DB::transaction(function () use ($itinerary, $data) {
            $itinerary->update([
                'title' => $data['title'],
                'destination_id' => $data['destination_id'],
                'duration_days' => $data['duration_days'],
                'duration_nights' => $data['duration_nights'],
                'description' => $data['description'] ?? null,
                'highlights' => $this->parseListInput($data['highlights'] ?? []),
                'inclusions' => $this->parseListInput($data['inclusions'] ?? []),
                'exclusions' => $this->parseListInput($data['exclusions'] ?? []),
                'terms_conditions' => $data['terms_conditions'] ?? null,
                'status' => $data['status'] ?? 'draft',
                'is_published' => ($data['status'] ?? '') === 'published' || !empty($data['is_published']),
                'updated_by' => auth()->id(),
            ]);

            ItineraryB2bDetail::updateOrCreate(['itinerary_id' => $itinerary->id], [
                'agency_id' => $data['agency_id'] ?? null,
                'supplier_id' => $data['supplier_id'] ?? null,
                'net_rate' => $data['net_rate'] ?? 0,
                'agent_rate' => $data['agent_rate'] ?? 0,
                'markup_percentage' => $data['markup_percentage'] ?? 0,
                'markup_amount' => $data['markup_amount'] ?? 0,
                'commission' => $data['commission'] ?? 0,
                'min_pax' => $data['min_pax'] ?? 1,
                'max_pax' => $data['max_pax'] ?? 50,
                'hotel_category' => $data['hotel_category'] ?? null,
                'room_category' => $data['room_category'] ?? null,
                'vehicle_category' => $data['vehicle_category'] ?? null,
                'guide_required' => !empty($data['guide_required']),
                'validity_start' => $data['validity_start'] ?? null,
                'validity_end' => $data['validity_end'] ?? null,
                'operational_notes' => $data['operational_notes'] ?? null,
                'agent_notes' => $data['agent_notes'] ?? null,
            ]);

            $this->saveDaysAndItems($itinerary, $data['days'] ?? []);

            return $itinerary;
        });
    }

    /**
     * Helper to sync days and day items
     */
    protected function saveDaysAndItems(Itinerary $itinerary, array $daysData)
    {
        // Delete existing days (cascade deletes day items)
        $itinerary->days()->delete();

        foreach ($daysData as $dayData) {
            $day = ItineraryDay::create([
                'itinerary_id' => $itinerary->id,
                'day_number' => $dayData['day_number'],
                'title' => $dayData['title'] ?? ('Day ' . $dayData['day_number']),
                'description' => $dayData['description'] ?? null,
                'highlights' => $this->parseListInput($dayData['highlights'] ?? []),
                'inclusions' => $this->parseListInput($dayData['inclusions'] ?? []),
                'exclusions' => $this->parseListInput($dayData['exclusions'] ?? []),
                'overnight_location' => $dayData['overnight_location'] ?? null,
            ]);

            if (!empty($dayData['items']) && is_array($dayData['items'])) {
                foreach ($dayData['items'] as $index => $itemData) {
                    ItineraryDayItem::create([
                        'itinerary_day_id' => $day->id,
                        'item_type' => $itemData['item_type'] ?? 'activity',
                        'item_id' => $itemData['item_id'] ?? null,
                        'title' => $itemData['title'],
                        'description' => $itemData['description'] ?? null,
                        'start_time' => $itemData['start_time'] ?? null,
                        'end_time' => $itemData['end_time'] ?? null,
                        'sort_order' => $itemData['sort_order'] ?? $index,
                    ]);
                }
            }
        }
    }

    /**
     * Helper to normalize text/array inputs into array of strings
     */
    private function parseListInput($input): array
    {
        if (is_array($input)) {
            return array_values(array_filter(array_map('trim', $input)));
        }
        if (is_string($input)) {
            $text = preg_replace('/<\/(p|li|h[1-6]|div)>/i', "\n", $input);
            $text = preg_replace('/<br\s*\/?>/i', "\n", $text);
            $text = strip_tags($text);
            $lines = explode("\n", $text);
            $clean = array_map(function ($line) {
                return trim(html_entity_decode($line), " \t\n\r\0\x0B-•*");
            }, $lines);
            return array_values(array_filter($clean));
        }
        return [];
    }
}
