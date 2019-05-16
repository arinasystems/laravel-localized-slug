<?php

namespace ArinaSystems\LocalizedSlug\Providers;

use Illuminate\Support\ServiceProvider;
use ArinaSystems\LocalizedSlug\Services\Slugger;

class SluggerServiceProvider extends ServiceProvider
{
    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->singleton('slugger', function ($app) {
            return new Slugger();
        });
    }
}
