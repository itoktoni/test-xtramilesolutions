<?php

namespace Tests\Unit;

use App\Models\User;
use App\Repositories\EloquentUserRepository;
use App\Repositories\UserRepositoryInterface;
use PHPUnit\Framework\TestCase;

class UserRepositoryTest extends TestCase
{
    /**
     * Test that EloquentUserRepository class exists.
     */
    public function test_repository_class_exists(): void
    {
        $this->assertTrue(class_exists(EloquentUserRepository::class));
    }

    /**
     * Test repository implements interface.
     */
    public function test_repository_implements_interface(): void
    {
        $reflection = new \ReflectionClass(EloquentUserRepository::class);
        $interfaces = $reflection->getInterfaceNames();
        $this->assertContains('App\Repositories\UserRepositoryInterface', $interfaces);
    }

    /**
     * Test repository has all required methods.
     */
    public function test_repository_has_required_methods(): void
    {
        $reflection = new \ReflectionClass(EloquentUserRepository::class);

        $this->assertTrue($reflection->hasMethod('getAll'));
        $this->assertTrue($reflection->hasMethod('find'));
        $this->assertTrue($reflection->hasMethod('create'));
        $this->assertTrue($reflection->hasMethod('update'));
        $this->assertTrue($reflection->hasMethod('delete'));
    }

    /**
     * Test getAll method signature.
     */
    public function test_get_all_method_signature(): void
    {
        $reflection = new \ReflectionMethod(EloquentUserRepository::class, 'getAll');
        $this->assertEquals('getAll', $reflection->getName());
        $this->assertTrue($reflection->hasReturnType());
    }

    /**
     * Test find method signature.
     */
    public function test_find_method_signature(): void
    {
        $reflection = new \ReflectionMethod(EloquentUserRepository::class, 'find');
        $params = $reflection->getParameters();
        $this->assertCount(1, $params);
    }

    /**
     * Test create method signature.
     */
    public function test_create_method_signature(): void
    {
        $reflection = new \ReflectionMethod(EloquentUserRepository::class, 'create');
        $params = $reflection->getParameters();
        $this->assertCount(1, $params);
    }

    /**
     * Test update method signature.
     */
    public function test_update_method_signature(): void
    {
        $reflection = new \ReflectionMethod(EloquentUserRepository::class, 'update');
        $params = $reflection->getParameters();
        $this->assertCount(2, $params);
    }

    /**
     * Test delete method signature.
     */
    public function test_delete_method_signature(): void
    {
        $reflection = new \ReflectionMethod(EloquentUserRepository::class, 'delete');
        $params = $reflection->getParameters();
        $this->assertCount(1, $params);
    }
}