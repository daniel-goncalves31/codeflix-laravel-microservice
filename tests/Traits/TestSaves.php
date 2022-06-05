<?php

namespace Tests\Traits;

use Exception;
use Illuminate\Testing\TestResponse;

trait TestSaves
{

    protected function assert_store(array $send_data, array $test_database, array $test_json_data = null)
    {
        /** @var TestResponse $response */
        $response = $this->json('POST', $this->route_store(), $send_data);

        if ($response->status() != 201) {
            throw new Exception("Reponse status must be 201, given {$response->status()}:\n{$response->content()}");
        }

        $this->assertInDatabase($response, $test_database);
        $this->assertJsonResponseContent($response, $test_database, $test_json_data);
    }

    protected function assert_update(array $send_data, array $test_database, array $test_json_data = null)
    {
        /** @var TestResponse $response */
        $response = $this->json('PUT', $this->route_update(), $send_data);

        if ($response->status() != 200) {
            throw new Exception("Reponse status must be 200, given {$response->status()}:\n{$response->content()}");
        }

        $this->assertInDatabase($response, $test_database);
        $this->assertJsonResponseContent($response, $test_database, $test_json_data);
    }

    private function assertInDatabase(TestResponse $response, array $test_database)
    {
        $model = $this->model();
        $table = (new $model)->getTable();
        $this->assertDatabaseHas($table, $test_database + ['id' => $response->json('id')]);
    }

    private function assertJsonResponseContent(TestResponse $response, array $test_database, array $test_json_data = null)
    {
        $test_response_data = $test_json_data ?? $test_database;
        $response->assertJsonFragment($test_response_data + ['id' => $response->json('id')]);
    }
}
