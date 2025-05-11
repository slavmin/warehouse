<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Stock;
use App\Models\Warehouse;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $warehouses = Warehouse::all();

        Order::factory()
            ->count(50)
            ->state(function (array $attributes) use ($warehouses) {
                return [
                    'warehouse_id' => $warehouses->random()->id,
                ];
            })
            ->afterCreating(function (Order $order) {
                $stockProducts = Stock::query()->where('warehouse_id', $order->warehouse_id)
                    ->inRandomOrder()
                    ->limit(3)
                    ->get()
                    ->unique('product_id')
                    ->values();

                foreach ($stockProducts as $stockProduct) {
                    $order->items()->create([
                        'product_id' => $stockProduct->product_id,
                        'count' => rand(1, min(5, max(1, $stockProduct->count))),
                    ]);
                }
            })
            ->create();
    }
}
