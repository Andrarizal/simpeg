<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\StaffContract>
 */
class StaffContractFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $romanLevels = ['I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X'];

        $contract_number = fake()->numberBetween(1, 999) . '/' .
            fake()->numberBetween(1, 12) . '/KK/YMP-U/' .
            fake()->randomElement($romanLevels) . '/' .
            fake()->year();

        return [
            'contract_number' => $contract_number,
            'start_date' => fake()->date('Y-m-d'),
            'end_date' => fake()->date('Y-m-d'),
        ];
    }
}
