<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\StaffWorkEducation>
 */
class StaffWorkEducationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $certificate_number = 'DN-' .
            fake()->numberBetween(1, 99) . '/BAN-PT/' .
            fake()->year() . '/' .
            fake()->numberBetween(10000, 99999);

        return [
            'level' => fake()->randomElement(['Dokter', 'Dokter Gigi','Spesialis', 'S2', 'S1', 'Profesi Ners', 'Profesi Apoteker', 'DIV', 'DIII', 'DIII Anestesi', 'DIV Anestesi', 'SMK', 'SMA', 'SMP'
            ]),
            'major' => fake()->sentence(),
            'institution' => fake()->company(),
            'certificate_number' => $certificate_number,
            'certificate_date' => fake()->date('Y-m-d'),
        ];
    }
}
