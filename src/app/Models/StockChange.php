<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class StockChange extends Model
{
    /** @use HasFactory<\Database\Factories\StockChangeFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'stock_id',
        'quantity',
        'operation',
    ];

    public function stock(): BelongsTo
    {
        return $this->belongsTo(Stock::class);
    }

    public function product(): HasOneThrough
    {
        return $this->hasOneThrough(
            Product::class,
            Stock::class,
            'id',
            'id',
            'stock_id',
            'product_id',
        );
    }

    public function warehouse(): HasOneThrough
    {
        return $this->hasOneThrough(
            Warehouse::class,
            Stock::class,
            'id',
            'id',
            'stock_id',
            'warehouse_id',
        );
    }
}
