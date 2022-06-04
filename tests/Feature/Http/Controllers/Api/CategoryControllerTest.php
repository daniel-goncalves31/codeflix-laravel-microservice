<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Category;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CategoryControllerTest extends TestCase
{
    use DatabaseMigrations;

    public function test_index()

    {
        $categories = Category::factory()->create();
        $response = $this->get(route('categories.index'));

        $response->assertStatus(200)->assertJson([$categories->toArray()]);
    }

    public function test_show()
    {
        $category = Category::factory()->create();
        $response = $this->get(route('categories.show', ['category' => $category->id]));

        $response->assertStatus(200)->assertJson($category->toArray());
    }

    public function test_store()
    {
        $response = $this->json('POST', route('categories.store'), [
            'name' => 'Test 1'
        ]);

        $id = $response->json('id');
        $category = Category::find($id);

        $response
            ->assertStatus(201)
            ->assertJson($category->toArray());

        $this->assertNull($response->json('description'));
        $this->assertTrue($response->json('is_active'));


        $data = [
            'name' => 'Test 2',
            'description' => 'Some random description',
            'is_active' => false
        ];
        $response = $this->json('POST', route('categories.store'), $data);

        $id = $response->json('id');
        $category = Category::find($id);

        $response
            ->assertJsonFragment($data);
    }

    public function test_update()
    {
        $category = Category::factory()->create([
            'is_active' => false,
            'description' => 'Ramdom description'
        ]);

        $data = [
            'name' => 'Another name',
            'is_active' => true,
            'description' => 'Other ramdom description'
        ];
        $response = $this->json('PUT', route(
            'categories.update',
            ['category' => $category->id],

        ), $data);

        $category->refresh();

        $response
            ->assertStatus(200)
            ->assertJson($category->toArray())
            ->assertJsonFragment($data);


        $response = $this->json('PUT', route(
            'categories.update',
            ['category' => $category->id],

        ), [
            'description' => ''
        ]);

        $this->assertNull($response->json('description'));
    }

    public function test_invalidation_data()
    {
        $response = $this->json('POST', route('categories.store'), []);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name'])
            ->assertJsonMissingValidationErrors(['is_active'])
            ->assertJsonFragment([
                trans('validation.required', ['attribute' => 'name'])
            ]);

        $response = $this->json('POST', route('categories.store'), [
            'name' => str_repeat('a', 256),
            'is_active' => 'a'
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'is_active'])
            ->assertJsonFragment([
                trans('validation.max.string', ['attribute' => 'name', 'max' => 255])
            ])
            ->assertJsonFragment([
                trans('validation.boolean', ['attribute' => 'is active'])
            ]);

        $category = Category::factory()->create();

        $response = $this->json('PUT', route('categories.update', ['category' => $category->id]));
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name'])
            ->assertJsonMissingValidationErrors(['is_active'])
            ->assertJsonFragment([
                trans('validation.required', ['attribute' => 'name'])
            ]);

        $response = $this->json('PUT', route('categories.update', ['category' => $category->id]), [
            'name' => str_repeat('a', 256),
            'is_active' => 'a'
        ]);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'is_active'])
            ->assertJsonFragment([
                trans('validation.max.string', ['attribute' => 'name', 'max' => 255])
            ])
            ->assertJsonFragment([
                trans('validation.boolean', ['attribute' => 'is active'])
            ]);
    }

    public function test_destroy()
    {
        $category = Category::factory()->create();
        $category->refresh();

        $response = $this->json('DELETE', route(
            'categories.destroy',
            ['category' => $category->id]
        ));

        $response
            ->assertStatus(204);

        $this->assertNull(Category::find($category->id));

        $this->assertNotNull(
            Category::withTrashed()->find($category->id)
        );
    }
}
