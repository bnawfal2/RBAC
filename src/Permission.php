<?php

namespace Cosmos\Rbac;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $table = 'permissions';
    protected $fillable = ['name'];

    public function roles()
    {
        return $this->belongsToMany(config('rbac.model.role', 'App\Role'));
    }
}
