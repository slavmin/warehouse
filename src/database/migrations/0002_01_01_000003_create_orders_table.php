<?php

use App\Enums\OrderStatuses;
use App\Models\Warehouse;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('customer', 255);
            $table->foreignIdFor(Warehouse::class)->constrained();
            $table->string('status', 255)->default(OrderStatuses::STATUS_ACTIVE->value);
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index('warehouse_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
