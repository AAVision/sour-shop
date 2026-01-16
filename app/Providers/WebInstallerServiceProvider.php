<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class WebInstallerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Only load the web installer if enabled
        if ($this->isInstallerEnabled()) {
            $this->app->register(\Abedin\WebInstaller\Providers\AppServiceProvider::class);
        }
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Check if installer is enabled
     */
    private function isInstallerEnabled(): bool
    {
        return (bool) config('installer.enabled', false);
    }
}
