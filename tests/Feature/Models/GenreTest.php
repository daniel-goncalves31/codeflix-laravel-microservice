<?php

namespace Tests\Feature\Models;

use App\Models\Genre;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class GenreTest extends TestCase
{

    use DatabaseMigrations;

    public function test_list()
    {
        Genre::factory(1)->create();

        $categories = Genre::all();

        $this->assertCount(1, $categories);

        $genreKeys = array_keys($categories->first()->getAttributes());

        $expectedKeys = ['id', 'name', 'is_active', 'created_at', 'updated_at', 'deleted_at'];

        $this->assertEqualsCanonicalizing($expectedKeys, $genreKeys);
    }

    public function test_create()
    {

        $genre = Genre::create([
            'name' => 'Test 1'
        ]);

        $genre->refresh();

        $this->assertEquals(strlen($genre->id), 36);
        $this->assertEquals($genre->name, 'Test 1');
        $this->assertTrue($genre->is_active);


        $genre = Genre::create([
            'name' => 'Test 2',
            'is_active' => false
        ]);

        $this->assertFalse($genre->is_active);

        $genre = Genre::create([
            'name' => 'Test 3',
            'is_active' => true
        ]);

        $this->assertTrue($genre->is_active);
    }

    public function test_update()
    {


        $genre = Genre::factory()->create([
            'is_active' => false
        ]);

        $data = [
            'name' => 'Updated Name',
            'is_active' => true
        ];

        $genre->update($data);

        foreach ($data as $key => $value) {
            $this->assertEquals($value, $genre->{$key});
        }
    }

    public function test_delete()
    {

        $genre = Genre::factory()->create();

        $genre->delete();
        $this->assertNull(Genre::find($genre->id));

        $genre->restore();
        $this->assertNotNull(Genre::find($genre->id));
    }
}
