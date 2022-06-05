<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Genre;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tests\Traits\TestSaves;
use Tests\Traits\TestValidation;

class GenreControllerTest extends TestCase
{
    use DatabaseMigrations, TestValidation, TestSaves;

    private $genre;

    protected function setUp(): void
    {
        parent::setUp();

        $this->genre = Genre::factory()->create();
    }


    public function test_index()
    {
        $response = $this->get(route('genres.index'));

        $response->assertStatus(200)->assertJson([$this->genre->toArray()]);
    }

    public function test_show()
    {
        $response = $this->get(route(
            'genres.show',
            ['genre' => $this->genre->id]
        ));

        $response->assertStatus(200)->assertJson($this->genre->toArray());
    }

    public function test_store()
    {

        $data = ['name' => 'Test'];
        $this->assert_store(
            $data,
            $data +
                [
                    'is_active' => true,
                    'deleted_at' => null
                ]
        );

        $data = [
            'name' => 'Test 2',
            'is_active' => false
        ];
        $this->assert_store(
            $data,
            $data
        );
    }

    public function test_update()
    {
        $this->genre = Genre::factory()->create([
            'is_active' => false,
        ]);

        $data = [
            'name' => 'Another name',
            'is_active' => true,
        ];
        $this->assert_update($data, $data + ['id' => $this->genre->id]);
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
        $this->genre->refresh();

        $response = $this->json('DELETE', route(
            'genres.destroy',
            ['genre' => $this->genre->id]
        ));

        $response
            ->assertStatus(204);

        $this->assertNull(Genre::find($this->genre->id));

        $this->assertNotNull(
            Genre::withTrashed()->find($this->genre->id)
        );
    }


    protected function route_store()
    {
        return route('genres.store');
    }

    protected function route_update()
    {
        return route(
            'genres.update',
            ['genre' => $this->genre->id]
        );
    }

    protected function model()
    {
        return Genre::class;
    }
}
