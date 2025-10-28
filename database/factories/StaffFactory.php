<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Staff>
 */
class StaffFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nik' => fake()->numerify('################'),
            'name' => fake()->name(),
            'birth_place' => fake()->city(),
            'birth_date' => fake()->date('Y-m-d'),
            'sex' => fake()->randomElement(['L', 'P']),
            'address' => fake()->paragraph(2, false),
            'phone' => fake()->numerify('08##########'),
            'personal_email' => fake()->unique()->safeEmail(),
            'office_email' => fake()->unique()->safeEmail(),
            'last_education' => fake()->randomElement(['SMA', 'D3', 'D4/S1', 'S2', 'S3']),
            'work_entry_date' => fake()->date('Y-m-d'),
            'unit_id' => fake()->numberBetween(1, 32),
            'chair_id' => fake()->numberBetween(1, 51),
        ];
    }
}
