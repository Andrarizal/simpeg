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
        $number = str_pad(fake()->numberBetween(0, 999999999999), 12, '0', STR_PAD_LEFT);

        $formatted = substr_replace($number, '.', 4, 0);
        $formatted = substr_replace($formatted, '.', 9, 0);
        $formatted = substr_replace($formatted, '.', 13, 0);

        $nip = $formatted . PHP_EOL;
        return [
            'nik' => fake()->numerify('################'),
            'nip' => $nip,
            'name' => fake()->name(),
            'birth_place' => fake()->city(),
            'birth_date' => fake()->date('Y-m-d'),
            'sex' => fake()->randomElement(['L', 'P']),
            'marital' => fake()->randomElement(['Lajang', 'Menikah', 'Cerai Hidup', 'Cerai Mati']),
            'address' => fake()->paragraph(2, false),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->numerify('08##########'),
            'other_phone' => fake()->numerify('08##########'),
            'other_phone_adverb' => fake()->randomElement(['Suami', 'Istri', 'Orang tua', 'Wali', 'Saudara', 'Lainnya']),
            'entry_date' => fake()->date('Y-m-d'),
            'retirement_date' => fake()->date('Y-m-d'),
            'staff_status_id' => fake()->numberBetween(1, 6),
            'chair_id' => fake()->numberBetween(1, 96),
            'group_id' => fake()->numberBetween(1, 13),
            'unit_id' => fake()->numberBetween(1, 32),
        ];
    }
}
