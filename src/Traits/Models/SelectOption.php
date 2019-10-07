<?php

namespace Foris\LaExtension\Traits\Models;

/**
 * Trait SelectOption
 */
trait SelectOption
{
    /**
     * 选项列表需要展示的属性值
     *
     * @var array
     */
    protected $selectOptions = ['id', 'name'];

    /**
     * 选项列表需要展示的属性值
     *
     * @return array
     */
    public function getSelectOptionKeys()
    {
        return $this->selectOptions;
    }
}