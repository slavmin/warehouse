<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\StockOperations;
use App\Models\Product;
use App\Models\Stock;
use Illuminate\Support\Facades\DB;

/**
 * StockService
 */
class StockService
{
    /**
     * @return array{
     *     success: bool,
     *     errors: array,
     * }
     */
    public function checkStockAvailability(int $warehouseId, array $items): array
    {
        $errors = [];

        foreach ($items as $item) {
            $product = Product::query()->find($item['product_id']);
            $stock = static::getProductStock($product->id, $warehouseId);

            if ($stock < $item['count']) {
                $errors[] = [
                    'warehouse_id' => $warehouseId,
                    'product_id' => $item['product_id'],
                    'product_name' => $product->name,
                    'available' => $stock,
                    'requested' => $item['count'],
                ];
            }
        }

        return [
            'success' => $errors === [],
            'errors' => $errors,
        ];
    }

    public function decrementStocks(int $warehouseId, array $items): void
    {
        DB::transaction(function () use ($warehouseId, $items): void {
            foreach ($items as $item) {
                $stock = Stock::query()
                    ->where('product_id', $item['product_id'])
                    ->where('warehouse_id', $warehouseId)
                    ->firstOrFail();

                $stock->decrement('stock', $item['count']);

                $stock->changes()->create([
                    'quantity' => $item['count'],
                    'operation' => StockOperations::DECREMENT,
                ]);
            }
        });
    }

    public function incrementStocks(int $warehouseId, array $items): void
    {
        DB::transaction(function () use ($warehouseId, $items): void {
            foreach ($items as $item) {
                $stock = Stock::query()
                    ->where('product_id', $item['product_id'])
                    ->where('warehouse_id', $warehouseId)
                    ->firstOrFail();

                $stock->increment('stock', $item['count']);

                $stock->changes()->create([
                    'quantity' => $item['count'],
                    'operation' => StockOperations::INCREMENT,
                ]);
            }
        });
    }

    protected static function getProductStock(int $productId, int $warehouseId): int
    {
        $stock = Stock::query()
            ->where('product_id', $productId)
            ->where('warehouse_id', $warehouseId)
            ->first();

        return $stock ? $stock->stock : 0;
    }
}
