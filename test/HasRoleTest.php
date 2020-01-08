<?php

namespace Cosmos\Rbac\Test;

class HasRoleTest extends TestCase
{
    /** @test */
    public function itWorks()
    {
        $this->testUser->attachRole(1);

        $this->assertTrue($this->testUser->hasRole('testRole'));
    }
}
