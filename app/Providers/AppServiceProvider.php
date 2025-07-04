<?php

namespace App\Providers;

use App\Facades\ApiResponse;
use App\Facades\FacadesLogic\ApiResponseLogic;
use App\Facades\FacadesLogic\FileHandlerLogic;
use App\Facades\FileHandler;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            ApiResponse::class,
            ApiResponseLogic::class
        );
        $this->app->bind(
            FileHandler::class,
            FileHandlerLogic::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
    }
}
