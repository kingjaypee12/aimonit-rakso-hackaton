<?php

namespace App\Providers;

use App\Filesystem\CustomApiAdapter;
use Illuminate\Support\Facades\Storage;
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
        
        Storage::extend('peac_files', function ($app, $config) {
            $adapter = new CustomApiAdapter($config);
            $filesystem = new \League\Flysystem\Filesystem($adapter, $config);

            return new \Illuminate\Filesystem\FilesystemAdapter($filesystem, $adapter, $config);
        });
    }
}
