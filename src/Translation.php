<?php

namespace Jqqjj\LaravelDatabaseTranslation;

use Illuminate\Support\Facades\Cache;
use Jqqjj\LaravelDatabaseTranslation\Models\Language;
use Jqqjj\LaravelDatabaseTranslation\Models\LanguageSource;
use Jqqjj\LaravelDatabaseTranslation\Models\LanguageTranslation;

class Translation
{
    protected $lifetimeSecond;

    public function __construct($lifetimeSecond)
    {
        $this->lifetimeSecond = $lifetimeSecond;
    }

    /**
     * @param $code
     * @param $localName
     * @return Language
     */
    public function createLanguage($code, $localName)
    {
        $exist = $this->getLanguage($code);
        if(!empty($exist)){
            $exist->name = $localName;
            $exist->save();
            return $exist;
        }
        $lang = Language::create([
            'language_code'=>$code,
            'name'=>$localName,
        ]);
        $lang->refresh();
        return $lang;
    }

    /**
     * @param $code
     * @return Language|null
     */
    public function getLanguage($code)
    {
        return Language::where(['language_code'=>$code])->first();
    }

    public function getLanguages()
    {
        return Language::get();
    }

    public function getEnabledLanguages()
    {
        return Language::enabled(1)->get();
    }

    /**
     * @param $code
     * @return int
     */
    public function enableLanguage($code)
    {
        return Language::where(['language_code'=>$code,'enabled'=>0])->update(['enabled'=>true]);
    }

    /**
     * @param $code
     * @return int
     */
    public function disableLanguage($code)
    {
        return Language::where(['language_code'=>$code,'enabled'=>1])->update(['enabled'=>false]);
    }

    public function getTranslations($locale, $group, $namespace)
    {
        $cacheKey = 'laravel_database_translation_'.$locale;
        $cacheKey .= is_string($group) && strlen($group) ? ("_$group") : '';
        $cacheKey .= is_string($namespace) && strlen($namespace) ? ("_$namespace") : '';
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $sources = LanguageSource::where(function ($query) use($group, $namespace) {
            if(!empty($group)){
                $query->where('group', $group);
            }
            if(!empty($namespace)){
                $query->where('namespace', $namespace);
            }
            return $query;
        })->get();

        $translations = LanguageTranslation::whereHas('language', function ($query) use($locale) {
            return $query->where(['language_code'=>$locale])->enabled(1);
        })->whereIn('language_source_id', $sources->pluck('language_source_id')->toArray())->get();

        $translationMaps = [];
        foreach ($sources as $v) {
            foreach ($translations as $t) {
                if ($v->language_source_id == $t->language_source_id) {
                    $translationMaps[$v->key] = $t->translation;
                    continue 2;
                }
            }
            $translationMaps[$v->key] = null;
        }

        Cache::put($cacheKey, $translationMaps, ceil($this->lifetimeSecond / 60));

        return $translationMaps;
    }

    public function getGroups($namespace = null)
    {
        return LanguageSource::where(function ($query) use($namespace) {
            if (!empty($namespace)) {
                $query->where(['namespace'=>$namespace]);
            }
            return $query;
        })->groupBy('group')->pluck('group')->toArray();
    }

    public function getNamespaces()
    {
        return LanguageSource::groupBy('namespace')->pluck('namespace')->toArray();
    }

    public function clearCache($locale, $group, $namespace)
    {
        $cacheKey = 'laravel_database_translation_'.$locale;
        $cacheKey .= is_string($group) && strlen($group) ? ("_$group") : '';
        $cacheKey .= is_string($namespace) && strlen($namespace) ? ("_$namespace") : '';
        if (Cache::has($cacheKey)) {
            Cache::forget($cacheKey);
        }
    }
}
