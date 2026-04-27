<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AttractionResource extends JsonResource
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
            'offer_price' => $this->offer_price,
            'price_2_6' => $this->price_2_6,
            'price_6_10' => $this->price_6_10,
            'currency' => $this->currency ?? 'USD',
            'image' => getImageUrl($this->image),
            'gallery' => $this->gallery ? array_map(function($img) {
                return getImageUrl($img);
            }, $this->gallery) : [],
            'place' => $this->whenLoaded('place', function() {
                return [
                    'id' => $this->destination->id,
                    'name' => $this->destination->name,
                    'slug' => $this->destination->slug,
                ];
            }),
            'package' => $this->whenLoaded('package', function() {
                return [
                    'id' => $this->package->id,
                    'name' => $this->package->name,
                    'slug' => $this->package->slug,
                ];
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

