<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

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
        // Custom component
        Blade::componentNamespace('App\\View\\Components\\Backend', 'backend');
        Blade::componentNamespace('App\\View\\Components\\Frontend', 'frontend');

        // Custom view
        View::addNamespace('category', resource_path('views/backend/category'));
    }
}
