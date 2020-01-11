<?php

namespace Cosmos\Rbac;

use Chelout\RelationshipEvents\Concerns\HasBelongsToManyEvents;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use \Illuminate\Database\Eloquent\Collection;

class Role extends Model
{
    use HasBelongsToManyEvents;

    protected $table = 'roles';
    protected $fillable = ['name'];

    public static function boot(): void
    {
        parent::boot();

        $forgetCache = function ($name, $model) {
            Cache::forget($model->getCacheKey());
        };

        static::belongsToManyAttached($forgetCache);
        static::belongsToManyDetached($forgetCache);
        static::belongsToManySynced($forgetCache);
        static::belongsToManyToggled($forgetCache);
    }

    public function permissions()
    {
        return $this->belongsToMany(config('rbac.models.permission', 'App\Permission'));
    }

    public function users()
    {
        return $this->belongsToMany(config('rbac.models.user', 'App\User'));
    }

    /**
     * Returns cached permissions.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function cachedPermissions(): Collection
    {
        return Cache::remember($this->getCacheKey(), config('rbac.cache.expires', 3600), function () {
            return $this->permissions()->get();
        });
    }

    protected function getCacheKey()
    {
        return config('rbac.cache.key', 'rbac,cache').'.permissionsFor.'.$this->getKey();
    }
}
