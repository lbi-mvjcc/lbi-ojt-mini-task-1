<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
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
        'password' => Hash::make('password'), // default password
        'role' => 'customer', // default role
        'remember_token' => Str::random(10),
    ];
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

    public function customer()
    {
        return $this->state(fn (array $attributes) => [
        'role' => 'customer',
    ]);
    }

    public function frontend()
    {
    return $this->state(fn (array $attributes) => [
        'role' => 'frontend_dev',
    ]);
    }

    public function backend()
    {
    return $this->state(fn (array $attributes) => [
        'role' => 'backend_dev',
    ]);
    }

    public function server()
    {
    return $this->state(fn (array $attributes) => [
        'role' => 'server_admin',
    ]);
    }

}
