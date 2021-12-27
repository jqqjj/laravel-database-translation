<?php

namespace Jqqjj\LaravelDatabaseTranslation;

use Illuminate\Contracts\Translation\Loader as LoaderInterface;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;

class Loader implements LoaderInterface
{
    protected int $lifetimeSecond;

    public function __construct($lifetimeSecond)
    {
        $this->lifetimeSecond = $lifetimeSecond;
    }

    public function load($locale, $group, $namespace = null)
    {
        $cacheKey = 'database_laravel_translation';
        $cacheKey .= is_string($group) && strlen($group) ? ("_$group") : '';
        $cacheKey .= is_string($namespace) && strlen($namespace) ? ("_$namespace") : '';
        if (!Cache::has($cacheKey)) {
            $translations = App::get('database_laravel_translation')->getTranslation($locale, $group, $namespace);
            $mapTranslations = [];
            foreach ($translations as $v) {
                $mapTranslations[$v->source->key] = $v->translation;
            }
            Cache::put($cacheKey, $mapTranslations, ceil($this->lifetimeSecond / 60));
        } else {
            $mapTranslations = Cache::get($cacheKey);
        }
        return $mapTranslations;
    }

    public function addNamespace($namespace, $hint)
    {
        $a = 1;
        // TODO: Implement addNamespace() method.
    }

    public function addJsonPath($path)
    {
        $a = 1;
        // TODO: Implement addJsonPath() method.
    }

    public function namespaces()
    {
        $a = 1;
        // TODO: Implement namespaces() method.
    }
}
