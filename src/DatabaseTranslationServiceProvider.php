<?php

namespace Jqqjj\LaravelDatabaseTranslation;

use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class DatabaseTranslationServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__ . '/migrations');
    }

    public function register()
    {
        $this->app->singleton('translation.loader', function (/*Application $app*/) {
            return new Loader();
        });
        $this->app->singleton('translator', function (Application $app) {
            $loader = $app['translation.loader'];
            $locale = $app['config']['app.locale'];
            $trans = new Translator($loader, $locale);
            $trans->setFallback($app['config']['app.fallback_locale']);
            return $trans;
        });
        $this->app->singleton('laravel_database_translation', function () {
            return new Translation(86400 * 7);
        });
    }
}
