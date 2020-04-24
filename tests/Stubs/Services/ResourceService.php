<?php

namespace Foris\LaExtension\Tests\Stubs\Services;

use Foris\LaExtension\Repositories\CrudRepository;
use Foris\LaExtension\Services\CrudService;
use Foris\LaExtension\Tests\Stubs\Repositories\ResourceRepository;

/**
 * Class ResourceService
 */
class ResourceService extends CrudService
{
    /**
     * Get resource repository instance
     *
     * @return CrudRepository|ResourceRepository
     */
    public function repository(): CrudRepository
    {
        return new ResourceRepository();
    }
}
