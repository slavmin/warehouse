<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Stock;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\StockChange>
 */
class StockChangeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'stock_id' => Stock::factory(),
            'quantity' => $this->faker->numberBetween(1, 3),
            'operation' => $this->faker->randomElement(['increment', 'decrement']),
        ];
    }
}
