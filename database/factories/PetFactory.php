<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PetFactory extends Factory
{
    public function definition(): array
    {
        $species = $this->faker->randomElement(['dog', 'cat', 'bird', 'rabbit', 'hamster']);
        $breeds = [
            'dog'     => ['Golden Retriever', 'Labrador', 'Poodle', 'Bulldog', 'Beagle'],
            'cat'     => ['Persian', 'Siamese', 'Maine Coon', 'Ragdoll', 'Bengal'],
            'bird'    => ['Parrot', 'Canary', 'Cockatiel', 'Budgerigar'],
            'rabbit'  => ['Holland Lop', 'Mini Rex', 'Lionhead'],
            'hamster' => ['Syrian', 'Dwarf', 'Roborovski'],
        ];

        return [
            'user_id'        => User::factory(),
            'name'           => $this->faker->firstName(),
            'species'        => $species,
            'breed'          => $this->faker->randomElement($breeds[$species]),
            'gender'         => $this->faker->randomElement(['male', 'female']),
            'date_of_birth'  => $this->faker->dateTimeBetween('-10 years', '-3 months'),
            'weight'         => $this->faker->randomFloat(1, 0.5, 40),
            'color'          => $this->faker->colorName(),
            'activity_level' => $this->faker->randomElement(['low', 'moderate', 'high']),
            'is_active'      => true,
        ];
    }
}
