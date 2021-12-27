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
 */
class LanguageSource extends Model
{
    protected $primaryKey = 'language_source_id';

    protected $fillable = [
        'namespace',
        'group',
        'key',
    ];
}
