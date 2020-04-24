<?php

namespace Foris\LaExtension\Tests\Traits\Models;

use Foris\LaExtension\Traits\Models\ColumnTranslate;
use Foris\LaExtension\Traits\Models\SelectOption;
use Foris\LaExtension\Traits\Models\StatusDefinition;
use Foris\LaExtension\Traits\Models\StatusOperation;
use Illuminate\Database\Eloquent\Model;

/**
 * Class DummyModel
 */
class DummyModel extends Model implements StatusDefinition
{
    use SelectOption, StatusOperation, ColumnTranslate;

    /**
     * 不允许批量填充字段
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * attribute a translate
     *
     * @return string
     */
    public function attrATranslate()
    {
        if (empty($this['attr_a'])) {
            return '';
        }

        return 'attribute [a] translate';
    }

    /**
     * attribute b translate
     *
     * @return string
     */
    public function attrBTranslate()
    {
        if (empty($this['attr_b'])) {
            return '';
        }

        return 'attribute [b] translate';
    }
}
