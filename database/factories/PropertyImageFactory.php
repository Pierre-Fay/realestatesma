<?php

namespace Database\Factories;

use App\Enums\PropertyImageType;
use App\Models\Property;
use App\Models\PropertyImage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PropertyImage>
 */
class PropertyImageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'property_id' => Property::factory(),
            'path' => 'properties/'.fake()->uuid().'.jpg',
            'type' => PropertyImageType::GALLERY,
            'sort_order' => fake()->numberBetween(0, 10),
        ];
    }

    /**
     * Indicate the image is the featured image.
     */
    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => PropertyImageType::FEATURED_IMAGE,
        ]);
    }
}
