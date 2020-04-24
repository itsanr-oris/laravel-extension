<?php

namespace Foris\LaExtension\Tests\Traits\Models;

use Foris\LaExtension\Tests\TestCase;
use Foris\LaExtension\Traits\Models\StatusDefinition;

/**
 * Class StatusOperationTest
 */
class StatusOperationTest extends TestCase
{
    /**
     * 测试获取状态字段
     */
    public function testGetStatusKeyName()
    {
        $this->assertEquals('status', (new DummyModel())->getStatusKeyName());
    }

    /**
     * test status translate
     */
    public function testStatusTranslate()
    {
        $map = [
            StatusDefinition::STATUS_ENABLE => '启用',
            StatusDefinition::STATUS_DISABLE => '禁用',
        ];

        $this->assertEquals($map, DummyModel::statusTranslate());
        $this->assertEquals($map[StatusDefinition::STATUS_ENABLE], DummyModel::statusTranslate(StatusDefinition::STATUS_ENABLE));
    }
}
