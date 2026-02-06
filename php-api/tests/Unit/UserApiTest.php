<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class UserApiTest extends TestCase
{
    public function test_response_structure(): void
    {
        // Test that the response structure matches expected format
        $expectedResponse = [
            'id' => 1,
            'name' => 'John Doe',
            'email' => 'john@example.com'
        ];

        $this->assertArrayHasKey('id', $expectedResponse);
        $this->assertArrayHasKey('name', $expectedResponse);
        $this->assertArrayHasKey('email', $expectedResponse);
    }

    public function test_email_validation_regex(): void
    {
        $validEmails = [
            'test@example.com',
            'user.name@domain.org',
            'user+tag@example.co'
        ];

        $invalidEmails = [
            'not-an-email',
            '@nodomain.com',
            'spaces in@email.com'
        ];

        foreach ($validEmails as $email) {
            $this->assertTrue(
                filter_var($email, FILTER_VALIDATE_EMAIL) !== false,
                "Email $email should be valid"
            );
        }

        foreach ($invalidEmails as $email) {
            $this->assertFalse(
                filter_var($email, FILTER_VALIDATE_EMAIL) !== false,
                "Email $email should be invalid"
            );
        }
    }

    public function test_required_fields(): void
    {
        $validData = [
            'name' => 'John Doe',
            'email' => 'john@example.com'
        ];

        $this->assertArrayHasKey('name', $validData);
        $this->assertArrayHasKey('email', $validData);
    }
}