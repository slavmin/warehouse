<?php

declare(strict_types=1);

namespace App\Providers;

use App\Services\OrderService;
use App\Services\StockService;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(OrderService::class, fn ($app): OrderService => new OrderService($app->get(StockService::class)));
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Date::use(CarbonImmutable::class);

        Model::shouldBeStrict();

        DB::prohibitDestructiveCommands(
            $this->app->isProduction(),
        );
    }
}
