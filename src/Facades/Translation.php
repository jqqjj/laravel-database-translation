<?php

namespace Jqqjj\LaravelDatabaseTranslation\Facades;

use Illuminate\Pagination\LengthAwarePaginator;
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
 * @method static Language|null getEnabledLanguage($code)
 * @method static Language[] getLanguages()
 * @method static Language[] getEnabledLanguages()
 * @method static LengthAwarePaginator paginateLanguage($where = [], $perPage = 15)
 * @method static bool enableLanguage($code)
 * @method static bool disableLanguage($code)
 * @method static bool deleteLanguage($code)
 *
 * @method static LanguageSource createSource($key, $group = '*', $namespace = '*')
 * @method static LanguageSource|null getSource($key, $group = '*', $namespace = '*')
 * @method static LanguageSource[] getSources($group = "", $namespace = "")
 * @method static bool deleteSource($key, $group = '*', $namespace = '*')
 * @method static LengthAwarePaginator[] paginateSource($where = [], $perPage = 15)
 *
 * @method static LanguageTranslation createTranslation($text, $language, $source)
 * @method static LanguageTranslation|null getTranslation($language, $source)
 * @method static bool deleteTranslation($language, $source)
 * @method static array getCacheableTranslations($languageCode, $group = '*', $namespace = '*')
 *
 * @method static array getGroups($namespace = '*')
 * @method static array getNamespaces()
 * @method static void clearLanguageCache($language)
 * @method static void clearAllCache()
 *
 * @see \Jqqjj\LaravelDatabaseTranslation\Translation
 */
class Translation extends Facade
{
    protected static function getFacadeAccessor()
    {
        return "laravel.database.translation";
    }
}
