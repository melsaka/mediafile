<?php

namespace Melsaka\MediaFile;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class MediaFileServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/config/mediafile.php', 'mediafile'
        );
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        if (str_contains(url()->current(), 'mediafiles')) {
            Paginator::defaultView('MediaFile::layouts.pagination');
        }

        $this->loadViewsFrom(__DIR__.'/views', 'MediaFile');

        $this->loadMigrationsFrom(__DIR__.'/database/migrations');

        $this->publishes([
            __DIR__.'/config/mediafile.php' => config_path('mediafile.php')
        ], 'mediafile-config');

        $this->publishes([
            __DIR__.'/database/migrations/' => database_path('migrations')
        ], 'mediafile-migrations');

        $this->publishes([
            __DIR__.'/views' => resource_path('views/vendor/mediafile'),
        ], 'mediafile-views');

        $this->publishes([
            __DIR__.'/public' => public_path('vendor/mediafile'),
        ], 'mediafile-assets');

        $this->publishes([
            __DIR__.'/Controllers/stub' => app_path('Http/Controllers/MediaFile'),
        ], 'mediafile-controllers');
    }
}
