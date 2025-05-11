<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Manage;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request): JsonResponse
    {
        $products = Product::with(['stocks.warehouse'])->get();

        $result = $products->map(fn ($product): array => [
            'id' => $product->id,
            'name' => $product->name,
            'price' => $product->price,
            'stocks' => $product->stocks->map(fn ($stock): array => [
                'warehouse_id' => $stock->warehouse_id,
                'warehouse_name' => $stock->warehouse->name,
                'stock' => $stock->stock,
            ]),
        ]);

        return response()->json($result);
    }
}
