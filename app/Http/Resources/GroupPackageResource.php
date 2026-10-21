<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class GroupPackageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request)
    {
        $hasUpcomingDeparture = $this->departure_date && $this->departure_date->greaterThanOrEqualTo(today());

        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'short_description' => $this->short_description,
            'price' => $this->price,
            'child_price' => $this->child_price,
            'discount_price' => $this->discount_price,
            'price_2_6' => $this->price_2_6,
            'price_6_10' => $this->price_6_10,
            'currency' => $this->currency ?? 'USD',
            'duration' => $this->duration,
            'duration_days' => $this->duration_days,
            'duration_nights' => $this->duration_nights,
            'departure_date' => optional($this->departure_date)->toDateString(),
            'return_date' => optional($this->return_date)->toDateString(),
            'capacity' => $this->total_pax,
            'fixed_itinerary_id' => $this->fixed_itinerary_id,
            'image' => getImageUrl($this->image),
            'hero_image' => getImageUrl($this->image),
            'gallery' => $this->gallery ? array_map(function($img) {
                return getImageUrl($img);
            }, $this->gallery) : [],
            'included_services' => $this->included_services ?? [],
            'excluded_services' => $this->excluded_services ?? [],
            'itinerary' => $this->itinerary ?? [],
            'source_fixed_itinerary' => $this->whenLoaded('fixedItinerary', function () {
                if (!$this->fixedItinerary) {
                    return null;
                }

                return [
                    'id' => $this->fixedItinerary->id,
                    'title' => $this->fixedItinerary->title,
                    'duration_days' => $this->fixedItinerary->duration_days,
                    'duration_nights' => $this->fixedItinerary->duration_nights,
                    'short_description' => $this->fixedItinerary->short_description,
                    'highlights' => $this->fixedItinerary->highlights ?: [],
                    'days' => $this->fixedItinerary->days ?: [],
                ];
            }),
            // Compatibility for the existing public site: this is a view of
            // the Group Tour's own direct date, not a group_departures record.
            'upcoming_departures' => $hasUpcomingDeparture ? [[
                'id' => null,
                'title' => $this->name,
                'announcement_date' => optional($this->announcement_date)->toDateString(),
                'departure_date' => $this->departure_date->toDateString(),
                'return_date' => optional($this->return_date)->toDateString(),
                'adult_rate' => $this->price,
                'child_rate' => $this->child_price,
                'currency' => $this->currency,
                'capacity' => $this->total_pax,
                'available_seats' => $this->total_pax,
                'status' => 'open',
            ]] : [],
            'addon_amenities' => $this->addon_amenities ?? [],
            'is_featured' => $this->featured ?? false,
            'place' => $this->whenLoaded('destination', function() {
                if (!$this->destination) {
                    return null;
                }

                return [
                    'id' => $this->destination->id,
                    'name' => $this->destination->name,
                    'slug' => $this->destination->slug,
                ];
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

