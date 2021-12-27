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
 * @method static int enableLanguage($code)
 * @method static int disableLanguage($code)
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
