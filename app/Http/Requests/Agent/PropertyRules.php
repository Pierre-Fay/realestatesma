<?php

namespace App\Http\Requests\Agent;

use App\Enums\CategoryGroupType;
use Illuminate\Validation\Rule;

trait PropertyRules
{
    /**
     * Validation rules shared by listing creation and update.
     *
     * @return array<string, mixed>
     */
    protected function propertyRules(): array
    {
        $agentCategoryGroups = [
            CategoryGroupType::PROPERTY_TYPE->value,
            CategoryGroupType::PROPERTY_AREA->value,
            CategoryGroupType::PROPERTY_FEATURE->value,
        ];

        return [
            'name' => ['required', 'string', 'max:100'],
            'address' => ['nullable', 'string', 'max:255'],
            'zip' => ['required', 'string', 'max:10'],
            'price_usd' => ['required', 'numeric', 'min:0'],
            'price_mxn' => ['required', 'numeric', 'min:0'],
            'show_both_prices' => ['boolean'],
            'description' => ['nullable', 'string'],
            'lot_meters' => ['required', 'numeric', 'min:0'],
            'construction_meters' => ['required', 'numeric', 'min:0'],
            'bedrooms' => ['required', 'integer', 'min:0'],
            'bathrooms' => ['required', 'integer', 'min:0'],
            'half_bathrooms' => ['required', 'integer', 'min:0'],
            'parking_spaces' => ['required', 'integer', 'min:0'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'categories' => ['array'],
            'categories.*' => ['integer', Rule::exists('categories', 'id')->whereIn('group_type', $agentCategoryGroups)],
            'featured_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'gallery' => ['array', 'max:10'],
            'gallery.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'remove_images' => ['array'],
            'remove_images.*' => ['integer'],
        ];
    }
}
