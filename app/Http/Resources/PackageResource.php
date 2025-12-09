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
            'currency' => $this->currency,
            'duration_days' => $this->duration_days,
            'duration_nights' => $this->duration_nights,
            'image' => $this->image ? url('storage/' . $this->image) : null,
            'gallery' => $this->gallery ? array_map(function($img) {
                return url('storage/' . $img);
            }, $this->gallery) : [],
            'inclusions' => $this->inclusions ?? [],
            'exclusions' => $this->exclusions ?? [],
            'itinerary' => $this->itinerary ?? [],
            'max_persons' => $this->max_persons,
            'min_persons' => $this->min_persons,
            'is_featured' => $this->is_featured,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
