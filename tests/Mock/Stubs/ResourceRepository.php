<?php

namespace Foris\LaExtension\Tests\Mock\Stubs;

use Foris\LaExtension\Repositories\CrudRepository;
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
