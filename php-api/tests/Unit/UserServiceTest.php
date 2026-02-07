<?php

namespace Tests\Unit;

use App\Services\UserService;
use PHPUnit\Framework\TestCase;

class UserServiceTest extends TestCase
{
    /**
     * Test that UserService class exists.
     */
    public function test_user_service_class_exists(): void
    {
        $this->assertTrue(class_exists(UserService::class));
    }

    /**
     * Test service can be instantiated.
     */
    public function test_service_can_be_instantiated(): void
    {
        // Test class has required methods
        $reflection = new \ReflectionClass(UserService::class);

        $this->assertTrue($reflection->hasMethod('getAllUsers'));
        $this->assertTrue($reflection->hasMethod('findUser'));
        $this->assertTrue($reflection->hasMethod('createUser'));
        $this->assertTrue($reflection->hasMethod('updateUser'));
        $this->assertTrue($reflection->hasMethod('deleteUser'));
    }

    /**
     * Test getAllUsers method signature.
     */
    public function test_get_all_users_method_signature(): void
    {
        $reflection = new \ReflectionMethod(UserService::class, 'getAllUsers');
        $this->assertEquals('getAllUsers', $reflection->getName());
    }

    /**
     * Test findUser method signature.
     */
    public function test_find_user_method_signature(): void
    {
        $reflection = new \ReflectionMethod(UserService::class, 'findUser');
        $this->assertEquals('findUser', $reflection->getName());
        $params = $reflection->getParameters();
        $this->assertCount(1, $params);
        $this->assertEquals('int', (string) $params[0]->getType());
    }

    /**
     * Test createUser method signature.
     */
    public function test_create_user_method_signature(): void
    {
        $reflection = new \ReflectionMethod(UserService::class, 'createUser');
        $this->assertEquals('createUser', $reflection->getName());
        $params = $reflection->getParameters();
        $this->assertCount(1, $params);
        $this->assertEquals('array', (string) $params[0]->getType());
    }

    /**
     * Test updateUser method signature.
     */
    public function test_update_user_method_signature(): void
    {
        $reflection = new \ReflectionMethod(UserService::class, 'updateUser');
        $this->assertEquals('updateUser', $reflection->getName());
        $params = $reflection->getParameters();
        $this->assertCount(2, $params);
    }

    /**
     * Test deleteUser method signature.
     */
    public function test_delete_user_method_signature(): void
    {
        $reflection = new \ReflectionMethod(UserService::class, 'deleteUser');
        $this->assertEquals('deleteUser', $reflection->getName());
    }

    /**
     * Test UserService constructor requires repository.
     */
    public function test_service_constructor_requires_repository(): void
    {
        $reflection = new \ReflectionClass(UserService::class);
        $constructor = $reflection->getConstructor();
        $this->assertNotNull($constructor);
        $params = $constructor->getParameters();
        $this->assertCount(1, $params);
        $this->assertEquals('repository', $params[0]->getName());
    }
}