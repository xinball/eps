<?php
/*
 * @Author: XinBall
 * @LastEditors: XinBall
 */

namespace App\Providers;


use App\View\Components\Modal;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Redis;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::useBootstrapThree();
        // Paginator::defaultView("vendor.pagination.pink");
        // Paginator::defaultSimpleView("vendor.pagination.pink");
        //
    }
}
