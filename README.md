# Role Based Access Control

A trait for using Role-based access control in the User that a Laravel eloquent model.

## WHAT IS RBAC

Role-based access control (RBAC) is an approach to restricting system access to authorized users. See below for details.

- [What is RBAC](https://www.imperva.com/learn/data-security/role-based-access-control-rbac/)
- [Wikipedia](https://en.wikipedia.org/wiki/Role-based_access_control)

## Table of contents

- [Database Structure](#database-structure)
- [Installation](#installation)
- [Models](#models)
  - [User](#user)
  - [Role](#role)
  - [Permission](#permission)
- [Usage](#usage)
  - [Assigning Roles and Permissions](#assigning-roles-and-permissions)
  - [Using Middleware](#using-middleware)
  - [Using Blade Directives](#using-blade-directives)
- [License](#license)

## Database Structure

``` yaml
users:
    - id INTEGER
    - email STRING
    - etc...

roles:
    - id INTEGER
    - name STRING
    - created_at DATE
    - updated_at DATE

permissions:
    - id INTEGER
    - name STRING
    - created_at DATE
    - updated_at DATE

role_user:
    - role_id INTEGER
    - user_id INTEGER
    - PRIMARY KEY role_id, user_id
    - FOREIGN KEY role_id REFERENCES roles.id ON DELETE CASCADE
    - FOREIGN KEY user_id REFERENCES users.id ON DELETE CASCADE

permission_role:
    - permission_id INTEGER
    - user_id INTEGER
    - PRIMARY KEY permission_id, role_id
    - FOREIGN KEY permission_id REFERENCES permissions.id ON DELETE CASCADE
    - FOREIGN KEY role_id REFERENCES roles.id ON DELETE CASCADE
```

## Installation

Install package via composer

``` sh
composer require cosmos/rbac
```

The service provider will automatically get registered. Or you may manually add the service provider in your `config/app.php` file:

``` php
'providers' => [
    // ...
    Cosmos\Rbac\RbacServiceProvider::class,
];
```

You can add middleware inside your `app/Http/Kernel.php` file:

``` php
protected $routeMiddleware = [
    // ...
    'role' => \Cosmos\Rbac\Middlewares\Role::class,
    'permission' => \Cosmos\Rbac\Middlewares\Permission::class,
];
```

You should publish the `config/rbac.php` config file:

``` sh
php artisan vendor:publish --provider="Cosmos\Rbac\RbacServiceProvider"
```

## Models

### User

Add the `Cosmos\Rbac\RoleBasedAccessControl` trait to your `App\User` model:

``` php
namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Cosmos\Rbac\RoleBasedAccessControl;

class User extends Authenticatable
{
    use RoleBasedAccessControl;

    //
}
```

### Role

Extends the `Cosmos\Rbac\Role` to your `App\Role` model:

``` php
namespace App;

use Cosmos\Rbac\Role as RoleModel;

class Role extends RoleModel
{
    //
}
```

### Permission

Extends the `Cosmos\Rbac\Permission` to your `App\Permission` model:

``` php
namespace App;

use Cosmos\Rbac\Permission as PermissionModel;

class Permission extends PermissionModel
{
    //
}
```

## Usage

### Assigning Roles and Permissions

You can assign `editor` role to the specific user.

``` php
$blogEdit = Permission::create(['name' => 'blog.edit']);
$newsEdit = Permission::create(['name' => 'news.edit']);

// Assign `blog.edit` and `news.edit` permission to `editor` role.
$editor = Role::create(['name' => 'editor']);
$editor->permissions()->attach($blogEdit);
$editor->permissions()->attach($newsEdit);

// Assign `editor` role to the user.
$user = User::find(1);
$user->roles()->attach($editor);

// checking whether the user has roles.
$user->hasRole('editor'); // true

// checking whether the user has permissions.
$user->hasPermission('blog.edit');   // true
$user->hasPermission('blog.delete'); // false

// checking multiple roles or permissions.
$user->hasRole(['editor', 'news-editor']); // true.
$user->hasPermission(['blog.edit', 'blog.delete'], true); // returns false. second parameter is `requireAll`, default is false.
```

And also you can deny roles from the user.

``` php
$editor->permissions()->detach($newsEdit);
$user->hasPermission('news.edit'); // false

$user->roles()->detach($editor);
$user->hasRole('editor'); // false
```

### Using Middleware

Using middleware rules in routes

``` php
Route::group(['middleware' => ['role:admin']], function () {
    //
});

// You can separate multiple roles or permission with a '|' (pipe) character.
Route::group(['middleware' => ['permission:edit articles|publish articles']], function () {
    //
});

Route::get('admin/profile', function () {
    //
})->middleware('role:admin', 'permission:admin.access');
```

Using middleware rules in Controllers

``` php
public function __construct()
{
    $this->middleware('role:super-user');
    // or
    $this->middleware(['role:admin', 'permission:admin.access']);
}
```

### Using Blade Directives

Check for a specific role:

``` php
@role('editor')
    //
@else
    //
@endrole
```

or permissions

``` php
@permission('blog.read,blog.edit')
    //
@endpermission
```

## License

The MIT License
