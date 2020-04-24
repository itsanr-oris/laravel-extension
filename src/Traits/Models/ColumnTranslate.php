<?php /** @noinspection PhpUndefinedFieldInspection */

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
     * Column translate attribute name
     *
     * @var string
     */
    protected $columnTranslatesAttributeName = 'column_translates';

    /**
     * Translate attributes
     *
     * @var array
     */
    protected $translateColumn = [];

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
     * Append column_translates attribute while initialize
     */
    public function initializeColumnTranslate()
    {
        if (config('app-ext.initialize_model_column_translate', true)) {
            $this->append($this->columnTranslatesAttributeName);
        }
    }

    /**
     * Get column_translate attribute
     *
     * @return array
     */
    public function getColumnTranslatesAttribute()
    {
        if (isset($this->attributes[$this->columnTranslatesAttributeName])) {
            return $this->attributes[$this->columnTranslatesAttributeName];
        }

        $translates = [];
        $attributes = $this->getAttributes();
        $appends = $this->getArrayableAppends();

        foreach ($appends as $column) {
            if (!array_key_exists($column, $attributes)) {
                $attributes[$column] = null;
            }
        }

        foreach ($attributes as $key => $value) {
            if (!empty($this->translateColumn)
                && !in_array($key, $this->translateColumn)) {
                continue;
            }

            $method = Str::camel($key) . 'Translate';
            if (method_exists($this, $method)) {
                $translates[$key] = $this->{$method}($value);
            }
        }

        return $translates;
    }

    /**
     * Append column_translates attribute
     *
     * @param array $attributes
     * @return $this
     */
    public function withTranslate($attributes = [])
    {
        $this->translateColumn = $attributes;
        $this->append($this->columnTranslatesAttributeName);
        return $this;
    }
}
