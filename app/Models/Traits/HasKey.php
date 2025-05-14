<?php

namespace App\Models\Traits;

use Illuminate\Support\Str;

trait HasKey
{
    protected static function bootHasKey() {
        static::creating(function ($model) { $model->key = $model->generateKey(); });
    }

    private function generateKey()
    {
        $modelName = strtolower(substr(class_basename($this), 0, 3));
        $randomString = Str::random(25);
        return $modelName . '_' . $randomString;
    }
}
