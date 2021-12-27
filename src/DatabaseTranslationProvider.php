<?php

namespace Jqqjj\LaravelDatabaseTranslation;

use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Illuminate\Translation\Translator;

class DatabaseTranslationProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__ . '/migrations');
    }

    public function register()
    {
        $this->app->singleton('translation.loader', function (/*Application $app*/) {
            return new Loader(86400 * 7);
        });
        $this->app->singleton('translator', function (Application $app) {
            $loader = $app['translation.loader'];
            $locale = $app['config']['app.locale'];
            $trans = new Translator($loader, $locale);
            $trans->setFallback($app['config']['app.fallback_locale']);
            return $trans;
        });
        $this->app->singleton('database_laravel_translation', function () {
            return new Translation();
        });
    }
}
