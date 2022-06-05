<?php

declare(strict_types=1);

namespace Tests\Traits;

use Illuminate\Testing\TestResponse;

trait TestValidation
{


    protected function assertInvalidationInStoreAction(
        array $data,
        string $rule,
        $rule_params = []
    ) {
        $response = $this->json('POST', $this->route_store(), $data);
        $fields = array_keys($data);
        $this->assertInvalidationFields($response, $fields, $rule, $rule_params);
    }

    protected function assertInvalidationInUpdateAction(
        array $data,
        string $rule,
        $rule_params = []
    ) {
        $response = $this->json('PUT', $this->route_update(), $data);
        $fields = array_keys($data);
        $this->assertInvalidationFields($response, $fields, $rule, $rule_params);
    }

    protected function assertInvalidationFields(
        TestResponse $response,
        array $fields,
        string $rule,
        array $rule_params = []
    ) {
        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors($fields);

        foreach ($fields as $field) {
            $fields_name = str_replace('_', ' ', $field);
            $response->assertJsonFragment([
                trans("validation.{$rule}", ['attribute' => $fields_name] + $rule_params)
            ]);
        }
    }
}
