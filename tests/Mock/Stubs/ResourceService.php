<?php

namespace Foris\LaExtension\Tests\Mock\Stubs;

use Foris\LaExtension\Repositories\CrudRepository;
use Foris\LaExtension\Services\CrudService;

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
