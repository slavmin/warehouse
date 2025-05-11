<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Manage;

use App\Http\Controllers\Controller;
use App\Http\Requests\Manage\Stock\StockChangeIndexRequest;
use App\Models\StockChange;
use Illuminate\Http\JsonResponse;

class StockChangeController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(StockChangeIndexRequest $request): JsonResponse
    {
        $query = StockChange::with([
            'stock',
            'product',
            'warehouse',
        ]);

        if ($request->input('warehouse_id')) {
            $query->whereHas('warehouse', fn ($q) => $q->where('warehouses.id', $request->input('warehouse_id')));
        }

        if ($request->input('product_id')) {
            $query->whereHas('product', fn ($q) => $q->where('products.id', $request->input('product_id')));
        }

        if ($request->input('date_from')) {
            $query->where('created_at', '>=', $request->input('date_from'));
        }

        if ($request->input('date_to')) {
            $query->where('created_at', '<=', $request->input('date_to'));
        }

        if ($request->input('operation')) {
            $query->where('operation', $request->input('operation'));
        }

        $query->orderBy('created_at', 'desc');

        $perPage = static::getPerPage($request);

        $rows = $query->paginate($perPage);

        return response()->json($rows);
    }
}
