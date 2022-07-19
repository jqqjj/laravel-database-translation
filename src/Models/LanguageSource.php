<?php

namespace Jqqjj\LaravelDatabaseTranslation\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class LanguageSource
 * @package Jqqjj\LaravelDatabaseTranslation\Models
 *
 * @property int language_source_id
 * @property string namespace
 * @property string group
 * @property string key
 * @property string created_at
 * @property string updated_at
 *
 * @property LanguageTranslation[] $translations
 */
class LanguageSource extends Model
{
    protected $primaryKey = 'language_source_id';

    protected $fillable = [
        'namespace',
        'group',
        'key',
    ];

    public function translationText(Language $language)
    {
        $matches = $this->translations->filter(function ($v) use ($language) {
            return $v->language_id == $language->language_id;
        });
        return $matches->isEmpty() ? null : $matches->first()->translation;
    }

    public function translations()
    {
        return $this->hasMany(LanguageTranslation::class, 'language_source_id', 'language_source_id');
    }
}
