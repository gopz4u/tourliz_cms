<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class B2BItineraryResource extends JsonResource
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
                ];
            }),
            'duration' => [
                'days' => $this->duration_days,
                'nights' => $this->duration_nights,
            ],
            'description' => $this->description,
            'b2b_rates' => [
                'net_rate' => (float) ($this->b2bDetail->net_rate ?? 0),
                'agent_rate' => (float) ($this->b2bDetail->agent_rate ?? 0),
                'markup_percentage' => (float) ($this->b2bDetail->markup_percentage ?? 0),
                'markup_amount' => (float) ($this->b2bDetail->markup_amount ?? 0),
                'commission' => (float) ($this->b2bDetail->commission ?? 0),
            ],
            'pax_limits' => [
                'min_pax' => $this->b2bDetail->min_pax ?? 1,
                'max_pax' => $this->b2bDetail->max_pax ?? 50,
            ],
            'categories' => [
                'hotel_category' => $this->b2bDetail->hotel_category ?? null,
                'room_category' => $this->b2bDetail->room_category ?? null,
                'vehicle_category' => $this->b2bDetail->vehicle_category ?? null,
            ],
            'operations' => [
                'guide_required' => (bool) ($this->b2bDetail->guide_required ?? false),
                'validity_start' => $this->b2bDetail->validity_start ? $this->b2bDetail->validity_start->format('Y-m-d') : null,
                'validity_end' => $this->b2bDetail->validity_end ? $this->b2bDetail->validity_end->format('Y-m-d') : null,
                'operational_notes' => $this->b2bDetail->operational_notes ?? null,
                'agent_notes' => $this->b2bDetail->agent_notes ?? null,
            ],
            'agency' => $this->whenLoaded('b2bDetail.agency', function () {
                return $this->b2bDetail->agency ? [
                    'id' => $this->b2bDetail->agency->id,
                    'name' => $this->b2bDetail->agency->name,
                ] : null;
            }),
            'supplier' => $this->whenLoaded('b2bDetail.supplier', function () {
                return $this->b2bDetail->supplier ? [
                    'id' => $this->b2bDetail->supplier->id,
                    'name' => $this->b2bDetail->supplier->name,
                ] : null;
            }),
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
            'status' => $this->status,
            'is_published' => (bool) $this->is_published,
        ];
    }
}
