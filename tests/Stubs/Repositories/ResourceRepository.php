<?php

namespace Foris\LaExtension\Tests\Stubs\Repositories;

use Foris\LaExtension\Repositories\CrudRepository;
use Foris\LaExtension\Tests\Stubs\Models\Resource;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ResourceRepository
 */
class ResourceRepository extends CrudRepository
{
    /**
     * Resource model instance
     *
     * @var Resource
     */
    protected $model;

    /**
     * Get resource model instance
     *
     * @param bool $newInstance
     * @return Model|Resource
     */
    public function model($newInstance = false): Model
    {
        if ($newInstance) {
            return new Resource();
        }

        if (!$this->model instanceof Resource) {
            $this->model = new Resource();
        }

        return $this->model;
    }
}
