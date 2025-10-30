<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\StaffAppointment>
 */
class StaffAppointmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $romanLevels = ['I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X'];

        $decree_number = fake()->numberBetween(1, 999) . '/' .
            fake()->numberBetween(1, 12) . '/SK/YMP/' .
            fake()->randomElement($romanLevels) . '/' .
            fake()->year();

        // Daftar angka golongan
        $classLevels = ['I', 'II', 'III', 'IV', 'V'];

        // Daftar huruf sub-golongan
        $letters = ['a', 'b', 'c', 'd', 'e'];

        // Gabungkan keduanya secara acak
        $class = $this->faker->randomElement($classLevels)
                   . $this->faker->randomElement($letters);

        return [
            'decree_number' => $decree_number,
            'decree_date' => fake()->date('Y-m-d'),
            'class' => $class,
        ];
    }
}
