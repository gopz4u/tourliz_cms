<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class B2BItineraryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $itineraryId = $this->route('id') ?? $this->route('b2b');

        return [
            'title' => 'required|string|max:255',
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('itineraries', 'slug')->ignore($itineraryId),
            ],
            'destination_id' => 'required|exists:destinations,id',
            'duration_days' => 'required|integer|min:1',
            'duration_nights' => 'required|integer|min:0',
            'agency_id' => 'nullable|exists:agencies,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'net_rate' => 'required|numeric|min:0',
            'agent_rate' => 'required|numeric|min:0',
            'markup_percentage' => 'nullable|numeric|min:0',
            'markup_amount' => 'nullable|numeric|min:0',
            'commission' => 'nullable|numeric|min:0',
            'min_pax' => 'required|integer|min:1',
            'max_pax' => 'required|integer|min:1',
            'hotel_category' => 'nullable|string|max:255',
            'room_category' => 'nullable|string|max:255',
            'vehicle_category' => 'nullable|string|max:255',
            'guide_required' => 'nullable|boolean',
            'validity_start' => 'nullable|date',
            'validity_end' => 'nullable|date|after_or_equal:validity_start',
            'operational_notes' => 'nullable|string',
            'agent_notes' => 'nullable|string',
            'inclusions' => 'nullable|array',
            'exclusions' => 'nullable|array',
            'terms_conditions' => 'nullable|string',
            'status' => 'required|in:draft,published,archived',
            'is_published' => 'nullable|boolean',
            'days' => 'required|array|min:1',
            'days.*.day_number' => 'required|integer|min:1',
            'days.*.title' => 'nullable|string|max:255',
            'days.*.description' => 'nullable|string',
            'days.*.overnight_location' => 'nullable|string|max:255',
        ];
    }
}
