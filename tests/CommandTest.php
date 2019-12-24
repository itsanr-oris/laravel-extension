<?php /** @noinspection PhpIncludeInspection */

namespace Foris\LaExtension\Tests;

use Foris\LaExtension\Repositories\CrudRepository;
use Foris\LaExtension\Repositories\Repository;
use Foris\LaExtension\Services\CrudService;
use Foris\LaExtension\Services\Service;
use Foris\LaExtension\Traits\Controllers\ExtResponse;
use Foris\LaExtension\Traits\Controllers\ResourceOperation;
use Illuminate\Support\Facades\Facade;
use ReflectionClass;

/**
 * Class ModelMakeTest
 */
class CommandTest extends TestCase
{
    /**
     * Test make repository command
     */
    public function testMakeRepositoryCommand()
    {
        $class = 'App\Repositories\TestRepository';
        $facade = 'App\Repositories\Facade\TestRepository';
        $classPath = app_path() . '/Repositories/TestRepository.php';
        $facadePath = app_path() . '/Repositories/Facade/TestRepository.php';

        $this->artisan('make:repository', ['name' => 'TestRepository', '--facade' => true]);
        $this->assertFileExists($classPath);
        $this->assertFileExists($facadePath);

        require_once $classPath;
        require_once $facadePath;
        $this->assertInstanceOf(Repository::class, new $class());
        $this->assertInstanceOf(Facade::class, new $facade());

        call_user_func([$class, 'register']);
        $this->assertInstanceOf($class, call_user_func([$facade, 'getFacadeRoot']));
    }

    /**
     * Test make curd repository command
     */
    public function testMakeCrudRepositoryCommand()
    {
        $class = 'App\Repositories\TestCrudRepository';
        $classPath = app_path() . '/Repositories/TestCrudRepository.php';

        $this->artisan('make:repository', ['name' => 'TestCrudRepository', '--model' => 'App\Models\CrudModel']);
        $this->assertFileExists($classPath);

        require_once $classPath;
        $this->assertInstanceOf(CrudRepository::class, new $class());
    }

    /**
     * Test make service command
     */
    public function testMakeServiceCommand()
    {
        $class = 'App\Services\TestService';
        $facade = 'App\Services\Facade\TestService';
        $classPath = app_path() . '/Services/TestService.php';
        $facadePath = app_path() . '/Services/Facade/TestService.php';

        $this->artisan('make:service', ['name' => 'TestService', '--facade' => true]);
        $this->assertFileExists($classPath);
        $this->assertFileExists($facadePath);

        require_once $classPath;
        require_once $facadePath;
        $this->assertInstanceOf(Service::class, new $class());
        $this->assertInstanceOf(Facade::class, new $facade());

        call_user_func([$class, 'register']);
        $this->assertInstanceOf($class, call_user_func([$facade, 'getFacadeRoot']));
    }

    /**
     * Test make crud service command
     */
    public function testMakeCrudServiceCommand()
    {
        $class = 'App\Services\TestCrudService';
        $classPath = app_path() . '/Services/TestCrudService.php';

        $this->artisan(
            'make:service',
            ['name' => 'TestCrudService', '--repository' => 'App\Repositories\TestCrudRepository']
        );
        $this->assertFileExists($classPath);

        require_once $classPath;
        $this->assertInstanceOf(CrudService::class, new $class());
    }

    /**
     * Test make controller command
     */
    public function testMakeControllerCommand()
    {
        $class = 'App\Http\Controllers\TestController';
        $classPath = app_path() . '/Http/Controllers/TestController.php';
        $this->artisan('make:controller', ['name' => 'TestController']);

        $this->assertFileExists($classPath);
        require_once $classPath;
        $this->assertInstanceOf('App\Http\Controllers\Controller', new $class());
    }

    /**
     * Test make crud controller command
     *
     * @throws \ReflectionException
     */
    public function testMakeCrudControllerCommand()
    {
        $class = 'App\Http\Controllers\TestCrudController';
        $classPath = app_path() . '/Http/Controllers/TestCrudController.php';

        $this->artisan('make:controller', [
            'name' => 'TestCrudController', '--service' => 'App\Services\TestCurdService', '--resource' => true
        ]);

        $this->assertFileExists($classPath);

        require_once $classPath;
        $reflect = new ReflectionClass($class);
        $this->assertTrue(in_array(ExtResponse::class, $reflect->getTraitNames()));
        $this->assertTrue(in_array(ResourceOperation::class, $reflect->getTraitNames()));
    }

    /**
     * Test make model command
     */
    public function testMakeModelCommand()
    {
        $this->artisan('make:model', ['name' => 'Test', '--resource' => true]);

        $this->assertFileExists(app_path() . '/Models/Test.php');
        $this->assertFileExists(app_path() . '/Repositories/TestRepository.php');
        $this->assertFileExists(app_path() . '/Repositories/Facade/TestRepository.php');
        $this->assertFileExists(app_path() . '/Services/TestService.php');
        $this->assertFileExists(app_path() . '/Services/Facade/TestService.php');
        $this->assertFileExists(app_path() . '/Http/Controllers/TestController.php');

        $this->artisan('make:model', ['name' => 'Test', '--resource' => true])->expectsOutput('Model already exists!');
    }

    /**
     * Test make facade command
     */
    public function testMakeFacadeCommand()
    {
        $this->app->bind('test.facade', function () {
            return 'test facade';
        });

        $class = 'App\TestFacade';
        $classPath = app_path() . '/TestFacade.php';
        $this->artisan('make:facade', ['name' => 'TestFacade', '--abstract' => 'test.facade']);

        $this->assertFileExists($classPath);
        require_once $classPath;
        $this->assertInstanceOf(Facade::class, new $class());
    }

    /**
     * Test make component facade command
     */
    public function testMakeComponentFacadeCommand()
    {
        $this->assertTrue(true);
    }
}
