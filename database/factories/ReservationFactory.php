<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Unit;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Reservation>
 */
class ReservationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
       $start = $this->faker->dateTimeBetween('now', '+1 month');
        $end = (clone $start)->modify('+'.rand(1,5).' days');

        return [
            'code' => $this->faker->unique()->regexify('[A-Z0-9]{5}'),
            'user_id' => User::factory(),
            'unit_id' => Unit::factory(),
            'start_at' => $start,
            'end_at' => $end,
            'quantity' => rand(1, 5),
            'status' => $this->faker->randomElement(['PENDING','CONFIRMED','CANCELLED','EXPIRED','COMPLETED']),
            'total_amount' => $this->faker->numberBetween(100000, 10000000),
            'payment_status' => $this->faker->randomElement(['UNPAID','PAID','REFUNDED']),
            'notes' => $this->faker->optional()->paragraph(),
            'checkin_at' => $this->faker->optional()->dateTimeBetween($start, $end),
            'checkout_at' => $this->faker->optional()->dateTimeBetween($start, $end),
        ];
    }
}
