<?php

namespace Jqqjj\LaravelDatabaseTranslation\Facades;

use Illuminate\Support\Facades\Facade;
use Jqqjj\LaravelDatabaseTranslation\Models\Language;
use Jqqjj\LaravelDatabaseTranslation\Models\LanguageSource;
use Jqqjj\LaravelDatabaseTranslation\Models\LanguageTranslation;

/**
 * Class Translation
 * @package Jqqjj\LaravelDatabaseTranslation\Facades
 *
 * @method static Language createLanguage($code, $localName)
 * @method static Language|null getLanguage($code)
 * @method static Language[] getLanguages()
 * @method static Language|null getEnabledLanguage($code)
 * @method static Language[] getEnabledLanguages()
 * @method static int enableLanguage($code)
 * @method static int disableLanguage($code)
 * @method static void deleteLanguage($code)
 *
 * @method static LanguageSource createSource($key, $group, $namespace = '*')
 * @method static LanguageSource|null getSource($key, $group, $namespace = '*')
 * @method static LanguageSource[] getSources($group, $namespace = '*')
 * @method static bool deleteSource($key, $group, $namespace = '*')
 * @method static bool deleteSources($group, $namespace = '*')
 *
 * @method static LanguageTranslation createTranslation($translation, Language $lang, LanguageSource $source)
 * @method static LanguageTranslation|null getTranslation(Language $lang, LanguageSource $source)
 * @method static array getTranslations($locale, $group, $namespace = '*')
 *
 * @method static array getGroups($namespace = '*')
 * @method static array getNamespaces()
 * @method static void clearCache($locale, $group, $namespace = '*')
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
