<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

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
        // Register global helper for PHP currency formatting
        if (!function_exists('format_currency')) {
            function format_currency($amount) {
                return '₱' . number_format((float)$amount, 2, '.', ',');
            }
        }
    }
}
