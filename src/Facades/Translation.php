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
 * @method static LengthAwarePaginator paginate($perPage = 15)
 * @method static int enableLanguage($code)
 * @method static int disableLanguage($code)
 * @method static bool deleteLanguage($code)
 *
 * @method static LanguageSource createSource($key, $group = '*', $namespace = '*')
 * @method static LanguageSource|null getSource($key, $group = '*', $namespace = '*')
 * @method static LanguageSource[] getGroupSources($group, $namespace = '*')
 * @method static LanguageSource[] getNamespaceSources($namespace)
 * @method static bool deleteSource($key, $group = '*', $namespace = '*')
 * @method static bool deleteGroupSources($group, $namespace = '*')
 * @method static bool deleteNamespaceSources($namespace)
 *
 * @method static LanguageTranslation createTranslation($text, $language, $source)
 * @method static LanguageTranslation|null getTranslation($language, $source)
 * @method static array getTranslations($languageCode, $group = '*', $namespace = '*')
 * @method static bool deleteTranslation($language, $source)
 * @method static bool deleteGroupTranslations($language, $group, $namespace = '*')
 * @method static bool deleteNamespaceTranslations($language, $namespace)
 * @method static bool deleteLanguageTranslations($language)
 *
 * @method static array getGroups($namespace = '*')
 * @method static array getNamespaces()
 * @method static void clearCache($language, $group, $namespace = '*')
 * @method static void clearCacheAll()
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
