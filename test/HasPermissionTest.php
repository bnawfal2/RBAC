<?php

namespace Cosmos\Rbac\Test;

use Cosmos\Rbac\Permission;
use Cosmos\Rbac\Role;

class HasPermissionTest extends TestCase
{
    /** @test */
    public function itCanAssignAndRemoveAPermission()
    {
        $testRole = Role::find(1);
        $editArticles = Permission::find(1);
        $editNews = Permission::find(2);

        $testRole->permissions()->attach($editArticles);
        $this->testUser->attachRole($testRole);

        $this->assertTrue($this->testUser->hasPermission('edit-articles'));
        $this->assertFalse($this->testUser->hasPermission('edit-news'));

        $testRole->permissions()->detach($editArticles);
        $testRole->permissions()->attach($editNews);

        $this->assertFalse($this->testUser->hasPermission('edit-articles'));
        $this->assertTrue($this->testUser->hasPermission('edit-news'));
    }
}
