<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\OrderStatuses;
use App\Models\Order;
use Exception;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function __construct(
        protected StockService $stockService
    ) {}

    /**
     * Создать новый заказ
     *
     * @throws Exception
     */
    public function createOrder(array $data): Order
    {
        $warehouseId = data_get($data, 'warehouse_id');
        $dataItems = data_get($data, 'items');
        $dataCustomer = data_get($data, 'customer');

        $stockCheck = $this->stockService->checkStockAvailability(
            $warehouseId,
            $dataItems
        );

        if (! $stockCheck['success']) {
            $messageArr = [
                'message' => 'Недостаточно товара на складе',
                'errors' => $stockCheck['errors'],
            ];
            throw new Exception(json_encode($messageArr), 400);
        }

        return DB::transaction(function () use ($warehouseId, $dataItems, $dataCustomer) {
            $order = Order::query()->create([
                'customer' => $dataCustomer,
                'warehouse_id' => $warehouseId,
                'status' => OrderStatuses::STATUS_ACTIVE->value,
            ]);

            $order->items()->createMany($dataItems);

            // Списываем товары со склада
            $this->stockService->decrementStocks($warehouseId, $dataItems);

            return $order->load(['warehouse', 'items.product']);
        });
    }

    /**
     * Обновить заказ
     *
     * @throws Exception
     */
    public function updateOrder(Order $order, array $data): Order
    {
        if ($order->status !== OrderStatuses::STATUS_ACTIVE->value) {
            $messageArr = ['message' => 'Невозможно обновить заказ'];
            throw new Exception(json_encode($messageArr), 400);
        }

        return DB::transaction(function () use ($order, $data) {
            $dataWarehouseId = data_get($data, 'warehouse_id');
            $dataCustomer = data_get($data, 'customer');
            $dataItems = data_get($data, 'items');
            $orderWarehouseId = $order->warehouse_id;
            $orderItems = static::getItemsFromOrder($order);
            $warehouseChanged = $dataWarehouseId && $dataWarehouseId !== $orderWarehouseId;

            if ($dataCustomer) {
                $order->customer = $dataCustomer;
            }

            if ($warehouseChanged) {
                $dataItems = empty($dataItems) ? $orderItems : $dataItems;
            }

            if (! empty($dataItems)) {
                // Возвращаем товары на склад
                $this->stockService->incrementStocks(
                    $orderWarehouseId,
                    $orderItems
                );

                // Удаляем элементы заказа
                $order->items()->delete();

                // Меняем warehouse_id
                if ($warehouseChanged) {
                    $order->warehouse_id = $dataWarehouseId;
                    $orderWarehouseId = $dataWarehouseId;
                }

                // Проверяем наличие товаров на складе
                $stockCheck = $this->stockService->checkStockAvailability(
                    $orderWarehouseId,
                    $dataItems
                );

                if (! $stockCheck['success']) {
                    $messageArr = [
                        'message' => 'Недостаточно товара на складе',
                        'errors' => $stockCheck['errors'],
                    ];
                    throw new Exception(json_encode($messageArr), 400);
                }

                // Добавляем новые элементы заказа
                $order->items()->createMany($dataItems);

                // Списываем товары со склада
                $this->stockService->decrementStocks($orderWarehouseId, $dataItems);
            }

            if ($dataCustomer || $warehouseChanged) {
                $order->save();
            }

            return $order->load(['warehouse', 'items.product']);
        });
    }

    /**
     * Завершить заказ
     *
     * @throws Exception
     */
    public function completeOrder(Order $order): Order
    {
        if (! $this->canCompleteOrder($order)) {
            throw new Exception('Невозможно завершить заказ', 400);
        }

        $order->update([
            'status' => OrderStatuses::STATUS_COMPLETED->value,
            'completed_at' => now(),
        ]);

        return $order;
    }

    /**
     * Отменить заказ
     *
     * @throws Exception
     */
    public function cancelOrder(Order $order): Order
    {
        if ($order->status !== OrderStatuses::STATUS_ACTIVE->value) {
            throw new Exception('Невозможно отменить заказ', 400);
        }

        return DB::transaction(function () use ($order): Order {
            $order->update([
                'status' => OrderStatuses::STATUS_CANCELED->value,
                'completed_at' => now(),
            ]);

            // Возвращаем товары на склад
            $this->stockService->incrementStocks(
                $order->warehouse_id,
                static::getItemsFromOrder($order)
            );

            return $order;
        });
    }

    /**
     * Возобновить заказ
     *
     * @throws Exception
     */
    public function reactivateOrder(Order $order): Order
    {
        if (! $this->canReactivateOrder($order)) {
            throw new Exception('Невозможно возобновить заказ', 400);
        }

        return DB::transaction(function () use ($order): Order {
            // Списываем товары со склада
            $this->stockService->decrementStocks(
                $order->warehouse_id,
                static::getItemsFromOrder($order)
            );

            $order->update([
                'status' => OrderStatuses::STATUS_ACTIVE->value,
                'completed_at' => null,
            ]);

            return $order;
        });
    }

    /**
     * Возможность завершить заказ
     */
    public function canCompleteOrder(Order $order): bool
    {
        return $order->status === OrderStatuses::STATUS_ACTIVE->value;
    }

    /**
     * Возможность возобновить заказ
     */
    public function canReactivateOrder(Order $order): bool
    {
        if ($order->status !== OrderStatuses::STATUS_CANCELED->value) {
            return false;
        }

        $check = $this->stockService->checkStockAvailability(
            $order->warehouse_id,
            static::getItemsFromOrder($order)
        );

        return $check['success'];
    }

    protected static function getItemsFromOrder(Order $order): array
    {
        return $order->items->map(fn ($item): array => [
            'product_id' => $item->product_id,
            'count' => $item->count,
        ])->toArray();
    }
}
