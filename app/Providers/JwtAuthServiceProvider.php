<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class JwtAuthServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
        require_once app_path().'\Helpers\JwtAuth.php'; //PERMITE CARGAR EL JWT COMO SERVICE PROVIDER
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
