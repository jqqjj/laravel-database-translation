<?php

namespace Jqqjj\LaravelDatabaseTranslation;

use Jqqjj\LaravelDatabaseTranslation\Models\Language;
use Jqqjj\LaravelDatabaseTranslation\Models\LanguageTranslation;

class Translation
{
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

    public function getTranslation($languageCode, $group, $namespace)
    {
        $query = LanguageTranslation::with(['source'])->whereHas('source', function ($query) use($group, $namespace) {
            if(!empty($group)){
                $query->where('group',$group);
            }
            if(!empty($namespace)){
                $query->where('namespace',$namespace);
            }
            return $query;
        })->whereHas('language', function ($query) use($languageCode) {
            return $query->where(['language_code'=>$languageCode])->enabled(1);
        });
        return $query->get();
    }
}
