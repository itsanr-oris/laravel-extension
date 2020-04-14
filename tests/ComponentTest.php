<?php /** @noinspection PhpIncludeInspection */

namespace Foris\LaExtension\Tests;

/**
 * Class ComponentTest
 */
class ComponentTest extends TestCase
{
    /**
     * Test component auto discover
     */
    public function testComponentDiscover()
    {
        $class = 'App\Components\Module\AutoRegisterComponent';
        $facade = 'App\Components\Module\Facade\AutoRegisterComponent';

        $this->assertTrue(class_exists($class));
        $this->assertInstanceOf($class, app(call_user_func([$class, 'name'])));
        $this->assertInstanceOf($class, call_user_func([$facade, 'getFacadeRoot']));

        $alias = call_user_func([$facade, 'aliasName']);
        $this->assertInstanceOf($class, call_user_func([$alias, 'getFacadeRoot']));
    }
}
