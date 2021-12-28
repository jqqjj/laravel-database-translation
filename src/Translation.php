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

    /**
     * @return Language[]
     */
    public function getLanguages()
    {
        return Language::get();
    }

    /**
     * @param $code
     * @return Language|null
     */
    public function getEnabledLanguage($code)
    {
        return Language::enabled(1)->where(['language_code'=>$code])->first();
    }

    /**
     * @return Language[]
     */
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

    /**
     * @param $code
     */
    public function deleteLanguage($code)
    {
        Language::where(['language_code'=>$code])->delete();
    }

    /**
     * @param $key
     * @param $group
     * @param string $namespace
     * @return LanguageSource
     */
    public function createSource($key, $group, $namespace = '*')
    {
        $exist = $this->getSource($key, $group, $namespace);
        if(!empty($exist)){
            return $exist;
        }
        $lang = LanguageSource::create([
            'namespace' => $namespace,
            'group' => $group,
            'key' => $key,
        ]);
        $lang->refresh();
        return $lang;
    }

    /**
     * @param $key
     * @param $group
     * @param $namespace
     * @return LanguageSource|null
     */
    public function getSource($key, $group, $namespace = '*')
    {
        return LanguageSource::where([
            'namespace' => $namespace,
            'group' => $group,
            'key' => $key,
        ])->first();
    }

    /**
     * @param $group
     * @param $namespace
     * @return LanguageSource[]
     */
    public function getSources($group, $namespace = '*')
    {
        return LanguageSource::where(function ($query) use($group, $namespace) {
            if (!empty($group)) {
                $query->where('group', $group);
            }
            if (!empty($namespace)) {
                $query->where('namespace', $namespace);
            }
            return $query;
        })->get();
    }

    /**
     * @param $key
     * @param $group
     * @param $namespace
     * @return bool
     */
    public function deleteSource($key, $group, $namespace = '*')
    {
        return LanguageSource::where([
            'namespace' => $namespace,
            'group' => $group,
            'key' => $key,
        ])->delete();
    }

    /**
     * @param $group
     * @param $namespace
     * @return bool
     */
    public function deleteSources($group, $namespace = '*')
    {
        return LanguageSource::where([
            'namespace' => $namespace,
            'group' => $group,
        ])->delete();
    }

    /**
     * @param $translation
     * @param Language $lang
     * @param LanguageSource $source
     * @return LanguageTranslation
     */
    public function createTranslation($translation, Language $lang, LanguageSource $source)
    {
        $exist = $this->getTranslation($lang, $source);
        if (!empty($exist)) {
            return $exist;
        }
        $new = LanguageTranslation::create([
            'language_id' => $lang->language_id,
            'language_source_id' => $source->language_source_id,
            'translation' => $translation,
        ]);
        $new->refresh();
        return $new;
    }

    /**
     * @param Language $lang
     * @param LanguageSource $source
     * @return LanguageTranslation|null
     */
    public function getTranslation(Language $lang, LanguageSource $source)
    {
        return LanguageTranslation::where([
            'language_id' => $lang->language_id,
            'language_source_id' => $source->language_source_id,
        ])->first();
    }

    /**
     * @param $locale
     * @param $group
     * @param $namespace
     * @return array
     */
    public function getTranslations($locale, $group, $namespace = '*')
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

    /**
     * @param string $namespace
     * @return array
     */
    public function getGroups($namespace = '*')
    {
        return LanguageSource::where(function ($query) use($namespace) {
            if (!empty($namespace)) {
                $query->where(['namespace'=>$namespace]);
            }
            return $query;
        })->groupBy('group')->pluck('group')->toArray();
    }

    /**
     * @return array
     */
    public function getNamespaces()
    {
        return LanguageSource::groupBy('namespace')->pluck('namespace')->toArray();
    }

    /**
     * @param $locale
     * @param $group
     * @param string $namespace
     */
    public function clearCache($locale, $group, $namespace = '*')
    {
        $cacheKey = 'laravel_database_translation_'.$locale;
        $cacheKey .= is_string($group) && strlen($group) ? ("_$group") : '';
        $cacheKey .= is_string($namespace) && strlen($namespace) ? ("_$namespace") : '';
        if (Cache::has($cacheKey)) {
            Cache::forget($cacheKey);
        }
    }

    public function clearCacheAll()
    {
        $languages = $this->getLanguages();
        $namespaces = $this->getNamespaces();
        foreach ($languages as $lang) {
            foreach ($namespaces as $namespace) {
                $groups = $this->getGroups($namespace);
                foreach ($groups as $group) {
                    $this->clearCache($lang->language_code, $group, $namespace);
                }
            }
        }
    }
}
