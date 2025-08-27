<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payment>
 */
class PaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'reservation_id' => \App\Models\Reservation::factory(),
            'provider' => $this->faker->randomElement(['MIDTRANS']),
            'method' => $this->faker->optional()->word(),
            'amount' => $this->faker->randomNumber(8),
            'status' => $this->faker->randomElement(['PENDING','PAID','FAILED','REFUNDED']),
            'external_id' => $this->faker->unique()->uuid(),
            'reference' => $this->faker->optional()->word(),
            'paid_at' => $this->faker->optional()->dateTime(),
            'refund_amount' => $this->faker->optional()->randomNumber(8),
            'meta' => json_encode([
                'ip_address' => $this->faker->ipv4,
                'user_agent' => $this->faker->userAgent,
            ]),
        ];
    }
}
