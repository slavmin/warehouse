<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'customer' => $this->faker->name(),
            'warehouse_id' => Warehouse::factory(),
            'status' => $this->faker->randomElement(['active', 'completed', 'canceled']),
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'completed_at' => function (array $attributes) {
                return $attributes['status'] === 'completed'
                    ? $this->faker->dateTimeBetween($attributes['created_at'], 'now')
                    : null;
            },
        ];
    }
}
