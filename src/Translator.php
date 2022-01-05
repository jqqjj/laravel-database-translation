<?php

namespace Jqqjj\LaravelDatabaseTranslation;

use Illuminate\Translation\Translator as LaravelTranslator;
use Jqqjj\LaravelDatabaseTranslation\Events\NoMatchTranslationEvent;

class Translator extends LaravelTranslator
{
    public function get($key, array $replace = [], $locale = null, $fallback = true)
    {
        $locale = $locale ?: $this->locale;

        // For JSON translations, there is only one file per locale, so we will simply load
        // that file and then we will be ready to check the array for the key. These are
        // only one level deep so we do not need to do any fancy searching through it.
        $this->load('*', '*', $locale);

        $line = $this->loaded['*']['*'][$locale][$key] ?? null;
        if (!empty($line)) {
            return $this->makeReplacements($line, $replace);
        }

        [$namespace, $group, $item] = $this->parseKey($key);

        // Here we will get the locale that should be used for the language line. If one
        // was not passed, we will use the default locales which was given to us when
        // the translator was instantiated. Then, we can load the lines and return.
        $locales = $fallback ? $this->localeArray($locale) : [$locale];

        foreach ($locales as $lang) {
            if (! is_null($line = $this->getLine(
                $namespace, $group, $lang, $item, $replace
            ))) {
                return $line;
            }
        }

        event(new NoMatchTranslationEvent($item, $group, $namespace, $locale));

        if (in_array($group, ['validation'])) {
            return $this->makeReplacements($key, $replace);
        } else {
            return $this->makeReplacements($item, $replace);
        }
    }

    /**
     * Parse a key into namespace, group, and item.
     *
     * @param  string  $key
     * @return array
     */
    public function parseKey($key)
    {
        if (isset($this->parsed[$key])) {
            return $this->parsed[$key];
        }

        $namespace_segments = array_filter(explode('::', $key, 2));
        if (count($namespace_segments) < 2) {
            array_unshift($namespace_segments, '*');
        }
        $group_segments = array_filter(explode('.', $namespace_segments[1], 2));
        if (count($group_segments) < 2) {
            array_unshift($group_segments, '*');
        }

        $parsed = [$namespace_segments[0], $group_segments[0], $group_segments[1]];

        return $this->parsed[$key] = $parsed;
    }
}
