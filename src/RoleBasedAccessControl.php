<?php

namespace Cosmos\Rbac;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Chelout\RelationshipEvents\Concerns\HasBelongsToManyEvents;

/**
 * A trait for User model that using RBAC.
 */
trait RoleBasedAccessControl
{
    use HasBelongsToManyEvents;

    public static function bootRoleBasedAccessControl()
    {
        $forgetCache = function ($name, $model) {
            Cache::forget($model->getCacheKey());
        };

        static::belongsToManyAttached($forgetCache);
        static::belongsToManyDetached($forgetCache);
        static::belongsToManySynced($forgetCache);
        static::belongsToManyToggled($forgetCache);
    }

    /**
     * Roles, many-to-many
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(config('rbac.models.role', 'App\Role'));
    }

    /**
     * Checks if the user has a role by its name.
     *
     * @param string|array $name
     * @param boolean $requireAll default: false
     * @return boolean
     */
    public function hasRole($name, $requireAll = false): bool
    {
        if (is_array($name)) {
            foreach ($name as $roleName) {
                $hasRole = $this->hasRole($roleName);

                if ($hasRole && !$requireAll) {
                    return true;
                } elseif (!$hasRole && $requireAll) {
                    return false;
                }
            }

            // If we've made it this far and $requireAll is FALSE, then NONE of the roles were found
            // If we've made it this far and $requireAll is TRUE, then ALL of the roles were found.
            // Return the value of $requireAll;
            return $requireAll;
        } else {
            foreach ($this->cachedRoles() as $role) {
                if ($role->name == $name) return true;
            }
        }

        return false;
    }

    /**
     * Check if user has a permission by its name.
     *
     * @param string|array $permission Permission string or array of permissions.
     * @param bool $requireAll All permissions in the array are required.
     * @return bool
     */
    public function hasPermission($permission, $requireAll = false): bool
    {
        if (is_array($permission)) {
            foreach ($permission as $permName) {
                $hasPerm = $this->hasPermission($permName);

                if ($hasPerm && !$requireAll) {
                    return true;
                } elseif (!$hasPerm && $requireAll) {
                    return false;
                }
            }

            // If we've made it this far and $requireAll is FALSE, then NONE of the perms were found
            // If we've made it this far and $requireAll is TRUE, then ALL of the perms were found.
            // Return the value of $requireAll;
            return $requireAll;
        } else {
            foreach ($this->cachedRoles() as $role) {
                // Validate against the Permission table
                foreach ($role->cachedPermissions() as $perm) {
                    if (Str::is($permission, $perm->name)) return true;
                }
            }
        }

        return false;
    }

    /**
     * Alias to eloquent many-to-many relation's attach() method.
     *
     * @param mixed $role
     */
    public function attachRole($role)
    {
        if(is_object($role)) {
            $role = $role->getKey();
        }

        $this->roles()->attach($role);
    }

    /**
     * Alias to eloquent many-to-many relation's detach() method.
     *
     * @param mixed $role
     */
    public function detachRole($role)
    {
        if (is_object($role)) {
            $role = $role->getKey();
        }

        $this->roles()->detach($role);
    }

    /**
     * Attach multiple roles to a user
     *
     * @param mixed $roles
     */
    public function attachRoles($roles)
    {
        foreach ($roles as $role) {
            $this->attachRole($role);
        }
    }

    /**
     * Detach multiple roles from a user
     *
     * @param mixed $roles
     */
    public function detachRoles($roles=null)
    {
        if (!$roles) $roles = $this->roles()->get();

        foreach ($roles as $role) {
            $this->detachRole($role);
        }
    }

    /**
     * Returns cached roles.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function cachedRoles(): Collection
    {
        return Cache::remember($this->getCacheKey(), config('rbac.cache.expires', 3600), function () {
            return $this->roles()->get();
        });
    }

    protected function getCacheKey()
    {
        return config('rbac.cache.key', 'rbac.cache').'.rolesFor.'.$this->getKey();
    }
}
