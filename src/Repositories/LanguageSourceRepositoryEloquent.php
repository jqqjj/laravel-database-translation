<?php

namespace Jqqjj\LaravelDatabaseTranslation\Repositories;

use Jqqjj\LaravelDatabaseTranslation\Models\LanguageSource;
use Prettus\Repository\Contracts\RepositoryInterface;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Eloquent\BaseRepository;

/**
 * Class LanguageSourceRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class LanguageSourceRepositoryEloquent extends BaseRepository implements RepositoryInterface
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return LanguageSource::class;
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
