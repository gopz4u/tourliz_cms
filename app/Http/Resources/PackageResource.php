<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PackageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
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
            'currency' => $this->currency ?? 'USD',
            'duration_days' => $this->duration_days,
            'duration_nights' => $this->duration_nights,
            'image' => getImageUrl($this->image),
            'gallery' => $this->gallery ? array_map(function($img) {
                return getImageUrl($img);
            }, $this->gallery) : [],
            'highlights' => $this->highlights ?? [],
            'inclusions' => $this->inclusions ?? [],
            'exclusions' => $this->exclusions ?? [],
            'itinerary' => $this->itinerary ?? [],
            'days' => $this->whenLoaded('days', function () {
                return $this->days->map(function ($day) {
                    return [
                        'id' => $day->id,
                        'day_number' => $day->day_number,
                        'title' => $day->title,
                        'description' => $day->description,
                        'highlights' => $day->highlights ?? [],
                        'inclusions' => $day->inclusions ?? [],
                        'exclusions' => $day->exclusions ?? [],
                        'meal_plan' => $day->meal_plan ?? [],
                    ];
                });
            }),
            'max_persons' => $this->max_persons,
            'min_persons' => $this->min_persons,
            'is_featured' => $this->is_featured,
            'addon_amenities' => $this->addon_amenities ?? [],
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
