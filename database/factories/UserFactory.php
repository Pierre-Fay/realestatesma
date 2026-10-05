<?php

namespace Database\Factories;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'photo' => null,
            'is_enabled' => true,
        ];
    }

    /**
     * Set the role directly: it is not mass-assignable, so the factory assigns it
     * on the model instance rather than through the fillable attributes.
     */
    public function configure(): static
    {
        return $this->afterMaking(function (User $user): void {
            $user->role ??= UserRole::AGENT;
        });
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Indicate that the user has the admin role.
     */
    public function admin(): static
    {
        return $this->afterMaking(function (User $user): void {
            $user->role = UserRole::ADMIN;
        });
    }

    /**
     * Indicate that the user has the agent role.
     */
    public function agent(): static
    {
        return $this->afterMaking(function (User $user): void {
            $user->role = UserRole::AGENT;
        });
    }

    /**
     * Indicate that the user's login access is disabled.
     */
    public function disabled(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_enabled' => false,
        ]);
    }
}
