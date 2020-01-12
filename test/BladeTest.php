<?php

namespace Cosmos\Rbac\Test;

use Cosmos\Rbac\Permission;
use Cosmos\Rbac\Role;
use Illuminate\Support\Facades\Artisan;

class BladeTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $blogRead = Permission::create(['name' => 'blog.read']);
        $blogEdit = Permission::create(['name' => 'blog.edit']);
        $blogDelete = Permission::create(['name' => 'blog.delete']);

        $editor = Role::create(['name' => 'editor']);
        $member = Role::create(['name' => 'member']);

        $editor->permissions()->attach([$blogRead, $blogEdit, $blogDelete]);
        $member->permissions()->attach([$blogRead]);
    }

    /** @test */
    public function itShouldReturnsFalseIfNotLoggedIn()
    {
        $role = 'editor';
        $elserole = 'member';

        $this->assertEquals(
            'does not have role',
            $this->renderView('role', compact('role', 'elserole'))
        );
    }

    /** @test */
    public function roleDirectiveShouldReturnsTrueIfCorrectRole()
    {
        $role = 'editor';
        $elserole = 'member';

        auth()->setUser($this->getEditor());

        $this->assertEquals(
            'has role',
            $this->renderView('role', compact('role', 'elserole'))
        );
    }

    /** @test */
    public function roleDirectiveShouldReturnsElseIfItsElseCondition()
    {
        $role = 'editor';
        $elserole = 'member';

        auth()->setUser($this->getMember());

        $this->assertEquals(
            'has else role',
            $this->renderView('role', compact('role', 'elserole'))
        );
    }

    protected function getEditor()
    {
        $role = Role::where('name', 'editor')->first();
        $this->testUser->roles()->attach($role);
        return $this->testUser;
    }

    protected function getMember()
    {
        $role = Role::where('name', 'member')->first();
        $this->testUser->roles()->attach($role);
        return $this->testUser;
    }

    protected function renderView($view, $parameters)
    {
        Artisan::call('view:clear');

        if (is_string($view)) {
            $view = view($view)->with($parameters);
        }

        return trim((string) $view);
    }
}
