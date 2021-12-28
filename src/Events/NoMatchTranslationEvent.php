<?php

namespace Jqqjj\LaravelDatabaseTranslation\Events;

class NoMatchTranslationEvent
{
    /**
     * @var string
     */
    public $key;

    /**
     * @var string
     */
    public $group;

    /**
     * @var string
     */
    public $namespace;

    /**
     * @var string
     */
    public $lang;

    public function __construct($key, $group, $namespace, $lang)
    {
        $this->key = $key;
        $this->group = $group;
        $this->namespace = $namespace;
        $this->lang = $lang;
    }
}
