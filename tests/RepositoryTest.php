<?php

namespace Foris\LaExtension\Tests;

use Foris\LaExtension\Exceptions\ErrorException;
use Foris\LaExtension\Repositories\CrudRepository;
use Foris\LaExtension\Tests\Stubs\Models\Resource;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Mockery\Mock;

/**
 * Class CrudRepositoryTest
 */
class RepositoryTest extends TestCase
{
    /**
     * Get resource model instance
     *
     * @param bool $softDelete
     * @return Resource|Mock
     */
    protected function model($softDelete = true)
    {
        $model = new Resource();

        if ($softDelete) {
            return $model;
        }

        $mockModel = \Mockery::mock(Model::class)->makePartial();
        $mockModel->shouldReceive('getTable')->andReturn($model->getTable());
        return $mockModel;
    }

    /**
     * Get resource table name
     *
     * @return string
     */
    protected function table()
    {
        return $this->model()->getTable();
    }

    /**
     * Get crud repository instance
     *
     * @param bool $modelSoftDelete
     * @return Mock|CrudRepository
     */
    public function repository($modelSoftDelete = true)
    {
        $repository = \Mockery::mock(CrudRepository::class)->makePartial();
        $repository->shouldReceive('model')->andReturn($this->model($modelSoftDelete));
        return $repository;
    }

    /**
     * Test get query instance but soft-delete check false exception
     *
     * @throws ErrorException
     */
    public function testGetQueryCheckCanSoftDeleteFalseException()
    {
        $this->expectException(ErrorException::class);
        $this->expectExceptionMessage(
            sprintf("Model[%s] does not support soft-delete!", get_class($this->model(false)))
        );

        $this->repository(false)->checkSoftDelete(true)->withTrashed(true);
    }

    /**
     * Test create resource info
     */
    public function testCreate()
    {
        $data = [
            'name' => 'new resource name',
            'desc' => 'new resource desc'
        ];
        $this->assertDatabaseMissing($this->table(), $data);

        $resource = $this->repository()->create($data);
        $this->assertInstanceOf(get_class($this->model()), $resource);
        $this->assertTrue($resource->wasRecentlyCreated);
        $this->assertDatabaseHas($this->table(), $data);
    }

    /**
     * Test get resource info
     */
    public function testDetail()
    {
        $resource = $this->model()->newQuery()->first();
        $this->assertEquals($resource, $this->repository()->detail($resource['id']));
        return $resource['id'];
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
            'desc' => 'update resource desc'
        ];
        $this->assertDatabaseMissing($this->table(), $data);

        $resource = $this->repository()->update($id, $data);
        $this->assertInstanceOf(get_class($this->model()), $resource);
        $this->assertDatabaseHas($this->table(), $data);
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
        $this->assertDatabaseHas($this->table(), ['id' => $id]);
        $this->assertEquals(1, $this->repository(false)->delete($id));
        $this->assertDatabaseMissing($this->table(), ['id' => $id]);
    }

    /**
     * Test soft-delete resource info
     *
     * @param $id
     * @throws \Foris\LaExtension\Exceptions\ErrorException
     * @depends testDetail
     */
    public function testSoftDelete($id)
    {
        $repository = $this->repository()->checkSoftDelete();

        $deleteCount = $repository->delete($id);
        $this->assertEquals(1, $deleteCount);
        $this->assertSoftDeleted($this->table(), ['id' => $id]);

        $this->assertNull($repository->detail($id));
        $this->assertInstanceOf(get_class($this->model()), $repository->withTrashed(true)->detail($id));
    }

    /**
     * Test check model soft-delete false exception
     *
     * @throws ErrorException
     */
    public function testSoftDeleteCheckFalseException()
    {
        $this->expectException(ErrorException::class);
        $this->expectExceptionMessage(
            sprintf("Model[%s] does not support soft-delete!", get_class($this->model(false)))
        );

        $this->repository(false)->checkSoftDelete(true)->delete(1);
    }

    /**
     * Test get all resource info
     */
    public function testGetAllResourceInfo()
    {
        $collections = $this->repository()->all();
        foreach ($collections as $item) {
            $this->assertInstanceOf(get_class($this->model()), $item);
        }
    }

    /**
     * Test get resource paginate info
     */
    public function testGetPaginateList()
    {
        $filter = [
            'select' => ['id', 'name']
        ];

        $page = $this->repository()->list($filter);
        $this->assertInstanceOf(LengthAwarePaginator::class, $page);
    }

    /**
     * Test get resource simple paginate info
     */
    public function testGetSimplePaginateList()
    {
        $filter = [
            'select' => ['id', 'name']
        ];

        $page = $this->repository()->list($filter, true);
        $this->assertInstanceOf(Paginator::class, $page);
    }

    /**
     * Test batch get resource info by ids
     */
    public function testBatchGetDetailByIds()
    {
        $ids = $this->model()->query()->select('id')->get()->pluck('id')->all();
        $this->assertNotEmpty($ids);

        $collections = $this->repository()->batchDetail($ids);
        $this->assertEquals(count($ids), count($collections));
        foreach ($collections as $item) {
            $this->assertInstanceOf(get_class($this->model()), $item);
        }
    }

    /**
     * Test batch delete resource info by ids
     */
    public function testBatchDeleteByIds()
    {
        $ids = $this->model()->newQuery()->select('id')->get()->pluck('id')->all();
        $this->assertNotEmpty($ids);

        $deleteCount = $this->repository()->batchDelete($ids);
        $this->assertEquals(count($ids), $deleteCount);
        $this->assertEmpty($this->model()->newQuery()->select('id')->whereIn('id', $ids)->get());
    }

    /**
     * Test db transaction
     */
    public function testTransaction()
    {
        $data = [
            'name' => 'test transaction resource name',
            'desc' => 'test transaction resource desc',
        ];

        $this->assertDatabaseMissing($this->table(), $data);

        try {
            $this->repository()->transaction(function () use ($data) {
                $this->repository()->create($data);
                throw new \Exception('something wrong!');
            });
        } catch (\Throwable $e) {
            // ignore
        }

        $this->assertDatabaseMissing($this->table(), $data);
    }
}
