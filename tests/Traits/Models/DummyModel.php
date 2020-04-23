<?php

namespace Foris\LaExtension\Tests\Traits\Models;

use Foris\LaExtension\Traits\Models\SelectOption;
use Foris\LaExtension\Traits\Models\StatusDefinition;
use Foris\LaExtension\Traits\Models\StatusOperation;
use Illuminate\Database\Eloquent\Model;

class DummyModel extends Model implements StatusDefinition
{
    use SelectOption, StatusOperation;
}
