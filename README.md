# Laravel extension

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE)
[![Total Downloads][ico-downloads]][link-downloads]

## 简介

[laravel](https://github.com/laravel/laravel)框架扩展包，可用于快速实现后端项目构建。

## 运行环境

 - PHP >= 7.1.0
 - Laravel >= 5.5.0
 - 推荐开发环境下安装[barryvdh/laravel-ide-helper](https://github.com/barryvdh/laravel-ide-helper)，结合service/repository组件facade进行业务编码

## 安装

通过composer安装

```sh
$ composer require f-oris/laravel-extension
```

publish扩展包配置文件

```sh
$ php artisan vendor:publish --provider="Foris\LaExtension\ServiceProvider"
```

## 使用教程

首先执行资源创建命令

```sh
$ php artisan make:model Resource -mr
```

打开生成的迁移文件，完善资源数据表结构

```php
<?php
//...
Schema::create('resources', function (Blueprint $table) {
    $table->increments('id');
    $table->string('name');
    $table->string('desc');
    $table->tinyInteger('status')->default(\Foris\LaExtension\Traits\Models\StatusDefinition::STATUS_ENABLE);
    $table->softDeletes();
    $table->timestamps();
});
//...
```

执行迁移命令

```sh
$ php artisan migrate
```

打开生成的模型文件，修改如下

```php
<?php
//...
class Resource extends Model
{
    protected $guarded = ['id'];
}
```

打开api路由文件，增加路由

```php
<?php
//...
Route::apiResource('resource', 'ResourceController');
```

执行查看路由命令，检查路由是否正确

```sh
$ php artisan route:list
```

执行请求，检查资源操作是否异常，以查看资源列表为例

```sh
$ curl http://localhost/api/resource
```

请求正常的情况选返回结果如下

```json
{
    "code": 0,
    "data": {
        "current_page": 1,
        "data": [],
        "first_page_url": "http://localhost/api/resource?page_index=1",
        "from": 1,
        "last_page": 1,
        "last_page_url": "http://localhost/api/resource?page_index=1",
        "next_page_url": null,
        "path": "http://localhost/api/resource",
        "per_page": 20,
        "prev_page_url": null,
        "to": 1,
        "total": 1
    },
    "message": "success"
}
```

## 扩展资源操作

以增加更改资源信息状态操作为例，首先修改上一步骤生成的model文件，如下

```php
<?php
// ...
use Foris\LaExtension\Traits\Models\StatusOperation;
use Foris\LaExtension\Traits\Models\StatusDefinition;

class Resource extends Model implements StatusDefinition
{
    use StatusOperation;

    protected $guarded = ['id'];
}
```

修改生成的ResourceRepository文件，如下

```php
<?php
//...
use Foris\LaExtension\Traits\Repositories\StatusOperation;

class ResourceRepository extends CrudRepository
{
    use StatusOperation;
    
    //...
}
```

修改生成的ResourceService文件，如下

```php
<?php
//...
use Foris\LaExtension\Traits\Services\StatusOperation;

class ResourceService extends CrudService
{
    use StatusOperation
    
    //...
}
```

修改生成的ResourceController文件，如下

```php
<?php
//...
use Foris\LaExtension\Traits\Controllers\StatusOperation;

class ResourceController extends Controller
{
    use ResourceOperation, ExtResponse, StatusOperation, SelectOption;
    
    //...
}
```

打开配置文件app-ext.php，找到resource_route配置，修改如下

```php
<?php
return [
    //...
    'resource_route' => [
        /**
         * 默认路由配置
         */
        'default' => [
            /*
            'index' => '查看{resource_name}列表',
            'create' => '查看{resource_name}创建表单',
            'edit' => '查看{resource_name}编辑表单',
            'store' => '创建{resource_name}信息',
            'show' => '查看{resource_name}详情',
            'update' => '更新{resource_name}信息',
            'destroy' => '删除{resource_name}信息',
            */
        ],

        /**
         * 自定义资源通用路由
         */
        'extra' => [
            /*
            'batchDestroy' => [
                'method' => 'delete',
                'route_suffix' => 'delete/batch',
                'name' => '批量删除{resource_name}信息',
            ],
            */
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
            /*
            'selectOptions' => [
                'method' => 'get',
                'route_suffix' => 'select_options',
                'name' => '查看{resource_name}选项信息',
            ],
            */
            
            // 可根据实际情况增加自定义资源通用路由
        ]
    ]
];
```

打开api路由文件，修改路由

```php
<?php
//...
Route::apiResource('resource', 'ResourceController', ['with_extra' => true]);
```

执行创建资源请求，创建一个资源信息

```sh
$ curl -d "name=resource&desc=This+is+a+test+resource" http://localhost/api/resource
```

执行查看资源信息请求，查看资源状态

```sh
$ curl http://localhost/api/resource/1
```

请求成功的话，查看到的资源信息应该如下，此时status值为1，即为启用状态

```json
{
    "code": 0,
    "data": {
        "created_at": "2019-10-07 07:48:25",
        "deleted_at": null,
        "desc": "This is a test resource",
        "id": 1,
        "name": "resource",
        "status": 1,
        "updated_at": "2019-10-07 07:48:25"
    },
    "message": "success"
}
```

执行禁用资源信息请求，禁用该资源信息

```sh
$ curl -X PUT http://lacalhost/api/resource/1/disable
```

再次执行查看资源信息请求，查看资源状态，此时资源状态应更改为0，即禁用状态

## License

MIT License

Copyright (c) 2019-present F.oris <us@f-oris.me>
