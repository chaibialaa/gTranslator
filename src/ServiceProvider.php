<?php

namespace Chaibi\gTranslator;

use Illuminate\Support\ServiceProvider as IlluminateServiceProvider;;

class ServiceProvider extends IlluminateServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/gTranslator.php' => config_path('gTranslator.php'),
        ]);

        if ($this->app->runningInConsole()) {
            $this->commands([
                Command::class
            ]);
        }
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
