<?php

namespace Spatie\JsonApiPaginate\Test\Models;

use Illuminate\Database\Eloquent\Model;

class TestModel extends Model
{
    protected $guarded = [];

    public function targetModels()
    {
        return $this->hasManyThrough(
            TargetModel::class,
            ThroughModel::class,
        );
    }
}
