<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Genre;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class GenreControllerTest extends TestCase
{
    use DatabaseMigrations;

    public function test_index()

    {
        $genres = Genre::factory()->create();
        $response = $this->get(route('genres.index'));

        $response->assertStatus(200)->assertJson([$genres->toArray()]);
    }

    public function test_show()
    {
        $genre = Genre::factory()->create();
        $response = $this->get(route('genres.show', ['genre' => $genre->id]));

        $response->assertStatus(200)->assertJson($genre->toArray());
    }

    public function test_store()
    {
        $response = $this->json('POST', route('genres.store'), [
            'name' => 'Test 1'
        ]);

        $id = $response->json('id');
        $genre = Genre::find($id);

        $response
            ->assertStatus(201)
            ->assertJson($genre->toArray());

        $this->assertTrue($response->json('is_active'));


        $data = [
            'name' => 'Test 2',
            'is_active' => false
        ];
        $response = $this->json('POST', route('genres.store'), $data);

        $id = $response->json('id');
        $genre = Genre::find($id);

        $response
            ->assertJsonFragment($data);
    }

    public function test_update()
    {
        $genre = Genre::factory()->create([
            'is_active' => false,
        ]);

        $data = [
            'name' => 'Another name',
            'is_active' => true,
        ];
        $response = $this->json('PUT', route(
            'genres.update',
            ['genre' => $genre->id],

        ), $data);

        $genre->refresh();

        $response
            ->assertStatus(200)
            ->assertJson($genre->toArray())
            ->assertJsonFragment($data);
    }

    public function test_invalidation_data()
    {
        $response = $this->json('POST', route('genres.store'), []);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name'])
            ->assertJsonMissingValidationErrors(['is_active'])
            ->assertJsonFragment([
                trans('validation.required', ['attribute' => 'name'])
            ]);

        $response = $this->json('POST', route('genres.store'), [
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

        $genre = Genre::factory()->create();

        $response = $this->json('PUT', route('genres.update', ['genre' => $genre->id]));
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name'])
            ->assertJsonMissingValidationErrors(['is_active'])
            ->assertJsonFragment([
                trans('validation.required', ['attribute' => 'name'])
            ]);

        $response = $this->json('PUT', route('genres.update', ['genre' => $genre->id]), [
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
        $genre = Genre::factory()->create();
        $genre->refresh();

        $response = $this->json('DELETE', route(
            'genres.destroy',
            ['genre' => $genre->id]
        ));

        $response
            ->assertStatus(204);

        $this->assertNull(Genre::find($genre->id));

        $this->assertNotNull(
            Genre::withTrashed()->find($genre->id)
        );
    }
}
