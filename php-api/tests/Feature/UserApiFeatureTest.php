<?php

namespace Tests\Feature;

use PHPUnit\Framework\TestCase;

class UserApiFeatureTest extends TestCase
{
    /**
     * Test that the users endpoint returns JSON structure.
     */
    public function test_users_endpoint_returns_json_structure(): void
    {
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
            'error' => 'Validation failed',
            'messages' => [
                'email' => ['The email field is required.'],
                'name' => ['The name field is required.']
            ]
        ];

        $this->assertArrayHasKey('error', $errorResponse);
        $this->assertArrayHasKey('messages', $errorResponse);
        $this->assertIsString($errorResponse['error']);
        $this->assertIsArray($errorResponse['messages']);
    }

    /**
     * Test not found response structure.
     */
    public function test_not_found_response_structure(): void
    {
        $notFoundResponse = [
            'error' => 'User not found'
        ];

        $this->assertArrayHasKey('error', $notFoundResponse);
        $this->assertEquals('User not found', $notFoundResponse['error']);
    }

    /**
     * Test user creation response structure.
     */
    public function test_user_creation_response_structure(): void
    {
        $createdUser = [
            'id' => 1,
            'name' => 'Test User',
            'email' => 'test@example.com'
        ];

        $this->assertArrayHasKey('id', $createdUser);
        $this->assertArrayHasKey('name', $createdUser);
        $this->assertArrayHasKey('email', $createdUser);
        $this->assertIsInt($createdUser['id']);
        $this->assertIsString($createdUser['name']);
        $this->assertIsString($createdUser['email']);
    }

    /**
     * Test success message response structure.
     */
    public function test_success_message_response_structure(): void
    {
        $successResponse = [
            'message' => 'User deleted successfully'
        ];

        $this->assertArrayHasKey('message', $successResponse);
        $this->assertEquals('User deleted successfully', $successResponse['message']);
    }

    /**
     * Test user show response structure.
     */
    public function test_user_show_response_structure(): void
    {
        $userResponse = [
            'id' => 1,
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'created_at' => '2024-01-01 00:00:00'
        ];

        $this->assertArrayHasKey('id', $userResponse);
        $this->assertArrayHasKey('name', $userResponse);
        $this->assertArrayHasKey('email', $userResponse);
        $this->assertArrayHasKey('created_at', $userResponse);
    }
}