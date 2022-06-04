<?php

namespace Tests\Feature\Models;

use App\Models\Category;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CategoryTest extends TestCase
{

    use DatabaseMigrations;

    public function test_list()
    {
        Category::factory(1)->create();

        $categories = Category::all();

        $this->assertCount(1, $categories);

        $categoryKeys = array_keys($categories->first()->getAttributes());

        $expectedKeys = ['id', 'name', 'description', 'is_active', 'created_at', 'updated_at', 'deleted_at'];

        $this->assertEqualsCanonicalizing($expectedKeys, $categoryKeys);
    }

    public function test_create()
    {

        $category = Category::create([
            'name' => 'Test 1'
        ]);

        $category->refresh();

        $this->assertEquals(strlen($category->id), 36);
        $this->assertEquals($category->name, 'Test 1');
        $this->assertNull($category->description);
        $this->assertTrue($category->is_active);

        $category = Category::create([
            'name' => 'Test 2',
            'description' => null
        ]);

        $this->assertNull($category->description);

        $category = Category::create([
            'name' => 'Test 3',
            'description' => 'Test Description'
        ]);

        $this->assertEquals($category->description, 'Test Description');

        $category = Category::create([
            'name' => 'Test 4',
            'is_active' => false
        ]);

        $this->assertFalse($category->is_active);

        $category = Category::create([
            'name' => 'Test 5',
            'is_active' => true
        ]);

        $this->assertTrue($category->is_active);
    }

    public function test_update()
    {


        $category = Category::factory()->create([
            'description' => 'Test Description',
            'is_active' => false
        ]);

        $data = [
            'name' => 'Updated Name',
            'description' => 'Updated Description',
            'is_active' => true
        ];

        $category->update($data);

        foreach ($data as $key => $value) {
            $this->assertEquals($value, $category->{$key});
        }
    }

    public function test_delete()
    {

        $category = Category::factory()->create();

        $category->delete();
        $this->assertNull(Category::find($category->id));

        $category->restore();
        $this->assertNotNull(Category::find($category->id));
    }
}
