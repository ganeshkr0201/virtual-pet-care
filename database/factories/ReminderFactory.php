<?php

namespace Database\Factories;

use App\Models\Pet;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReminderFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id'        => User::factory(),
            'pet_id'         => Pet::factory(),
            'title'          => $this->faker->randomElement(['Morning Feeding', 'Evening Walk', 'Medication', 'Grooming', 'Water Refill']),
            'type'           => $this->faker->randomElement(['feeding', 'walking', 'medication', 'grooming', 'water']),
            'reminder_time'  => $this->faker->time('H:i'),
            'start_date'     => now()->subDays(rand(0, 30)),
            'repeat'         => $this->faker->randomElement(['daily', 'weekly', 'none']),
            'is_active'      => true,
            'email_notify'   => true,
            'push_notify'    => true,
            'snooze_minutes' => 10,
        ];
    }
}
