<?php

namespace Cosmos\Rbac\Test;

use Orchestra\Testbench\TestCase as Orchestra;
use Illuminate\Database\Schema\Blueprint;

abstract class TestCase extends Orchestra
{
    /** @var \Cosmos\Rbac\Test\User */
    protected $testUser;

    public function setUp(): void
    {
        parent::setUp();

        $this->setUpDatabase($this->app);
        $this->testUser = User::first();
    }

    /**
     * Set up the environment.
     *
     * @param \Illuminate\Foundation\Application $app
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
        // $app['config']->set('view.paths', [__DIR__.'/resources/views']);

        $app['config']->set('rbac.models.role', 'Cosmos\Rbac\Test\Role');
        $app['config']->set('rbac.models.permission', 'Cosmos\Rbac\Test\Permission');
    }

    /**
     * Set up the database.
     *
     * @param \Illuminate\Foundation\Application $app
     */
    protected function setUpDatabase($app)
    {
        $app['db']->connection()->getSchemaBuilder()->create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('email');
            $table->timestamps();
            $table->softDeletes();
        });

        $app['db']->connection()->getSchemaBuilder()->create('roles', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->timestamps();
        });

        $app['db']->connection()->getSchemaBuilder()->create('permissions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->timestamps();
        });

        $app['db']->connection()->getSchemaBuilder()->create('role_user', function (Blueprint $table) {
            $table->integer('role_id');
            $table->integer('user_id');
            $table->foreign('role_id')
                ->references('id')
                ->on('roles')
                ->onDelete('cascade');
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
            $table->primary(['role_id', 'user_id'], 'role_user_primary');
        });

        $app['db']->connection()->getSchemaBuilder()->create('permission_role', function (Blueprint $table) {
            $table->integer('permission_id');
            $table->integer('role_id');
            $table->foreign('permission_id')
                ->references('id')
                ->on('permissions')
                ->onDelete('cascade');
            $table->foreign('role_id')
                ->references('id')
                ->on('roles')
                ->onDelete('cascade');
            $table->primary(['permission_id', 'role_id'], 'permission_role_primary');
        });

        User::create(['email' => 'test@user.com']);

        Role::create(['name' => 'testRole']);
        Role::create(['name' => 'testRole2']);

        Permission::create(['name' => 'edit-articles']);
        Permission::create(['name' => 'edit-news']);
        Permission::create(['name' => 'edit-blog']);
        Permission::create(['name' => 'Edit News']);
    }
}
