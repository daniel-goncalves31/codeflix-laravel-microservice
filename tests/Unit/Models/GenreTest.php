<?php

namespace Tests\Unit;

use App\Models\Genre;
use PHPUnit\Framework\TestCase;

class GenreTest extends TestCase
{

    public function test_fillable()
    {
        $fillable = ['name', 'is_active'];
        $genre = new Genre();
        $this->assertEquals($genre->getFillable(), $fillable);
    }

    public function test_dates()
    {
        $dates = ['deleted_at', 'created_at', 'updated_at'];
        $genre = new Genre();
        $this->assertEquals($genre->getDates(), $dates);
    }

    public function test_keyType()
    {
        $keyType = 'string';
        $genre = new Genre();
        $this->assertEquals($genre->getKeyType(), $keyType);
    }

    public function test_incrementing()
    {
        $genre = new Genre();
        $this->assertFalse($genre->getIncrementing());
    }

    public function test_casts()
    {
        $casts = ['deleted_at' => 'datetime', 'is_active' => 'boolean'];
        $genre = new Genre();
        $this->assertEquals($genre->getCasts(), $casts);
    }
}
