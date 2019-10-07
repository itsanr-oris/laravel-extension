<?php

namespace Foris\LaExtension\Traits\Models;

use Illuminate\Support\Str;

/**
 * Trait ColumnTranslate
 *
 * require laravel/framework >= 5.7
 */
trait ColumnTranslate
{
    /**
     * 获取所有属性信息
     *
     * @return array
     */
    abstract public function getAttributes();

    /**
     * Append attributes to query when building a query.
     *
     * @param  array|string  $attributes
     * @return $this
     */
    abstract public function append($attributes);

    /**
     * 初始化，添加column_translates字段
     */
    public function initializeColumnTranslate()
    {
        $this->append('column_translates');
    }

    /**
     * 获取字段标签翻译
     *
     * @return array
     */
    public function getColumnTranslatesAttribute()
    {
        $translates = [];

        foreach ($this->getAttributes() as $key => $value) {
            $method = Str::camel($key) . 'Translate';
            if (method_exists($this, $method)) {
                $translates[$key] = $this->{$method}($value);
            }
        }

        return $translates;
    }
}