<?php

return [
    /**
     * 是否使用扩展的异常处理
     */
    'handle_exception' => true,

    /**
     * 是否展示原始异常信息
     */
    'show_raw_exception_message' => false,

    /**
     * 是否检查数据模型软删除启用情况
     */
    'check_model_soft_delete' => false,

    /**
     * 文件路径
     */
    'file_path' => [
        /**
         * 相对于app目录下，数据model存放的目录路径
         */
        'models' => 'Models',

        /**
         * 相对于app目录下，数据仓库存放的目录路径
         */
        'repositories' => 'Repositories',

        /**
         * 相对于app目录下，业务逻辑处理服务存放的目录路径
         */
        'services' => 'Services',
    ],

    /**
     * 组件配置
     */
    'component' => [
        /**
         * 组件名前缀
         */
        'name_prefix' => 'test',

        /**
         * 扫描目录，相对于app
         */
        'scan_path' => ['Repositories', 'Services', 'Components'],

        /**
         * 是否启用组件Facade别名
         */
        'enable_facade_alias' => true,
    ],

    /**
     * 各组件父类设置
     */
    'parent_class' => [
        /**
         * service父类
         */
        'service' => \Foris\LaExtension\Services\Service::class,

        /**
         * crud service父类
         */
        'crud_service' => \Foris\LaExtension\Services\CrudService::class,

        /**
         * repository父类
         */
        'repository' => \Foris\LaExtension\Repositories\Repository::class,

        /**
         * crud repository父类
         */
        'crud_repository' => \Foris\LaExtension\Repositories\CrudRepository::class,
    ],

    /**
     * resource model配置
     */
    'resource_route' => [
        /**
         * 默认路由配置
         */
        'default' => [
            'index' => '查看{resource_name}列表',
            'create' => '查看{resource_name}创建表单',
            'edit' => '查看{resource_name}编辑表单',
            'store' => '创建{resource_name}信息',
            'show' => '查看{resource_name}详情',
            'update' => '更新{resource_name}信息',
            'destroy' => '删除{resource_name}信息',
        ],

        /**
         * 自定义资源通用路由
         */
        'extra' => [
            'batchDestroy' => [
                'method' => 'delete',
                'route_suffix' => 'delete/batch',
                'name' => '批量删除{resource_name}信息',
            ],

            'enable' => [
                'method' => 'put',
                'route_suffix' => '{resource}/enable',
                'name' => '启用{resource_name}信息',
            ],

            'disable' => [
                'method' => 'put',
                'route_suffix' => '{resource}/disable',
                'name' => '禁用{resource_name}信息',
            ],

            'batchEnable' => [
                'method' => 'put',
                'route_suffix' => 'enable/batch',
                'name' => '批量启用{resource_name}信息',
            ],

            'batchDisable' => [
                'method' => 'put',
                'route_suffix' => 'disable/batch',
                'name' => '批量禁用{resource_name}信息',
            ],

            'selectOptions' => [
                'method' => 'get',
                'route_suffix' => 'select_options',
                'name' => '查看{resource_name}选项信息',
            ],
        ]
    ],

    /**
     * 接口响应通用状态码
     */
    'api_response_code' => [
        /**
         * 响应成功状态码
         */
        'success' => \Foris\LaExtension\Http\Response::CODE_SUCCESS,

        /**
         * 响应失败状态码
         */
        'failure' => \Foris\LaExtension\Http\Response::CODE_FAILURE,

        /**
         * 用户未认证响应状态码
         */
        'unauthorized' => \Symfony\Component\HttpFoundation\Response::HTTP_UNAUTHORIZED,

        /**
         * 操作未授权响应状态码
         */
        'forbidden' => \Symfony\Component\HttpFoundation\Response::HTTP_FORBIDDEN,

        /**
         * 资源不存在响应状态码
         */
        'notFound' => \Symfony\Component\HttpFoundation\Response::HTTP_NOT_FOUND,

        /**
         * 系统异常状态码
         */
        'exception' => \Symfony\Component\HttpFoundation\Response::HTTP_INTERNAL_SERVER_ERROR,

        /**
         * 参数校验不通过状态码
         */
        'paramsValidException' => \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
    ],

    /**
     * 是否自动加载model字段翻译功能
     */
    'initialize_model_column_translate' => true,

    /**
     * 缓存配置
     */
    'cache' => [
        /**
         * 是否启用缓存
         */
        'enable' => false,

        /**
         * 缓存时间
         */
        'ttl' => 3600,

        /**
         * 不启用缓存的环境
         */
        'disable_cache_env' => ['local', 'develop']
    ]
];
