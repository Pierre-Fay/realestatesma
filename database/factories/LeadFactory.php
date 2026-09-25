<?php

namespace Database\Factories;

use App\Enums\LeadStatus;
use App\Models\Agent;
use App\Models\Lead;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Lead>
 */
class LeadFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->optional()->phoneNumber(),
            'interested_in' => fake()->words(3, true),
            'budget' => fake()->optional()->randomFloat(2, 100000, 5000000),
            'status' => LeadStatus::NEW,
            'notes' => fake()->optional()->paragraph(),
            'agent_id' => Agent::factory(),
        ];
    }

    /**
     * Indicate the lead has been contacted.
     */
    public function contacted(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => LeadStatus::CONTACTED,
        ]);
    }

    /**
     * Indicate the lead is qualified.
     */
    public function qualified(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => LeadStatus::QUALIFIED,
        ]);
    }

    /**
     * Indicate the lead is closed (deal won).
     */
    public function closed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => LeadStatus::CLOSED,
        ]);
    }

    /**
     * Indicate the lead is lost.
     */
    public function lost(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => LeadStatus::LOST,
        ]);
    }
}
