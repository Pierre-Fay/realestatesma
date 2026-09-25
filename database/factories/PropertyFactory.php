<?php

namespace Database\Factories;

use App\Models\Property;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Property>
 */
class PropertyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->words(3, true);

        return [
            'name' => $name,
            'slug' => Str::slug($name).'-'.fake()->unique()->randomNumber(5),
            'address' => fake()->streetAddress(),
            'city' => 'San Miguel de Allende',
            'state' => 'Guanajuato',
            'country' => 'Mexico',
            'zip' => fake()->postcode(),
            'latitude' => fake()->latitude(20.8, 21.0),
            'longitude' => fake()->longitude(-100.9, -100.7),
            'is_featured' => false,
            'is_active' => false,
            'is_sold' => false,
            'sold_date' => null,
            'price_usd' => fake()->randomFloat(2, 150000, 3000000),
            'price_mxn' => fake()->randomFloat(2, 3000000, 60000000),
            'show_both_prices' => fake()->boolean(),
            'description' => fake()->paragraph(),
            'description_es' => null,
            'lot_meters' => fake()->randomFloat(2, 200, 2000),
            'construction_meters' => fake()->randomFloat(2, 100, 1000),
            'bedrooms' => fake()->numberBetween(1, 8),
            'bathrooms' => fake()->numberBetween(1, 6),
            'half_bathrooms' => fake()->numberBetween(0, 3),
            'parking_spaces' => fake()->numberBetween(0, 4),
            'notes' => null,
            'property_order' => fake()->numberBetween(0, 100),
        ];
    }
}
