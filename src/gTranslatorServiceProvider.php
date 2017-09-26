<?php

namespace Chaibi\gTranslator;

use Illuminate\Support\ServiceProvider;

class gTranslatorServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/config/gTranslator.php' => config_path('gTranslator.php'),
        ]);

        if ($this->app->runningInConsole()) {
            $this->commands([
                gTranslatorCommand::class
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
