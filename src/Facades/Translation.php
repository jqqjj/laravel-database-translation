<?php

namespace Jqqjj\LaravelDatabaseTranslation\Facades;

use Illuminate\Support\Facades\Facade;
use Jqqjj\LaravelDatabaseTranslation\Models\Language;

/**
 * Class Translation
 * @package Jqqjj\LaravelDatabaseTranslation\Facades
 *
 * @method static Language createLanguage($code, $localName)
 * @method static Language|null getLanguage($code)
 * @method static Language|null getEnabledLanguage($code)
 * @method static Language[] getLanguages()
 * @method static Language[] getEnabledLanguages()
 * @method static int enableLanguage($code)
 * @method static int disableLanguage($code)
 * @method static void deleteLanguage($code)
 * @method static array getTranslations($locale, $group, $namespace)
 * @method static array getGroups($namespace = null)
 * @method static array getNamespaces()
 * @method static void clearCache($locale, $group, $namespace)
 * @method static void clearCacheAll()
 *
 * @see \Jqqjj\LaravelDatabaseTranslation\Translation
 */
class Translation extends Facade
{
    protected static function getFacadeAccessor()
    {
        return "laravel_database_translation";
    }
}
