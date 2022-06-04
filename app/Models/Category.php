<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory, SoftDeletes, Traits\Uuid;

    protected $fillable = ['name', 'description', 'is_active'];
    protected $dates = ['deleted_at'];
    protected $keyType = 'string';
    protected $casts = [
        'is_active' => 'boolean'
    ];
    public $incrementing = false;

}
