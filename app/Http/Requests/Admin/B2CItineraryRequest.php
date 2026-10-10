<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class B2CItineraryRequest extends FormRequest
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
        $itineraryId = $this->route('id') ?? $this->route('b2c');

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
            'description' => 'nullable|string',
            'short_description' => 'nullable|string',
            'selling_price' => 'required|numeric|min:0',
            'child_price' => 'nullable|numeric|min:0',
            'is_template' => 'nullable|boolean',
            'quotation_reference' => 'nullable|string|max:255',
            'customer_name' => 'nullable|string|max:255',
            'customer_whatsapp' => 'nullable|string|max:255',
            'customer_email' => 'nullable|email|max:255',
            'travel_date_start' => 'nullable|date',
            'travel_date_end' => 'nullable|date|after_or_equal:travel_date_start',
            'adults' => 'nullable|integer|min:1',
            'children' => 'nullable|integer|min:0',
            'rooms' => 'nullable|integer|min:1',
            'lead_source' => 'nullable|string|max:255',
            'inclusions' => 'nullable|string',
            'exclusions' => 'nullable|string',
            'terms_conditions' => 'nullable|string',
            'status' => 'required|in:draft,published,archived',
            'is_published' => 'nullable|boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'seo_title' => 'nullable|string|max:255',
            'seo_description' => 'nullable|string',
            'seo_keywords' => 'nullable|string',
            'days' => 'required|array|min:1',
            'days.*.day_number' => 'required|integer|min:1',
            'days.*.title' => 'nullable|string|max:255',
            'days.*.description' => 'nullable|string',
            'days.*.overnight_location' => 'nullable|string|max:255',
        ];
    }
}
