<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Observers\GlobalModelObserver;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //tambahkan model yang ingin di log
        \App\Models\User::observe(GlobalModelObserver::class);
        \Spatie\Permission\Models\Role::observe(GlobalModelObserver::class);
        // \App\Models\User::observe(GlobalModelObserver::class);
    }
}
