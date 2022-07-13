<?php

namespace Jqqjj\LaravelDatabaseTranslation\Repositories;

use Jqqjj\LaravelDatabaseTranslation\Models\Language;
use Prettus\Repository\Contracts\RepositoryInterface;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Eloquent\BaseRepository;

/**
 * Class LanguageRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class LanguageRepositoryEloquent extends BaseRepository implements RepositoryInterface
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Language::class;
    }


    /**
     * Boot up the repository, pushing criteria
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

}
