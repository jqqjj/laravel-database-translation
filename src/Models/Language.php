<?php

namespace Jqqjj\LaravelDatabaseTranslation\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Language
 * @package Jqqjj\LaravelDatabaseTranslation\Models
 *
 * @property int language_id
 * @property string language_code
 * @property string name
 * @property boolean enabled
 * @property string created_at
 * @property string updated_at
 */
class Language extends Model
{
    protected $primaryKey = 'language_id';

    protected $fillable = [
        'language_code',
        'name',
        'enabled',
    ];

    public function scopeEnabled($query, $enabled = 1)
    {
        return $query->where(['enabled'=>$enabled]);
    }
}
