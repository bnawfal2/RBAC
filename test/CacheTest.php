<?php

namespace Cosmos\Rbac\Test;

use Illuminate\Support\Facades\Cache;

class CacheTest extends TestCase
{
    /** @test */
    public function itWorks()
    {
        $this->testUser->attachRole(1);
        $this->assertTrue($this->testUser->hasRole('testRole'));

        // if hasRole has been called, user's roles are remembered into cache.
        $cachedRoles = Cache::get('rbac.cache.rolesFor.1');
        $this->assertEquals($cachedRoles[0]->name, 'testRole');

        // if user's role has been detached, cache will clear.
        $this->testUser->detachRole(1);
        $cachedRoles = Cache::get('rbac.cache.rolesFor.1');
        $this->assertEmpty($cachedRoles);
    }
}
