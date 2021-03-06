<?php

namespace App\Models\Traits;
use \Ramsey\Uuid\Uuid as RamseyUuid;

trait Uuid
{
    protected static function booted()
    {
        static::creating(function ($obj) {
            $obj->id = RamseyUuid::uuid4()->toString();
        });
    }
}
