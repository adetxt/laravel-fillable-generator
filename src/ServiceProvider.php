<?php

namespace Adetxt\LaravelFillableGenerator;

use Adetxt\LaravelFillableGenerator\Console\Commands\GenerateModelFillable;
use Illuminate\Support\ServiceProvider as LaravelServiceProvider;

class ServiceProvider extends LaravelServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                GenerateModelFillable::class,
            ]);
        }
    }
}
