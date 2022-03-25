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
            'language_code' => $code,
            'name' => $localName,
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
        return Language::where(['language_code' => $code])->first();
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
    public function getLanguages()
    {
        return Language::get();
    }

    /**
     * @return LengthAwarePaginator
     */
    public function paginate($perPage = 15)
    {
        return Language::paginate($perPage);
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
     * @return bool
     */
    public function deleteLanguage($code)
    {
        return Language::where(['language_code'=>$code])->delete();
    }

    /**
     * @param $key
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
    public function getGroupSources($group, $namespace = '*')
    {
        return LanguageSource::where(function ($query) use($namespace) {
            if (!empty($namespace)) {
                $query->where('namespace', $namespace);
            }
            return $query;
        })->where(['group' => $group])->get();
    }

    /**
     * @param string $namespace
     * @return LanguageSource[]
     */
    public function getNamespaceSources($namespace)
    {
        return LanguageSource::where(['namespace' => $namespace])->get();
    }

    /**
     * @param $key
     * @param string $group
     * @param string $namespace
     * @return bool
     */
    public function deleteSource($key, $group = '*', $namespace = '*')
    {
        return LanguageSource::where([
            'namespace' => $namespace,
            'group' => $group,
            'key' => $key,
        ])->delete();
    }

    /**
     * @param string $group
     * @param string $namespace
     * @return bool
     */
    public function deleteGroupSources($group, $namespace = '*')
    {
        return LanguageSource::where([
            'namespace' => $namespace,
            'group' => $group,
        ])->delete();
    }

    /**
     * @param string $namespace
     * @return bool
     */
    public function deleteNamespaceSources($namespace)
    {
        return LanguageSource::where([
            'namespace' => $namespace,
        ])->delete();
    }

    /**
     * @param $text
     * @param Language|int $language
     * @param LanguageSource|int $source
     * @return LanguageTranslation
     */
    public function createTranslation($text, $language, $source)
    {
        $exist = $this->getTranslation($language, $source);
        if (!empty($exist)) {
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
     * @param $languageCode
     * @param string $group
     * @param string $namespace
     * @return array
     */
    public function getTranslations($languageCode, $group = '*', $namespace = '*')
    {
        $cacheKey = 'laravel_database_translation_'.$languageCode;
        $cacheKey .= is_string($group) && strlen($group) ? ("_$group") : '';
        $cacheKey .= is_string($namespace) && strlen($namespace) ? ("_$namespace") : '';
        if (!App::hasDebugModeEnabled() && Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $sources = $this->getGroupSources($group, $namespace);

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
     * @param Language|int $language
     * @param LanguageSource|int $source
     * @return bool
     */
    public function deleteTranslation($language, $source)
    {
        $language_id = $language instanceof Language ? $language->language_id : intval($language);
        $source_id = $source instanceof LanguageSource ? $source->language_source_id : intval($source);
        return LanguageTranslation::where([
            'language_id' => $language_id,
            'language_source_id' => $source_id,
        ])->delete();
    }

    /**
     * @param Language|int $language
     * @param string $group
     * @param string $namespace
     * @return bool
     */
    public function deleteGroupTranslations($language, $group, $namespace = '*')
    {
        $language_id = $language instanceof Language ? $language->language_id : intval($language);
        return LanguageTranslation::whereHas('source', function ($query) use ($group, $namespace) {
            return $query->where([
                'group' => $group,
                'namespace' => $namespace,
            ]);
        })->where(['language_id' => $language_id])->delete();
    }

    /**
     * @param Language|int $language
     * @param string $namespace
     * @return bool
     */
    public function deleteNamespaceTranslations($language, $namespace)
    {
        $language_id = $language instanceof Language ? $language->language_id : intval($language);
        return LanguageTranslation::whereHas('source', function ($query) use ($namespace) {
            return $query->where(['namespace' => $namespace]);
        })->where(['language_id' => $language_id])->delete();
    }

    /**
     * @param Language|int $language
     * @return bool
     */
    public function deleteLanguageTranslations($language)
    {
        $language_id = $language instanceof Language ? $language->language_id : intval($language);
        return LanguageTranslation::where(['language_id' => $language_id])->delete();
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
     * @param Language|string $language
     * @param $group
     * @param string $namespace
     */
    public function clearCache($language, $group, $namespace = '*')
    {
        $languageCode = $language instanceof Language ? $language->language_code : (string)$language;
        $cacheKey = 'laravel_database_translation_'.$languageCode;
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
                    $this->clearCache($lang, $group, $namespace);
                }
            }
        }
    }
}
