<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Helpers\ItineraryHelper;

class ItineraryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $itinerary = $this->resource['itinerary'] ?? [];
        $package = $this->resource['package'] ?? null;

        // Enrich itinerary with database data
        $enrichedItinerary = ItineraryHelper::enrichItinerary($itinerary);

        // Calculate costs
        $costBreakdown = ItineraryHelper::calculateTotalCost($enrichedItinerary, $package->currency ?? 'USD');

        return [
            'package' => [
                'id' => $package->id ?? null,
                'name' => $package->name ?? null,
                'slug' => $package->slug ?? null,
                'duration' => $package->duration ?? null,
                'currency' => $package->currency ?? 'USD',
            ],
            'summary' => [
                'total_days' => count($enrichedItinerary),
                'total_nights' => count($enrichedItinerary) > 0 ? count($enrichedItinerary) - 1 : 0,
                'total_places' => $this->countPlaces($enrichedItinerary),
                'total_activities' => $this->countActivities($enrichedItinerary),
            ],
            'cost_breakdown' => $costBreakdown,
            'itinerary' => $enrichedItinerary,
        ];
    }

    /**
     * Count total places in itinerary
     *
     * @param array $itinerary
     * @return int
     */
    private function countPlaces($itinerary)
    {
        $count = 0;
        foreach ($itinerary as $day) {
            if (isset($day['places']) && is_array($day['places'])) {
                $count += count($day['places']);
            }
        }
        return $count;
    }

    /**
     * Count total activities in itinerary
     *
     * @param array $itinerary
     * @return int
     */
    private function countActivities($itinerary)
    {
        $count = 0;
        foreach ($itinerary as $day) {
            if (isset($day['activities']) && is_array($day['activities'])) {
                $count += count($day['activities']);
            }
        }
        return $count;
    }
}
