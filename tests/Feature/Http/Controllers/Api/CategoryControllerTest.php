<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Category;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tests\Traits\TestSaves;
use Tests\Traits\TestValidation;

class CategoryControllerTest extends TestCase
{
    use DatabaseMigrations, TestValidation, TestSaves;

    private $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->category = Category::factory()->create();
    }


    public function test_index()
    {
        $response = $this->get(route('categories.index'));

        $response->assertStatus(200)->assertJson([$this->category->toArray()]);
    }

    public function test_show()
    {
        $response = $this->get(route(
            'categories.show',
            ['category' => $this->category->id]
        ));

        $response->assertStatus(200)->assertJson($this->category->toArray());
    }

    public function test_store()
    {

        $data = ['name' => 'Test'];
        $this->assert_store(
            $data,
            $data +
                [
                    'description' => null,
                    'is_active' => true,
                    'deleted_at' => null
                ]
        );

        $data = [
            'name' => 'Test 2',
            'description' => 'Some random description',
            'is_active' => false
        ];
        $this->assert_store(
            $data,
            $data
        );
    }

    public function test_update()
    {
        $this->category = Category::factory()->create([
            'is_active' => false,
            'description' => 'Ramdom description'
        ]);

        $data = [
            'name' => 'Another name',
            'is_active' => true,
            'description' => 'Other ramdom description'
        ];
        $this->assert_update($data, $data + ['id' => $this->category->id]);


        $data = [
            'name' => 'Another name',
            'description' => ''
        ];
        $this->assert_update($data, ['name' => 'Another name', 'description' => null]);
    }

    public function test_invalidation_data()
    {
        $data = ['name' => ''];
        $this->assertInvalidationInStoreAction($data, 'required');
        $this->assertInvalidationInUpdateAction($data, 'required');

        $data =  [
            'name' => str_repeat('a', 256)
        ];
        $this->assertInvalidationInStoreAction(
            $data,
            'max.string',
            ['max' => 255]
        );
        $this->assertInvalidationInUpdateAction(
            $data,
            'max.string',
            ['max' => 255]
        );

        $data = ['is_active' => 'a'];
        $this->assertInvalidationInStoreAction($data, 'boolean');
        $this->assertInvalidationInUpdateAction($data, 'boolean');
    }

    public function test_destroy()
    {
        $this->category->refresh();

        $response = $this->json('DELETE', route(
            'categories.destroy',
            ['category' => $this->category->id]
        ));

        $response
            ->assertStatus(204);

        $this->assertNull(Category::find($this->category->id));

        $this->assertNotNull(
            Category::withTrashed()->find($this->category->id)
        );
    }


    protected function route_store()
    {
        return route('categories.store');
    }

    protected function route_update()
    {
        return route(
            'categories.update',
            ['category' => $this->category->id]
        );
    }

    protected function model()
    {
        return Category::class;
    }
}
