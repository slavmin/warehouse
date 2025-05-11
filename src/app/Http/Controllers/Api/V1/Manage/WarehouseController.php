<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Manage;

use App\Http\Controllers\Controller;
use App\Models\Warehouse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request): JsonResponse
    {
        $warehouses = Warehouse::all();

        return response()->json($warehouses);
    }
}
