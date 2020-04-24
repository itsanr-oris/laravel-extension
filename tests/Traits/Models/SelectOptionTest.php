<?php

namespace Foris\LaExtension\Tests\Traits\Models;

use Foris\LaExtension\Tests\TestCase;

/**
 * Class SelectOptionTest
 */
class SelectOptionTest extends TestCase
{
    /**
     * 测试获取下拉选项的字段信息
     */
    public function testGetSelectOptionKeys()
    {
        $this->assertEquals(['id', 'name'], (new DummyModel())->getSelectOptionKeys());
    }
}
