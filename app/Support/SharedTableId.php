<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Model;

class SharedTableId
{
    /**
     * @param  class-string<Model>  $modelClass
     */
    public static function next(string $modelClass): int
    {
        /** @var Model $model */
        $model = new $modelClass;

        $latest = $model->newQuery()
            ->select($model->getKeyName())
            ->latest($model->getKeyName())
            ->lockForUpdate()
            ->first();

        return ((int) $latest?->getKey()) + 1;
    }
}
