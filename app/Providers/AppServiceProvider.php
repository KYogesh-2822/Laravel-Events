<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
namespace App\Services;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
       $this->app()->bind('product',function(){
        return new ProductService();
       });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
