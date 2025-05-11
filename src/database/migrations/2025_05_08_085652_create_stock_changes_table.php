<?php

use App\Models\Order;
use App\Models\Product;
use App\Models\Stock;
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
        Schema::create('stock_changes', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Stock::class)->constrained()->cascadeOnDelete();
            $table->unsignedInteger('quantity');
            $table->string('operation', 255)->index();
            $table->timestamps();

            $table->index(['stock_id']);
            $table->index(['created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_changes');
    }
};
