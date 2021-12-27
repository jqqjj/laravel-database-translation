<?php

namespace Jqqjj\LaravelDatabaseTranslation;

use Illuminate\Contracts\Translation\Loader as LoaderInterface;
use Illuminate\Support\Facades\App;

class Loader implements LoaderInterface
{
    public function load($locale, $group, $namespace = null)
    {
        $translations = App::get('laravel_database_translation')->getTranslations($locale, $group, $namespace);
        return array_filter($translations, function ($item){
            return is_string($item) && strlen($item);
        });
    }

    public function addNamespace($namespace, $hint)
    {

    }

    public function addJsonPath($path)
    {

    }

    public function namespaces()
    {
        return App::get('laravel_database_translation')->getNamespaces();
    }
}
