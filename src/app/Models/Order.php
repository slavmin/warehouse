<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Order model
 *
 * @property int $id
 * @property string $customer
 * @property string $status
 * @property int $warehouse_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon|null $completed_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read mixed $items
 */
class Order extends Model
{
    /** @use HasFactory<\Database\Factories\OrderFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'customer',
        'status',
        'warehouse_id',
        'completed_at',
    ];

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
