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
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'short_description' => $this->short_description,
            'price' => $this->price,
            'discount_price' => $this->discount_price,
            'price_2_6' => $this->price_2_6,
            'price_6_10' => $this->price_6_10,
            'currency' => $this->currency ?? 'USD',
            'duration' => $this->duration,
            'duration_days' => $this->duration_days,
            'duration_nights' => $this->duration_nights,
            'fixed_itinerary_id' => $this->fixed_itinerary_id,
            'image' => getImageUrl($this->image),
            'hero_image' => getImageUrl($this->image),
            'gallery' => $this->gallery ? array_map(function($img) {
                return getImageUrl($img);
            }, $this->gallery) : [],
            'included_services' => $this->included_services ?? [],
            'excluded_services' => $this->excluded_services ?? [],
            'itinerary' => $this->itinerary ?? [],
            'upcoming_departures' => $this->whenLoaded('departures', function () {
                return $this->departures->map(function ($departure) {
                    return [
                        'id' => $departure->id,
                        'title' => $departure->title,
                        'announcement_date' => optional($departure->announcement_date)->toDateString(),
                        'departure_date' => optional($departure->departure_date)->toDateString(),
                        'return_date' => optional($departure->return_date)->toDateString(),
                        'adult_rate' => $departure->adult_rate,
                        'child_rate' => $departure->child_rate,
                        'currency' => $departure->currency,
                        'capacity' => $departure->capacity,
                        'available_seats' => $departure->available_seats,
                        'status' => $departure->status,
                    ];
                })->values();
            }, []),
            'addon_amenities' => $this->addon_amenities ?? [],
            'is_featured' => $this->featured ?? false,
            'place' => $this->whenLoaded('destination', function() {
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

