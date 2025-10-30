<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\StaffWorkExperience>
 */
class StaffWorkExperienceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'institution' => fake()->company(),
            'work_length' => fake()->numberBetween(2,9) . 'years',
            'admission' => fake()->sentence(),
        ];
    }
}
