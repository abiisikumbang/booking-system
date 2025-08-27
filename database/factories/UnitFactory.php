<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Property;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Unit>
 */
class UnitFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'property_id' => Property::factory(),
            'code' => $this->faker->unique()->regexify('[A-Z0-9]{5}'),
            'name' => "Room " . $this->faker->numberBetween(100, 999),
            'description' => $this->faker->optional()->paragraph(),
            'capacity' => $this->faker->numberBetween(1, 4),
            'price_amount' => $this->faker->numberBetween(200000, 1000000),
            'price_currency' => 'IDR',
            'price_unit' => $this->faker->randomElement(['NIGHT','HOUR','MONTH']),
            'amenities' => json_encode($this->faker->words(5)),
            'status' => $this->faker->randomElement(['ACTIVE','INACTIVE','MAINTENANCE']),
        ];
    }
}
