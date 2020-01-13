<?php

namespace Cosmos\Rbac;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Auth;

class RbacServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/rbac.php', 'rbac');
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/rbac.php' => config_path('rbac.php'),
        ], 'config');

        $this->bladeDirectives();
    }

    private function bladeDirectives()
    {
        Blade::if('role', function ($name, $requireAll = false) {
            if (!Auth::check()) return false;
            $names = is_array($name) ? $name : explode(',', $name);
            return Auth::user()->hasRole($names, $requireAll);
        });

        Blade::if('permission', function ($name, $requireAll = false) {
            if (!Auth::check()) return false;
            $names = is_array($name) ? $name : explode(',', $name);
            return Auth::user()->hasPermission($names, $requireAll);
        });
    }
}
