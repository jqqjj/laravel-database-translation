<?php

namespace Jqqjj\LaravelDatabaseTranslation;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\App;
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
     * @param string $code
     * @param string $localName
     * @return Language
     */
    public function createLanguage($code, $localName)
    {
        $exist = $this->getLanguage($code);
        if(!empty($exist)){
            $exist->update(['name'=>$localName]);
            return $exist;
        }
        $lang = Language::create([
            'language_code' => $code,
            'name' => $localName,
        ]);
        $lang->refresh();
        return $lang;
    }

    /**
     * @param string $key
     * @param string $group
     * @param string $namespace
     * @return LanguageSource
     */
    public function createSource($key, $group = '*', $namespace = '*')
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
     * @param string $text
     * @param Language|int $language
     * @param LanguageSource|int $source
     * @return LanguageTranslation
     */
    public function createTranslation($text, $language, $source)
    {
        $exist = $this->getTranslation($language, $source);
        if (!empty($exist)) {
            $exist->update(['translation'=>$text]);
            return $exist;
        }
        $language_id = $language instanceof Language ? $language->language_id : intval($language);
        $source_id = $source instanceof LanguageSource ? $source->language_source_id : intval($source);
        $new = LanguageTranslation::create([
            'language_id' => $language_id,
            'language_source_id' => $source_id,
            'translation' => $text,
        ]);
        $new->refresh();
        return $new;
    }

    /**
     * @param string $code
     * @return Language|null
     */
    public function getLanguage($code)
    {
        return Language::where(['language_code' => $code])->first();
    }

    /**
     * @return Language[]
     */
    public function getLanguages()
    {
        return Language::get();
    }

    /**
     * @param string $code
     * @return Language|null
     */
    public function getEnabledLanguage($code)
    {
        return Language::enabled(1)->where(['language_code' => $code])->first();
    }

    /**
     * @return Language[]
     */
    public function getEnabledLanguages()
    {
        return Language::enabled(1)->get();
    }

    /**
     * @param $key
     * @param string $group
     * @param string $namespace
     * @return LanguageSource|null
     */
    public function getSource($key, $group = '*', $namespace = '*')
    {
        return LanguageSource::where([
            'namespace' => $namespace,
            'group' => $group,
            'key' => $key,
        ])->first();
    }

    /**
     * @param string $group
     * @param string $namespace
     * @return LanguageSource[]
     */
    public function getSources($group = "", $namespace = "")
    {
        return LanguageSource::where(function ($query) use($group, $namespace) {
            if (!empty($group)) {
                $query = $query->where('group', $group);
            }
            if (!empty($namespace)) {
                $query = $query->where('namespace', $namespace);
            }
            return $query;
        })->get();
    }

    /**
     * @param Language|int $language
     * @param LanguageSource|int $source
     * @return LanguageTranslation|null
     */
    public function getTranslation($language, $source)
    {
        $language_id = $language instanceof Language ? $language->language_id : intval($language);
        $source_id = $source instanceof LanguageSource ? $source->language_source_id : intval($source);
        return LanguageTranslation::where([
            'language_id' => $language_id,
            'language_source_id' => $source_id,
        ])->first();
    }

    /**
     * @param array $where
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function paginateLanguage($where = [], $perPage = 15)
    {
        return Language::where($where)->paginate($perPage);
    }

    /**
     * @param array $where
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function paginateSource($where = [], $perPage = 15)
    {
        return LanguageSource::where($where)->paginate($perPage);
    }

    /**
     * @param array $where
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function paginateTranslations($where = [], $perPage = 15)
    {
        return LanguageTranslation::where($where)->paginate($perPage);
    }

    /**
     * @param string $code
     * @return bool
     */
    public function enableLanguage($code)
    {
        return !!Language::where(['language_code'=>$code,'enabled'=>0])->update(['enabled'=>true]);
    }

    /**
     * @param string $code
     * @return bool
     */
    public function disableLanguage($code)
    {
        if (Language::where(['language_code'=>$code,'enabled'=>1])->update(['enabled'=>false])) {
            $this->clearLanguageCache($code);
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param string $code
     * @return bool
     */
    public function deleteLanguage($code)
    {
        if (Language::where(['language_code'=>$code])->delete()) {
            $this->clearLanguageCache($code);
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param string $key
     * @param string $group
     * @param string $namespace
     * @return bool
     */
    public function deleteSource($key, $group = '*', $namespace = '*')
    {
        if (LanguageSource::where([
            'namespace' => $namespace,
            'group' => $group,
            'key' => $key,
        ])->delete()) {
            $this->clearAllCache();
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param Language|int $language
     * @param LanguageSource|int $source
     * @return bool
     */
    public function deleteTranslation($language, $source)
    {
        $language_id = $language instanceof Language ? $language->language_id : intval($language);
        $source_id = $source instanceof LanguageSource ? $source->language_source_id : intval($source);
        if (LanguageTranslation::where([
            'language_id' => $language_id,
            'language_source_id' => $source_id,
        ])->delete()) {
            $this->clearLanguageCache($language instanceof Language ? $language : Language::findOrFail($language));
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param $languageCode
     * @param string $group
     * @param string $namespace
     * @return array
     */
    public function getCacheableTranslations($languageCode, $group = '*', $namespace = '*')
    {
        $cacheKey = $this->_getCacheKey($languageCode, $group, $namespace);
        if (!App::hasDebugModeEnabled() && Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $sources = $this->getSources($group, $namespace);

        $translations = LanguageTranslation::whereHas('language', function ($query) use($languageCode) {
            return $query->where(['language_code'=>$languageCode])->enabled(1);
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

        if (!App::hasDebugModeEnabled()) {
            Cache::put($cacheKey, $translationMaps, ceil($this->lifetimeSecond / 60));
        }

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
     * @return void
     */
    public function clearAllCache()
    {
        $languages = $this->getLanguages();
        foreach ($languages as $lang) {
            $this->clearLanguageCache($lang);
        }
    }

    /**
     * @param Language|string $language
     * @return void
     */
    public function clearLanguageCache($language)
    {
        $languageCode = $language instanceof Language ? $language->language_code : (string)$language;
        $namespaces = $this->getNamespaces();
        foreach ($namespaces as $namespace) {
            $groups = $this->getGroups($namespace);
            foreach ($groups as $group) {
                $this->_clearCache($languageCode, $group, $namespace);
            }
        }
    }

    /**
     * @param Language|string $language
     * @param string $group
     * @param string $namespace
     */
    protected function _clearCache($language, $group, $namespace = '*')
    {
        $cacheKey = $this->_getCacheKey($language, $group, $namespace);
        if (Cache::has($cacheKey)) {
            Cache::forget($cacheKey);
        }
    }

    /**
     * @param Language|string $language
     * @param string $group
     * @param string $namespace
     * @return string
     */
    protected function _getCacheKey($language, $group, $namespace = '*')
    {
        $languageCode = $language instanceof Language ? $language->language_code : (string)$language;
        $cacheKey = 'laravel_database_translation_'.$languageCode;
        $cacheKey .= is_string($group) && strlen($group) ? ("_$group") : '';
        $cacheKey .= is_string($namespace) && strlen($namespace) ? ("_$namespace") : '';
        return $cacheKey;
    }
}
