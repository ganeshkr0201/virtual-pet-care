<?php

namespace Database\Factories;

use App\Models\Pet;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AppointmentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id'              => User::factory(),
            'pet_id'               => Pet::factory(),
            'title'                => $this->faker->randomElement(['Annual Checkup', 'Vaccination', 'Dental Cleaning', 'Grooming Session']),
            'type'                 => $this->faker->randomElement(['checkup', 'vaccination', 'grooming', 'dental']),
            'appointment_datetime' => $this->faker->dateTimeBetween('now', '+3 months'),
            'vet_name'             => 'Dr. ' . $this->faker->lastName(),
            'clinic_name'          => $this->faker->company() . ' Vet Clinic',
            'status'               => 'scheduled',
        ];
    }
}
