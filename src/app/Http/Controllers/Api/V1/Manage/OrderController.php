<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Manage;

use App\Http\Controllers\Controller;
use App\Http\Requests\Manage\Order\OrderIndexRequest;
use App\Http\Requests\Manage\Order\OrderStoreRequest;
use App\Http\Requests\Manage\Order\OrderUpdateRequest;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Throwable;

class OrderController extends Controller
{
    public function __construct(
        protected OrderService $orderService
    ) {}

    public function index(OrderIndexRequest $request): JsonResponse
    {
        $query = Order::with(['warehouse', 'items.product']);

        if ($request->input('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->input('warehouse_id')) {
            $query->where('warehouse_id', $request->input('warehouse_id'));
        }

        if ($request->input('customer')) {
            $query->where('customer', 'like', '%'.$request->input('customer').'%');
        }

        $query->orderBy('id', 'desc');

        $perPage = static::getPerPage($request);

        $rows = $query->paginate($perPage);

        return response()->json($rows);
    }

    public function store(OrderStoreRequest $request): JsonResponse
    {
        try {
            $order = $this->orderService->createOrder($request->validated());

            return response()->json($order, 201);
        } catch (Throwable $e) {
            return response()->json(['error' => json_decode($e->getMessage())], 400);
        }
    }

    public function update(OrderUpdateRequest $request, Order $order): JsonResponse
    {
        try {
            $order = $this->orderService->updateOrder($order, $request->validated());

            return response()->json($order);
        } catch (Throwable $e) {
            return response()->json(['error' => json_decode($e->getMessage())], 400);
        }
    }

    public function show(Order $order): void
    {
        //
    }

    public function destroy(Order $order): void
    {
        //
    }

    public function complete(Order $order): JsonResponse
    {
        try {
            $order = $this->orderService->completeOrder($order);

            return response()->json(['message' => 'Заказ успешно завершен']);
        } catch (Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function cancel(Order $order): JsonResponse
    {
        try {
            $order = $this->orderService->cancelOrder($order);

            return response()->json(['message' => 'Заказ успешно отменен']);
        } catch (Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function activate(Order $order): JsonResponse
    {
        try {
            $order = $this->orderService->reactivateOrder($order);

            return response()->json(['message' => 'Заказ успешно возобновлен']);
        } catch (Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
