<?php

namespace Database\Factories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Generate a random month between 1 and 12
        $randomMonth = $this->faker->numberBetween(1, 12);

        // Get the current year
        $currentYear = Carbon::now()->year;

        // Generate a date with the random month and the current year
        $createdDate = Carbon::createFromDate($currentYear, $randomMonth, 1)->subDays(rand(0, 30));

        return [
            'user_name' => $this->faker->unique()->userName,
            'email' => $this->faker->unique()->safeEmail,
            'password' => bcrypt('password'), // Default password for now
            'character' => $this->faker->numberBetween(1, 5),
            'level' => $this->faker->numberBetween(1, 3),
            'created_at' => $createdDate,
            'updated_at' => now(),
        ];
    }
}
