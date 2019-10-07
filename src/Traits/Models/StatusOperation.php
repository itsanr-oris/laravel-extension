<?php

namespace Foris\LaExtension\Traits\Models;

/**
 * Trait StatusOperation
 */
trait StatusOperation
{
    /**
     * 状态字段
     *
     * @var string
     */
    protected $statusKey = 'status';

    /**
     * 获取状态字段名称
     *
     * @return string
     */
    public function getStatusKeyName()
    {
        return $this->statusKey;
    }

    /**
     * 状态翻译
     *
     * @param null $status
     * @return array|mixed|null
     */
    public static function statusTranslate($status = null)
    {
        $map = [
            StatusDefinition::STATUS_ENABLE => '启用',
            StatusDefinition::STATUS_DISABLE => '禁用',
        ];

        return $status === null ? $map : ($map[$status] ?? null);
    }
}