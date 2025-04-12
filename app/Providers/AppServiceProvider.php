<?php

namespace App\Providers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
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
        Request::macro('logExceptionError', function (\Exception $exception, $message){
            Log::error($message . ' Exception: ' . $exception->getMessage(), $exception->getTrace());
        });

        Paginator::useBootstrap();

        if($this->app->environment('production')) {
            \URL::forceScheme('https');
        }
    }
}
