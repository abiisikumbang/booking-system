<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Property>
 */
class PropertyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'owner_id' => User::factory(),
            'name' => $this->faker->company . " Hotel",
            'address' => $this->faker->address,
            'city' => $this->faker->city,
            'description' => $this->faker->paragraph,
        ];
    }
}
