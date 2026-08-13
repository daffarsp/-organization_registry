<?php

namespace Database\Factories;

use App\Enums\RegistrationStatus;
use App\Models\Division;
use App\Models\Registration;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Registration>
 */
class RegistrationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'gender' => fake()->randomElement(['male', 'female', 'other']),
            'birth_date' => fake()->dateTimeBetween('-25 years', '-15 years')->format('Y-m-d'),
            'school' => fake()->company(),
            'address' => fake()->address(),
            'division_id' => Division::factory(),
            'reason' => fake()->paragraph(),
            'organization_experience' => fake()->optional()->paragraph(),
            'instagram' => fake()->optional()->userName(),
            'status' => RegistrationStatus::Pending,
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
