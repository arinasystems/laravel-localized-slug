<?php

namespace ArinaSystems\LocalizedSlug\Providers;

use Illuminate\Support\ServiceProvider;

class LocalizedSlugServiceProvider extends ServiceProvider
{
    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerConfig();
    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->publishes([
            __DIR__ . '/../../config/config.php' => config_path('localized-slug.php'),
        ], 'config');
        $this->mergeConfigFrom(
            __DIR__ . '/../../config/config.php', 'localized-slug'
        );
    }
}
