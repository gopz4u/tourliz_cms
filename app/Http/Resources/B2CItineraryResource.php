<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class B2CItineraryResource extends JsonResource
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
            'title' => $this->title,
            'slug' => $this->slug,
            'itinerary_type' => $this->itinerary_type,
            'destination' => $this->whenLoaded('destination', function () {
                return [
                    'id' => $this->destination->id,
                    'name' => $this->destination->name,
                    'slug' => $this->destination->slug ?? null,
                ];
            }),
            'duration' => [
                'days' => $this->duration_days,
                'nights' => $this->duration_nights,
                'formatted' => $this->duration_days . ' Days / ' . $this->duration_nights . ' Nights',
            ],
            'description' => $this->description,
            'short_description' => $this->b2cDetail->short_description ?? null,
            'price' => [
                'selling_price' => (float) ($this->b2cDetail->selling_price ?? 0),
                'child_price' => $this->b2cDetail->child_price ? (float) $this->b2cDetail->child_price : null,
                'currency' => 'INR',
            ],
            'image' => $this->b2cDetail->image_url ?? null,
            'gallery' => $this->b2cDetail->gallery ?? [],
            'inclusions' => $this->inclusions ?? [],
            'exclusions' => $this->exclusions ?? [],
            'terms_conditions' => $this->terms_conditions,
            'days' => $this->whenLoaded('days', function () {
                return $this->days->map(function ($day) {
                    return [
                        'day_number' => $day->day_number,
                        'title' => $day->title,
                        'description' => $day->description,
                        'overnight_location' => $day->overnight_location,
                        'items' => $day->relationLoaded('items') ? $day->items->map(function ($item) {
                            return [
                                'item_type' => $item->item_type,
                                'title' => $item->title,
                                'description' => $item->description,
                                'start_time' => $item->start_time,
                                'end_time' => $item->end_time,
                                'sort_order' => $item->sort_order,
                            ];
                        }) : [],
                    ];
                });
            }),
            'seo' => [
                'title' => $this->b2cDetail->seo_title ?? $this->title,
                'description' => $this->b2cDetail->seo_description ?? $this->description,
                'keywords' => $this->b2cDetail->seo_keywords ?? null,
            ],
            'published_at' => $this->created_at ? $this->created_at->toISOString() : null,
        ];
    }
}
