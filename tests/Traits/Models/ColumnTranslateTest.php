<?php

namespace Foris\LaExtension\Tests\Traits\Models;

use Foris\LaExtension\Tests\TestCase;

/**
 * Class ColumnTranslateTest
 */
class ColumnTranslateTest extends TestCase
{
    /**
     * 测试自动进行字段转义
     */
    public function testAutoProcessColumnTranslate()
    {
        $translates = [
            'status' => DummyModel::statusTranslate(1),
            'attr_a' => 'attribute [a] translate',
            'attr_b' => 'attribute [b] translate',
        ];

        $model = new DummyModel();

        $arr = $model->fill([
            'status' => 1, 'attr_a' => 'attribute a', 'attr_b' => 'attribute b'
        ])->toArray();

        $this->assertEquals(true, $this->getConfig()->get('app-ext.initialize_model_column_translate', false));
        $this->assertEquals($translates, $arr['column_translates']);
    }

    /**
     * 测试预设字段转义
     */
    public function testPresetColumnTranslate()
    {
        $translates = [
            'status' => 'status translate',
            'attr_a' => 'attribute [a] translate!',
            'attr_b' => 'attribute [b] translate!',
        ];

        $model = new DummyModel();

        $arr = $model->fill([
            'status' => 1, 'attr_a' => 'attribute a', 'attr_b' => 'attribute b', 'column_translates' => $translates
        ])->toArray();

        $this->assertEquals($translates, $arr['column_translates']);
    }

    /**
     * 测试指定字段才进行转义处理
     */
    public function testSpecColumnCanProcessColumnTranslate()
    {
        $translates = [
            'status' => DummyModel::statusTranslate(1),
            'attr_a' => 'attribute [a] translate',
        ];

        $model = new DummyModel();
        $model->withTranslate(['status', 'attr_a']);

        $arr = $model->fill([
            'status' => 1, 'attr_a' => 'attribute a', 'attr_b' => 'attribute b'
        ])->toArray();

        $this->assertEquals($translates, $arr['column_translates']);
    }
}
