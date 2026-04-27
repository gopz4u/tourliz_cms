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
            'image' => getImageUrl($this->image),
            'gallery' => $this->gallery ? array_map(function($img) {
                return getImageUrl($img);
            }, $this->gallery) : [],
            'included_services' => $this->included_services ?? [],
            'excluded_services' => $this->excluded_services ?? [],
            'itinerary' => $this->itinerary ?? [],
            'addon_amenities' => $this->addon_amenities ?? [],
            'is_featured' => $this->featured ?? false,
            'place' => $this->whenLoaded('place', function() {
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

