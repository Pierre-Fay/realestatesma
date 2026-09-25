<?php

namespace Database\Factories;

use App\Enums\CategoryGroupType;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'slug' => fake()->unique()->slug(),
            'category_order' => fake()->numberBetween(0, 100),
            'group_type' => fake()->randomElement(CategoryGroupType::cases()),
        ];
    }
}
