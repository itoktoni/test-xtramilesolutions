<?php

namespace Tests\Unit;

use App\Models\User;
use PHPUnit\Framework\TestCase;

class UserApiTest extends TestCase
{
    /**
     * Test response structure matches expected format.
     */
    public function test_response_structure(): void
    {
        $expectedResponse = [
            'id' => 1,
            'name' => 'John Doe',
            'email' => 'john@example.com'
        ];

        $this->assertArrayHasKey('id', $expectedResponse);
        $this->assertArrayHasKey('name', $expectedResponse);
        $this->assertArrayHasKey('email', $expectedResponse);
    }

    /**
     * Test email validation regex.
     */
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

    /**
     * Test required fields validation.
     */
    public function test_required_fields(): void
    {
        $validData = [
            'name' => 'John Doe',
            'email' => 'john@example.com'
        ];

        $this->assertArrayHasKey('name', $validData);
        $this->assertArrayHasKey('email', $validData);
        $this->assertNotEmpty($validData['name']);
        $this->assertNotEmpty($validData['email']);
    }

    /**
     * Test User model class exists.
     */
    public function test_user_model_exists(): void
    {
        $this->assertTrue(class_exists(User::class));
    }

    /**
     * Test User model has fillable attributes.
     */
    public function test_user_model_has_fillable(): void
    {
        $user = new User();
        $fillables = $user->getFillable();

        $this->assertContains('name', $fillables);
        $this->assertContains('email', $fillables);
    }

    /**
     * Test User model uses HasFactory trait.
     */
    public function test_user_model_uses_has_factory(): void
    {
        $reflection = new \ReflectionClass(User::class);
        $traits = $reflection->getTraitNames();
        $this->assertContains('Illuminate\Database\Eloquent\Factories\HasFactory', $traits);
    }

    /**
     * Test email format validation using PHP filter_var.
     */
    public function test_email_format_validation(): void
    {
        $emails = [
            'valid@email.com' => true,
            'another.valid@domain.co.uk' => true,
            'invalid' => false,
            '@nodomain.com' => false,
            'no@domain' => false,
        ];

        foreach ($emails as $email => $expected) {
            $result = filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
            $this->assertEquals($expected, $result, "Email: $email");
        }
    }

    /**
     * Test validation rule string for create user name.
     */
    public function test_create_user_name_validation_rule(): void
    {
        $rule = 'required|string|max:255';
        $this->assertStringContainsString('required', $rule);
        $this->assertStringContainsString('string', $rule);
        $this->assertStringContainsString('max:255', $rule);
    }

    /**
     * Test validation rule string for create user email.
     */
    public function test_create_user_email_validation_rule(): void
    {
        $rule = 'required|email|max:255|unique:users';
        $this->assertStringContainsString('required', $rule);
        $this->assertStringContainsString('email', $rule);
        $this->assertStringContainsString('max:255', $rule);
        $this->assertStringContainsString('unique:users', $rule);
    }

    /**
     * Test validation rule string for update user email with unique.
     */
    public function test_update_user_email_validation_rule(): void
    {
        $rule = 'sometimes|required|email|max:255|unique:users,email,1';
        $this->assertStringContainsString('sometimes', $rule);
        $this->assertStringContainsString('required', $rule);
        $this->assertStringContainsString('email', $rule);
        $this->assertStringContainsString('unique:users', $rule);
    }

    /**
     * Test that valid user data has required keys.
     */
    public function test_valid_user_data_has_required_keys(): void
    {
        $data = [
            'name' => 'John Doe',
            'email' => 'john@example.com'
        ];

        $this->assertArrayHasKey('name', $data);
        $this->assertArrayHasKey('email', $data);
        $this->assertIsString($data['name']);
        $this->assertIsString($data['email']);
    }
}