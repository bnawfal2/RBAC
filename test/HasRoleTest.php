<?php

namespace Cosmos\Rbac\Test;

use Cosmos\Rbac\Role;

class HasRoleTest extends TestCase
{
    /** @test */
    public function itWorks()
    {
        $this->testUser->attachRole(1);
        $this->assertTrue($this->testUser->hasRole('testRole'));
    }

    /** @test */
    public function itCanAssignAndRemoveARole()
    {
        $role = Role::create(['name' => 'test-user']);

        $this->testUser->attachRole($role);
        $this->assertTrue($this->testUser->hasRole('test-user'));

        $this->testUser->detachRole($role);
        $this->assertFalse($this->testUser->hasRole('test-user'));
    }
}
