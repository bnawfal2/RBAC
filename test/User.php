<?php

namespace Cosmos\Rbac\Test;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Cosmos\Rbac\RoleBasedAccessControl;


class User extends Model implements AuthorizableContract, AuthenticatableContract
{
    use Authenticatable, Authorizable, RoleBasedAccessControl;

    protected $table = 'users';
    protected $fillable = ['email'];
}
