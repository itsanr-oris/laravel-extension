<?php

namespace Foris\LaExtension\Tests;

use Foris\LaExtension\Tests\Stubs\Models\Resource;
use Foris\LaExtension\Tests\Stubs\Services\ResourceService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Pagination\Paginator;

/**
 * Class CrudServiceTest
 */
class ServiceTest extends TestCase
{
    /**
     * Get resource service instance
     *
     * @return ResourceService
     */
    public function service()
    {
        return new ResourceService();
    }

    /**
     * Test get resource paginate info
     */
    public function testGetPaginateList()
    {
        $page = $this->service()->list([]);
        $this->assertInstanceOf(LengthAwarePaginator::class, $page);
    }

    /**
     * Test get resource simple paginate info
     */
    public function testGetSimplePaginateList()
    {
        $page = $this->service()->list([], true);
        $this->assertInstanceOf(Paginator::class, $page);
    }

    /**
     * Test get resource detail
     */
    public function testDetail()
    {
        $resource = Resource::query()->first();
        $model = $this->service()->repository()->model();
        $this->assertInstanceOf(get_class($model), $this->service()->detail($resource['id']));
        return $resource['id'];
    }

    /**
     * Test create resource info
     */
    public function testCreate()
    {
        $data = [
            'name' => 'new resource name',
            'desc' => 'new resource desc',
        ];
        $resource = $this->service()->create($data);
        $this->assertInstanceOf(get_class($this->service()->repository()->model()), $resource);
    }

    /**
     * Test update resource info
     *
     * @param $id
     * @depends testDetail
     */
    public function testUpdate($id)
    {
        $data = [
            'name' => 'update resource name',
            'desc' => 'update resource desc',
        ];
        $resource = $this->service()->update($id, $data);
        $this->assertInstanceOf(get_class($this->service()->repository()->model()), $resource);
    }

    /**
     * Test save resource info
     *
     * @param $id
     * @depends testDetail
     */
    public function testSave($id)
    {
        $data = [
            'name' => 'resource name',
            'desc' => 'resource desc'
        ];
        $model = $this->service()->repository()->model();
        $this->assertInstanceOf(get_class($model), $this->service()->save($data));
        $this->assertInstanceOf(get_class($model), $this->service()->save(array_merge($data, ['id' => $id])));
    }

    /**
     * Test delete resource info
     *
     * @param $id
     * @throws \Foris\LaExtension\Exceptions\ErrorException
     * @depends testDetail
     */
    public function testDelete($id)
    {
        $this->assertIsInt($this->service()->delete($id));
    }

    /**
     * Test batch delete resource info
     *
     * @throws \Throwable
     */
    public function testBatchDelete()
    {
        $ids = $this->service()->repository()->model()->query()->select('id')->get()->pluck('id')->all();
        $this->assertTrue($this->service()->batchDelete($ids));
    }
}
