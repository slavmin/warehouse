<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;

abstract class Controller
{
    final public static function getPerPage(Request $request): int
    {
        return $request->filled('per_page')
            ? (int) min(max($request->integer('per_page'), config('app.pagination.per_page_min')), config('app.pagination.per_page_max'))
            : (int) config('app.pagination.per_page');
    }
}
