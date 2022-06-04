<?php

namespace Tests\Unit;

use App\Models\Category;
use PHPUnit\Framework\TestCase;

class CategoryTest extends TestCase
{

    public function test_fillable()
    {
        $fillable = ['name', 'description', 'is_active'];
        $category = new Category();
        $this->assertEquals($category->getFillable(), $fillable);
    }

    public function test_dates()
    {
        $dates = ['deleted_at', 'created_at', 'updated_at'];
        $category = new Category();
        $this->assertEquals($category->getDates(), $dates);
    }

    public function test_keyType()
    {
        $keyType = 'string';
        $category = new Category();
        $this->assertEquals($category->getKeyType(), $keyType);
    }

    public function test_incrementing()
    {
        $category = new Category();
        $this->assertFalse($category->getIncrementing());
    }

    public function test_casts()
    {
        $casts = ['deleted_at' => 'datetime', 'is_active' => 'boolean'];
        $category = new Category();
        $this->assertEquals($category->getCasts(), $casts);
    }
}
