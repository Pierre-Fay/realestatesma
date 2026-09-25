<?php

namespace Database\Factories;

use App\Models\OpenHouse;
use App\Models\Property;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OpenHouse>
 */
class OpenHouseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startsAt = fake()->dateTimeBetween('+1 day', '+30 days');

        return [
            'property_id' => Property::factory(),
            'starts_at' => $startsAt,
            'ends_at' => (clone $startsAt)->modify('+3 hours'),
            'notes' => fake()->optional()->paragraph(),
            'notes_es' => null,
            'is_published' => fake()->boolean(),
        ];
    }
}
