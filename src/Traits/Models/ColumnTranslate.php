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
     * Get all of the current attributes on the model.
     *
     * @return array
     */
    abstract public function getAttributes();

    /**
     * Get all of the appendable values that are arrayable.
     *
     * @return array
     */
    abstract public function getArrayableAppends();

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
        $attributes = $this->getAttributes();
        $appends = $this->getArrayableAppends();

        foreach ($appends as $column) {
            if (!array_key_exists($column, $attributes)) {
                $attributes[$column] = null;
            }
        }

        foreach ($attributes as $key => $value) {
            $method = Str::camel($key) . 'Translate';
            if (method_exists($this, $method)) {
                $translates[$key] = $this->{$method}($value);
            }
        }

        return $translates;
    }
}
