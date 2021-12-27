<?php

namespace Jqqjj\LaravelDatabaseTranslation\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class LanguageTranslation
 * @package Jqqjj\LaravelDatabaseTranslation\Models
 *
 * @property int language_translation_id
 * @property int language_id
 * @property int language_source_id
 * @property string translation
 * @property string created_at
 * @property string updated_at
 *
 * @property Language language
 * @property LanguageSource source
 */
class LanguageTranslation extends Model
{
    protected $primaryKey = 'language_translation_id';

    protected $fillable = [
        'language_id',
        'language_source_id',
        'translation',
    ];

    public function language()
    {
        return $this->belongsTo(Language::class,'language_id','language_id');
    }

    public function source()
    {
        return $this->belongsTo(LanguageSource::class,'language_source_id','language_source_id');
    }
}
