<?php

namespace Foris\LaExtension\Tests\Mock\Stubs;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class DirectDeleteResource
 */
class Resource extends Model
{
    use SoftDeletes;

    /**
     * Guard attribute
     *
     * @var array
     */
    protected $guarded = ['id'];
}
