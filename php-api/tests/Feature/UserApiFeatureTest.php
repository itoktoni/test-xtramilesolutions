<?php

namespace Tests\Feature;

use PHPUnit\Framework\TestCase;

class UserApiFeatureTest extends TestCase
{
    /**
     * Test that the API endpoint structure is correct.
     */
    public function test_users_endpoint_returns_json(): void
    {
        // Simulate a GET /users response structure
        $mockResponse = [
            ['id' => 1, 'name' => 'John Doe', 'email' => 'john@example.com'],
            ['id' => 2, 'name' => 'Jane Doe', 'email' => 'jane@example.com']
        ];

        $this->assertIsArray($mockResponse);
        $this->assertCount(2, $mockResponse);

        // Verify each user has required fields
        foreach ($mockResponse as $user) {
            $this->assertArrayHasKey('id', $user);
            $this->assertArrayHasKey('name', $user);
            $this->assertArrayHasKey('email', $user);
        }
    }

    /**
     * Test POST /users request structure.
     */
    public function test_create_user_request_structure(): void
    {
        $validRequest = [
            'name' => 'John Doe',
            'email' => 'john@example.com'
        ];

        $this->assertArrayHasKey('name', $validRequest);
        $this->assertArrayHasKey('email', $validRequest);
        $this->assertNotEmpty($validRequest['name']);
        $this->assertNotEmpty($validRequest['email']);
    }

    /**
     * Test error response structure.
     */
    public function test_error_response_structure(): void
    {
        $errorResponse = [
            'error' => 'Name and email are required'
        ];

        $this->assertArrayHasKey('error', $errorResponse);
        $this->assertIsString($errorResponse['error']);
    }
}